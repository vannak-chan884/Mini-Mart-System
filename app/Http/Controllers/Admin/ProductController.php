<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\StockHistory;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
        ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
        ->latest()
        ->paginate(8)
        ->withQueryString(); // ← keeps ?search=xxx in pagination links
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'barcode' => 'required|unique:products,barcode',
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cost_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'low_stock_alert' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        // Record initial stock
        if ($validated['stock'] > 0) {
            StockHistory::create([
                'product_id' => $product->id,
                'quantity' => $validated['stock'],
                'type' => 'in',
                'note' => 'Initial stock'
            ]);
        }

        // Activity Logs
        ActivityLogger::created('Product', $product, "Product '{$product->name}' created");

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'barcode' => 'required|unique:products,barcode,' . $product->id,
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cost_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'low_stock_alert' => 'nullable|integer|min:0',
        ]);

        // ✅ Capture ALL current values before update logs
        $old = $product->getAttributes();

        $oldStock = $product->stock;

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        // ✅ Fresh from DB to get all updated values logs
        $product->refresh();
        $new = $product->getAttributes();

        // Track stock difference
        if ($oldStock != $validated['stock']) {

            $difference = $validated['stock'] - $oldStock;

            StockHistory::create([
                'product_id' => $product->id,
                'quantity' => abs($difference),
                'type' => $difference > 0 ? 'in' : 'out',
                'note' => 'Manual stock adjustment'
            ]);
        }

        // ✅ Compare and collect only what changed logs
        $changes = [];
        $skip = ['updated_at', 'created_at']; // ignore timestamp fields

        foreach ($new as $key => $value) {
            if (in_array($key, $skip))
                continue;
            if (isset($old[$key]) && (string) $old[$key] !== (string) $value) {
                $changes[$key] = [
                    'from' => $old[$key],
                    'to' => $value,
                ];
            }
        }

        ActivityLogger::updated('Product', $product, $changes);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $name = $product->name;

        if (auth()->user()->role === 'cashier') {
            // Cashier — soft delete (move to trash)
            $product->delete(); // sets deleted_at, record stays in DB
            ActivityLogger::log('deleted', 'Product', "Product '{$name}' moved to trash by cashier", $product);
            return redirect()->route('admin.products.index')
                ->with('success', "'{$name}' moved to trash.");
        }

        // Admin — hard delete permanently
        $product->forceDelete();
        ActivityLogger::deleted('Product', "Product '{$name}' permanently deleted", $product);
        return redirect()->route('admin.products.index')
            ->with('success', "'{$name}' permanently deleted.");
    }

    // Trash Page controller methods
    // Show trashed products
    public function trash(): \Illuminate\View\View
    {
        $products = Product::onlyTrashed()->latest('deleted_at')->paginate(15);
        return view('admin.products.trash', compact('products'));
    }

    // Restore a trashed product
    public function restore(int $id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore(); // clears deleted_at
        ActivityLogger::log('updated', 'Product', "Product '{$product->name}' restored from trash", $product);
        return redirect()->route('admin.products.trash')
            ->with('success', "'{$product->name}' restored successfully.");
    }

    // Permanently delete from trash (admin only)
    public function forceDestroy(int $id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $name = $product->name;
        $product->forceDelete();
        ActivityLogger::deleted('Product', "Product '{$name}' permanently deleted");
        return redirect()->route('admin.products.trash')
            ->with('success', "'{$name}' permanently deleted.");
    }
}
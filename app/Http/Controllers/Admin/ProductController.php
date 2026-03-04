<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->get();
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
            'category_id'     => 'required|exists:categories,id',
            'barcode'         => 'required|unique:products,barcode',
            'name'            => 'required|string|max:255',
            'image'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cost_price'      => 'required|numeric|min:0',
            'sell_price'      => 'required|numeric|min:0',
            'stock'           => 'required|integer|min:0',
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
                'quantity'   => $validated['stock'],
                'type'       => 'in',
                'note'       => 'Initial stock'
            ]);
        }

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
            'category_id'     => 'required|exists:categories,id',
            'barcode'         => 'required|unique:products,barcode,' . $product->id,
            'name'            => 'required|string|max:255',
            'image'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cost_price'      => 'required|numeric|min:0',
            'sell_price'      => 'required|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'low_stock_alert' => 'nullable|integer|min:0',
        ]);

        $oldStock = $product->stock;

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        // Track stock difference
        if ($oldStock != $validated['stock']) {

            $difference = $validated['stock'] - $oldStock;

            StockHistory::create([
                'product_id' => $product->id,
                'quantity'   => abs($difference),
                'type'       => $difference > 0 ? 'in' : 'out',
                'note'       => 'Manual stock adjustment'
            ]);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
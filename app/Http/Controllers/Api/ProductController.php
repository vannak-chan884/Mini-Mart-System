<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends ApiController
{
    // GET /api/products
    public function index(Request $request)
    {
        $query = Product::with('category')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('barcode', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('in_stock')) {
            $query->where('stock', '>', 0);
        }

        $perPage  = $request->get('per_page', 15);
        $products = $query->paginate($perPage);

        return response()->json([
            'data'       => $products->items(),
            'pagination' => [
                'total'        => $products->total(),
                'per_page'     => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
            ],
        ]);
    }

    // GET /api/products/{id}
    public function show(Product $product)
    {
        $product->load('category');

        return response()->json([
            'data' => array_merge($product->toArray(), [
                'image_url'    => $product->image ? Storage::url($product->image) : null,
                'stock_history'=> $product->stockHistories()->latest()->limit(10)->get(),
            ]),
        ]);
    }

    // POST /api/products
    public function store(Request $request)
    {
        $this->requirePermission($request, 'products.create');

        $validated = $request->validate([
            'category_id'    => 'required|exists:categories,id',
            'barcode'        => 'required|unique:products,barcode',
            'name'           => 'required|string|max:255',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cost_price'     => 'required|numeric|min:0',
            'sell_price'     => 'required|numeric|min:0',
            'stock'          => 'required|integer|min:0',
            'low_stock_alert'=> 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        if ($validated['stock'] > 0) {
            StockHistory::create([
                'product_id' => $product->id,
                'quantity'   => $validated['stock'],
                'type'       => 'in',
                'note'       => 'Initial stock via API',
            ]);
        }

        return response()->json(['success' => true, 'data' => $product], 201);
    }

    // PUT /api/products/{id}
    public function update(Request $request, Product $product)
    {
        $this->requirePermission($request, 'products.edit');

        $validated = $request->validate([
            'category_id'    => 'required|exists:categories,id',
            'barcode'        => 'required|unique:products,barcode,' . $product->id,
            'name'           => 'required|string|max:255',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cost_price'     => 'required|numeric|min:0',
            'sell_price'     => 'required|numeric|min:0',
            'stock'          => 'required|integer|min:0',
            'low_stock_alert'=> 'nullable|integer|min:0',
        ]);

        $oldStock = $product->stock;

        if ($request->hasFile('image')) {
            if ($product->image) Storage::disk('public')->delete($product->image);
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        if ($oldStock != $validated['stock']) {
            $diff = $validated['stock'] - $oldStock;
            StockHistory::create([
                'product_id' => $product->id,
                'quantity'   => abs($diff),
                'type'       => $diff > 0 ? 'in' : 'out',
                'note'       => 'Manual adjustment via API',
            ]);
        }

        return response()->json(['success' => true, 'data' => $product->fresh('category')]);
    }

    // DELETE /api/products/{id}
    public function destroy(Request $request, Product $product)
    {
        $this->requirePermission($request, 'products.delete');
        $product->delete();
        return response()->json(['success' => true, 'message' => 'Product deleted.']);
    }

    // GET /api/products/search?q=...
    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $products = Product::with('category')
            ->where('stock', '>', 0)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('barcode', $q); // exact barcode match for scanner
            })
            ->limit(20)
            ->get();

        return response()->json(['data' => $products]);
    }

    // GET /api/products/low-stock
    public function lowStock(Request $request)
    {
        $products = Product::with('category')
            ->whereColumn('stock', '<=', 'low_stock_alert')
            ->where('low_stock_alert', '>', 0)
            ->latest()
            ->get();

        return response()->json(['data' => $products, 'count' => $products->count()]);
    }

    // ── Helper ────────────────────────────────────────────────────
    private function requirePermission(Request $request, string $permission): void
    {
        if (!$request->user()->canDo($permission)) {
            abort(response()->json(['error' => 'Permission denied.'], 403));
        }
    }
}
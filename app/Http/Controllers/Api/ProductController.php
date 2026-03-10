<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends ApiController
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $action = $request->route()->getActionMethod();

            $map = [
                'store'   => 'products.create',
                'update'  => 'products.edit',
                'destroy' => 'products.delete',
            ];

            if (isset($map[$action]) && !$request->user()->canDo($map[$action])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission denied.',
                ], 403);
            }

            return $next($request);
        });
    }

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

        return $this->paginated(ProductResource::collection($products));
    }

    // GET /api/products/{id}
    public function show(Product $product)
    {
        $product->load('category');
        return $this->success(new ProductResource($product));
    }

    // POST /api/products
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

        if ($validated['stock'] > 0) {
            StockHistory::create([
                'product_id' => $product->id,
                'quantity'   => $validated['stock'],
                'type'       => 'in',
                'note'       => 'Initial stock via API',
            ]);
        }

        return $this->created(new ProductResource($product->load('category')), 'Product created successfully.');
    }

    // PUT /api/products/{id}
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

        if ($oldStock != $validated['stock']) {
            $diff = $validated['stock'] - $oldStock;
            StockHistory::create([
                'product_id' => $product->id,
                'quantity'   => abs($diff),
                'type'       => $diff > 0 ? 'in' : 'out',
                'note'       => 'Manual adjustment via API',
            ]);
        }

        return $this->success(new ProductResource($product->fresh('category')), 'Product updated successfully.');
    }

    // DELETE /api/products/{id}
    public function destroy(Product $product)
    {
        $product->delete();
        return $this->success(null, 'Product deleted successfully.');
    }

    // GET /api/products/search?q=...
    public function search(Request $request)
    {
        $q = $request->get('q', '');

        $products = Product::with('category')
            ->where('stock', '>', 0)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('barcode', $q);
            })
            ->limit(20)
            ->get();

        return $this->success(ProductResource::collection($products));
    }

    // GET /api/products/low-stock
    public function lowStock()
    {
        $products = Product::with('category')
            ->whereColumn('stock', '<=', 'low_stock_alert')
            ->where('low_stock_alert', '>', 0)
            ->latest()
            ->get();

        return $this->success(ProductResource::collection($products), 'Low stock products.');
    }
}
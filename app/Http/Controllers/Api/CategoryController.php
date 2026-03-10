<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends ApiController
{
    // GET /api/categories
    public function index()
    {
        $categories = Category::withCount('products')->latest()->get();
        return response()->json(['data' => $categories]);
    }

    // GET /api/categories/{id}
    public function show(Category $category)
    {
        $category->loadCount('products');
        return response()->json(['data' => $category]);
    }

    // POST /api/categories
    public function store(Request $request)
    {
        $this->requirePermission($request, 'categories.view');

        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($validated);
        return response()->json(['success' => true, 'data' => $category], 201);
    }

    // PUT /api/categories/{id}
    public function update(Request $request, Category $category)
    {
        $this->requirePermission($request, 'categories.view');

        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($validated);
        return response()->json(['success' => true, 'data' => $category]);
    }

    // DELETE /api/categories/{id}
    public function destroy(Request $request, Category $category)
    {
        $this->requirePermission($request, 'categories.view');
        $category->delete();
        return response()->json(['success' => true, 'message' => 'Category deleted.']);
    }

    private function requirePermission(Request $request, string $permission): void
    {
        if (!$request->user()->canDo($permission)) {
            abort(response()->json(['error' => 'Permission denied.'], 403));
        }
    }
}
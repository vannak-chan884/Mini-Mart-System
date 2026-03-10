<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends ApiController
{
    // GET /api/categories
    public function index()
    {
        $categories = Category::withCount('products')->latest()->get();
        return $this->success(CategoryResource::collection($categories));
    }

    // GET /api/categories/{id}
    public function show(Category $category)
    {
        $category->loadCount('products');
        return $this->success(new CategoryResource($category));
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
        return $this->created(new CategoryResource($category), 'Category created.');
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
        return $this->success(new CategoryResource($category), 'Category updated.');
    }

    // DELETE /api/categories/{id}
    public function destroy(Request $request, Category $category)
    {
        $this->requirePermission($request, 'categories.view');
        $category->delete();
        return $this->success(null, 'Category deleted.');
    }

    private function requirePermission(Request $request, string $permission): void
    {
        if (!$request->user()->canDo($permission)) {
            abort(response()->json(['error' => 'Permission denied.'], 403));
        }
    }
}
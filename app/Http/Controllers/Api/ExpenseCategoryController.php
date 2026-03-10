<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends ApiController
{
    public function index(Request $request)
    {
        $categories = ExpenseCategory::withCount('expenses')->latest()->get();
        return response()->json(['data' => $categories]);
    }

    public function show(Request $request, ExpenseCategory $expenseCategory)
    {
        $expenseCategory->loadCount('expenses');
        return response()->json(['data' => $expenseCategory]);
    }

    public function store(Request $request)
    {
        $this->requirePermission($request, 'expense_categories.view');
        $validated = $request->validate(['name' => 'required|string|max:255|unique:expense_categories,name']);
        $category  = ExpenseCategory::create($validated);
        return response()->json(['success' => true, 'data' => $category], 201);
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $this->requirePermission($request, 'expense_categories.view');
        $validated = $request->validate(['name' => 'required|string|max:255|unique:expense_categories,name,' . $expenseCategory->id]);
        $expenseCategory->update($validated);
        return response()->json(['success' => true, 'data' => $expenseCategory]);
    }

    public function destroy(Request $request, ExpenseCategory $expenseCategory)
    {
        $this->requirePermission($request, 'expense_categories.view');
        $expenseCategory->delete();
        return response()->json(['success' => true]);
    }

    private function requirePermission(Request $request, string $permission): void
    {
        if (!$request->user()->canDo($permission)) {
            abort(response()->json(['error' => 'Permission denied.'], 403));
        }
    }
}
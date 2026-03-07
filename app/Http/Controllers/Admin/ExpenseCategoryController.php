<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::latest()->get();
        return view('admin.expense-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.expense-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        ExpenseCategory::create([
            'name' => $request->name
        ]);

        return redirect()->route('admin.expense-categories.index')
            ->with('success', 'Category created successfully');
    }

    public function edit(ExpenseCategory $expense_category)
    {
        return view('admin.expense-categories.edit', compact('expense_category'));
    }

    public function update(Request $request, ExpenseCategory $expense_category)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $expense_category->update($request->all());

        return redirect()->route('admin.expense-categories.index')
            ->with('success', 'Category updated successfully');
    }

    public function destroy(ExpenseCategory $expense_category)
    {
        $expense_category->delete();

        return redirect()->route('admin.expense-categories.index')
            ->with('success', 'Category deleted');
    }
}

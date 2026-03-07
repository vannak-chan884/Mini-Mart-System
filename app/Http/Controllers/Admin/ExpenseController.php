<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::latest()->paginate(10);

        $todayExpense = Expense::whereDate('date', Carbon::today())->sum('amount');

        $monthExpense = Expense::whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->sum('amount');

        $totalExpense = Expense::sum('amount');

        return view('admin.expenses.index', compact(
            'expenses',
            'todayExpense',
            'monthExpense',
            'totalExpense'));
    }

    public function create()
    {
        $categories = ExpenseCategory::all();

        return view('admin.expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'title' => 'required',
            'amount' => 'required|numeric',
            'date' => 'required|date',
        ]);

        Expense::create($request->all());

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense added successfully');
    }

    public function edit(Expense $expense)
    {
        $categories = ExpenseCategory::all();

        return view('admin.expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'title' => 'required',
            'amount' => 'required|numeric',
            'date' => 'required|date',
        ]);

        $expense->update($request->all());

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense updated successfully');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense deleted');
    }

    // Monthly Report
    public function monthlyReport(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');

        $expenses = Expense::with('category')
            ->whereMonth('date', date('m', strtotime($month)))
            ->whereYear('date', date('Y', strtotime($month)))
            ->get();

        $total = $expenses->sum('amount');

        return view('admin.expenses.monthly_report', compact('expenses', 'month', 'total'));
    }
}
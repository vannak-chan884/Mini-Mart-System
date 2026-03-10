<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends ApiController
{
    // GET /api/expenses
    public function index(Request $request)
    {
        $this->requirePermission($request, 'expenses.view');

        $query = Expense::with('category', 'user')->latest();

        if ($request->filled('search')) {
            $query->where('description', 'like', "%{$request->search}%");
        }

        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $expenses = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data'       => $expenses->items(),
            'pagination' => [
                'total'        => $expenses->total(),
                'per_page'     => $expenses->perPage(),
                'current_page' => $expenses->currentPage(),
                'last_page'    => $expenses->lastPage(),
            ],
        ]);
    }

    // GET /api/expenses/{id}
    public function show(Request $request, Expense $expense)
    {
        $this->requirePermission($request, 'expenses.view');
        $expense->load('category', 'user');
        return response()->json(['data' => $expense]);
    }

    // POST /api/expenses
    public function store(Request $request)
    {
        $this->requirePermission($request, 'expenses.create');

        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'description'         => 'required|string|max:255',
            'amount'              => 'required|numeric|min:0',
            'expense_date'        => 'required|date',
            'note'                => 'nullable|string',
        ]);

        $validated['user_id'] = $request->user()->id;
        $expense = Expense::create($validated);

        return response()->json(['success' => true, 'data' => $expense], 201);
    }

    // PUT /api/expenses/{id}
    public function update(Request $request, Expense $expense)
    {
        $this->requirePermission($request, 'expenses.edit');

        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'description'         => 'required|string|max:255',
            'amount'              => 'required|numeric|min:0',
            'expense_date'        => 'required|date',
            'note'                => 'nullable|string',
        ]);

        $expense->update($validated);
        return response()->json(['success' => true, 'data' => $expense->fresh('category')]);
    }

    // DELETE /api/expenses/{id}
    public function destroy(Request $request, Expense $expense)
    {
        $this->requirePermission($request, 'expenses.delete');
        $expense->delete();
        return response()->json(['success' => true, 'message' => 'Expense deleted.']);
    }

    // GET /api/expenses/summary
    public function summary(Request $request)
    {
        $this->requirePermission($request, 'expenses.view');

        $byCategory = Expense::with('category')
            ->select('expense_category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('expense_category_id')
            ->get();

        return response()->json([
            'total_all_time'   => (float) Expense::sum('amount'),
            'total_this_month' => (float) Expense::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)->sum('amount'),
            'by_category'      => $byCategory,
        ]);
    }

    // GET /api/expenses/monthly
    public function monthly(Request $request)
    {
        $this->requirePermission($request, 'expenses.view');

        $year = $request->get('year', now()->year);

        $monthly = Expense::whereYear('created_at', $year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json(['year' => $year, 'data' => $monthly]);
    }

    private function requirePermission(Request $request, string $permission): void
    {
        if (!$request->user()->canDo($permission)) {
            abort(response()->json(['error' => 'Permission denied.'], 403));
        }
    }
}
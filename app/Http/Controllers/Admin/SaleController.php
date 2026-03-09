<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        // ✅ Block direct URL access if no permission
        if (!auth()->user()->hasPermission('sales.view')) {
            abort(403, 'You do not have permission to view sales.');
        }
        
        $query = Sale::with('items.product')->latest();

        // Search by invoice number
        if ($request->filled('search')) {
            $query->where('invoice_no', 'like', '%' . $request->search . '%');
        }

        // Filter by payment method
        if ($request->filled('payment')) {
            $query->where('payment_method', $request->payment);
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $sales = $query->paginate(6)->withQueryString();

        // Summary stats
        $stats = [
            'total_sales'   => Sale::count(),
            'total_revenue' => Sale::sum('total_amount'),
            'cash_revenue'  => Sale::where('payment_method', 'cash')->sum('total_amount'),
            'khqr_revenue'  => Sale::whereIn('payment_method', ['khqr_usd', 'khqr_khr'])->sum('total_amount'),
            'today_revenue' => Sale::whereDate('created_at', today())->sum('total_amount'),
            'today_count'   => Sale::whereDate('created_at', today())->count(),
        ];

        return view('admin.sales.index', compact('sales', 'stats'));
    }

    public function show(Sale $sale)
    {
        if (!auth()->user()->hasPermission('sales.view')) {
            abort(403);
        }
        
        $sale->load('items.product');
        return view('admin.sales.show', compact('sale'));
    }

    public function destroy(Sale $sale)
    {
        if (!auth()->user()->hasPermission('sales.delete')) {
            abort(403);
        }
        
        $sale->items()->delete();
        $sale->delete();

        return redirect()->route('admin.sales.index')
            ->with('success', 'Sale deleted successfully.');
    }
}
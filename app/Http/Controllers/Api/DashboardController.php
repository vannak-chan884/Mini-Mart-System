<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends ApiController
{
    // GET /api/dashboard
    public function index(Request $request)
    {
        $today     = now()->toDateString();
        $thisMonth = now()->format('Y-m');

        // ── Sales stats ───────────────────────────────────────────
        $todaySales = Sale::whereDate('created_at', $today);
        $monthSales = Sale::whereYear('created_at', now()->year)
                          ->whereMonth('created_at', now()->month);

        // ── Revenue by payment method (all time) ──────────────────
        $revenueByMethod = Sale::select('payment_method', DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->get()
            ->mapWithKeys(fn($r) => [$r->payment_method => (float) $r->total]);

        // ── Low stock products ────────────────────────────────────
        $lowStock = Product::whereColumn('stock', '<=', 'low_stock_alert')
            ->where('low_stock_alert', '>', 0)
            ->count();

        // ── Sales last 7 days ─────────────────────────────────────
        $last7Days = Sale::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ── Top 5 selling products (this month) ───────────────────
        $topProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereYear('sales.created_at', now()->year)
            ->whereMonth('sales.created_at', now()->month)
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_qty'),
                DB::raw('SUM(sale_items.total) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // ── Expenses this month ───────────────────────────────────
        $monthExpenses = Expense::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        return response()->json([
            'today' => [
                'sales'   => $todaySales->count(),
                'revenue' => (float) $todaySales->sum('total_amount'),
            ],
            'this_month' => [
                'sales'    => $monthSales->count(),
                'revenue'  => (float) $monthSales->sum('total_amount'),
                'expenses' => (float) $monthExpenses,
                'profit'   => (float) $monthSales->sum('total_amount') - (float) $monthExpenses,
            ],
            'total' => [
                'sales'   => Sale::count(),
                'revenue' => (float) Sale::sum('total_amount'),
            ],
            'revenue_by_payment' => $revenueByMethod,
            'low_stock_count'    => $lowStock,
            'sales_last_7_days'  => $last7Days,
            'top_products'       => $topProducts,
        ]);
    }
}
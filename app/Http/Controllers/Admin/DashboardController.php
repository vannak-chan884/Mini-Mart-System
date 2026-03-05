<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today('Asia/Phnom_Penh');
        $thirtyDaysAgo = Carbon::now('Asia/Phnom_Penh')->subDays(30);

        // ── Today stats ──────────────────────────────
        $todaySales = Sale::whereDate('created_at', $today)->count();
        $todayRevenue = Sale::whereDate('created_at', $today)->sum('total_amount');

        // ── All-time stats ───────────────────────────
        $totalSales = Sale::count();
        $totalRevenue = Sale::sum('total_amount');

        // ── Product stats ────────────────────────────
        $totalProducts = Product::count();

        // ── Low stock ────────────────────────────────
        $lowStockProducts = Product::whereColumn('stock', '<=', 'low_stock_alert')
            ->orderBy('stock')
            ->take(8)
            ->get();
        $lowStockCount = $lowStockProducts->count();

        // ── Recent 8 sales ───────────────────────────
        $recentSales = Sale::latest()->take(8)->get();

        // ── Top 5 products (last 30 days) ────────────
        $topProducts = SaleItem::with('product')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(quantity * price) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // ── Payment breakdown (all time) ─────────────
        $paymentBreakdown = Sale::selectRaw('payment_method, SUM(total_amount) as revenue')
            ->groupBy('payment_method')
            ->pluck('revenue', 'payment_method')
            ->toArray();

        return view('admin.dashboard.index', compact(
            'todaySales',
            'todayRevenue',
            'totalSales',
            'totalRevenue',
            'totalProducts',
            'lowStockProducts',
            'lowStockCount',
            'recentSales',
            'topProducts',
            'paymentBreakdown',
        ));
    }
}
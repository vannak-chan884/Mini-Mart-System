<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->canDo('sales.view')) {
            abort(403, 'You do not have permission to view sales.');
        }

        $query = Sale::with('items.product', 'user', 'confirmedBy')->latest();

        if ($request->filled('search'))          $query->where('invoice_no', 'like', '%' . $request->search . '%');
        if ($request->filled('payment'))         $query->where('payment_method', $request->payment);
        if ($request->filled('status'))          $query->where('status', $request->status);
        if ($request->filled('delivery_status')) $query->where('delivery_status', $request->delivery_status);
        if ($request->filled('from'))            $query->whereDate('created_at', '>=', $request->from);
        if ($request->filled('to'))              $query->whereDate('created_at', '<=', $request->to);

        $sales = $query->paginate(6)->withQueryString();

        $stats = [
            'total_sales'    => Sale::count(),
            'total_revenue'  => Sale::sum('total_amount'),
            'cash_revenue'   => Sale::where('payment_method', 'cash')->sum('total_amount'),
            'khqr_revenue'   => Sale::whereIn('payment_method', ['khqr', 'khqr_usd', 'khqr_khr'])->sum('total_amount'),
            'today_revenue'  => Sale::whereDate('created_at', today())->sum('total_amount'),
            'today_count'    => Sale::whereDate('created_at', today())->count(),
        ];

        // Pending delivery across ALL order types
        $pendingDeliveryCount = Sale::where('delivery_status', 'pending')
            ->where('status', '!=', 'cancelled')
            ->count();

        return view('admin.sales.index', compact('sales', 'stats', 'pendingDeliveryCount'));
    }

    public function show(Sale $sale)
    {
        if (!auth()->user()->canDo('sales.view')) abort(403);

        $sale->load('items.product', 'user', 'confirmedBy');
        return view('admin.sales.show', compact('sale'));
    }

    // PATCH /admin/sales/{sale}/status — payment status (cash only)
    public function updateStatus(Request $request, Sale $sale)
    {
        if (!auth()->user()->canDo('sales.view')) abort(403);

        $request->validate([
            'status'            => 'required|in:pending,delivering,paid,cancelled',
            'payment_reference' => 'nullable|string|max:255',
            'payment_proof'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $data = ['status' => $request->status];

        if ($request->filled('payment_reference')) {
            $data['payment_reference'] = $request->payment_reference;
        }

        if ($request->hasFile('payment_proof')) {
            if ($sale->payment_proof) Storage::disk('public')->delete($sale->payment_proof);
            $data['payment_proof'] = $request->file('payment_proof')->store('payment_proofs', 'public');
        }

        if ($request->status === 'paid') {
            $data['confirmed_by'] = auth()->id();
            $data['confirmed_at'] = now();
        }

        if (in_array($request->status, ['pending', 'delivering', 'cancelled'])) {
            $data['confirmed_by'] = null;
            $data['confirmed_at'] = null;
        }

        $sale->update($data);
        $sale->load('user');

        try {
            app(TelegramService::class)->notifyStatusUpdate($sale, auth()->user()->name);
        } catch (\Exception $e) {
            \Log::warning('Telegram notification failed: ' . $e->getMessage());
        }

        return redirect()->route('admin.sales.show', $sale)
            ->with('success', "Payment status updated to " . ucfirst($request->status) . '.');
    }

    // PATCH /admin/sales/{sale}/delivery — delivery status (ALL orders)
    public function updateDelivery(Request $request, Sale $sale)
    {
        if (!auth()->user()->canDo('sales.view')) abort(403);

        $request->validate([
            'delivery_status' => 'required|in:pending,delivering,delivered',
        ]);

        $sale->update(['delivery_status' => $request->delivery_status]);
        $sale->load('user');

        try {
            app(TelegramService::class)->notifyDeliveryUpdate($sale, auth()->user()->name);
        } catch (\Exception $e) {
            \Log::warning('Telegram delivery notification failed: ' . $e->getMessage());
        }

        return redirect()->route('admin.sales.show', $sale)
            ->with('success', "Delivery status updated to " . ucfirst($request->delivery_status) . '.');
    }

    public function destroy(Sale $sale)
    {
        if (!auth()->user()->canDo('sales.delete')) abort(403);

        if ($sale->payment_proof) Storage::disk('public')->delete($sale->payment_proof);
        $sale->items()->delete();
        $sale->delete();

        return redirect()->route('admin.sales.index')
            ->with('success', 'Sale deleted successfully.');
    }
}
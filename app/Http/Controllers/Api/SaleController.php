<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends ApiController
{
    // GET /api/sales
    public function index(Request $request)
    {
        $this->requirePermission($request, 'sales.view');

        $query = Sale::with('items.product', 'user')->latest();

        if ($request->filled('search')) {
            $query->where('invoice_no', 'like', "%{$request->search}%");
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $perPage = $request->get('per_page', 15);
        $sales   = $query->paginate($perPage);

        return response()->json([
            'data'       => $sales->items(),
            'pagination' => [
                'total'        => $sales->total(),
                'per_page'     => $sales->perPage(),
                'current_page' => $sales->currentPage(),
                'last_page'    => $sales->lastPage(),
            ],
            'summary' => [
                'total_revenue' => (float) Sale::sum('total_amount'),
                'total_sales'   => Sale::count(),
            ],
        ]);
    }

    // GET /api/sales/{id}
    public function show(Request $request, Sale $sale)
    {
        $this->requirePermission($request, 'sales.view');
        $sale->load('items.product', 'user');
        return response()->json(['data' => $sale]);
    }

    // DELETE /api/sales/{id}
    public function destroy(Request $request, Sale $sale)
    {
        $this->requirePermission($request, 'sales.view');
        $sale->delete();
        return response()->json(['success' => true, 'message' => 'Sale deleted.']);
    }

    // GET /api/sales/export
    public function export(Request $request)
    {
        $this->requirePermission($request, 'sales.view');

        $sales = Sale::with('items.product', 'user')
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->get();

        return response()->json(['data' => $sales]);
    }

    private function requirePermission(Request $request, string $permission): void
    {
        if (!$request->user()->canDo($permission)) {
            abort(response()->json(['error' => 'Permission denied.'], 403));
        }
    }

    /**
     * POST /api/sales
     * Process a direct sale (mobile/frontend sends items directly)
     *
     * Request body:
     * {
     *   "items": [
     *     { "product_id": 1, "quantity": 2 },
     *     { "product_id": 3, "quantity": 1 }
     *   ],
     *   "payment_method": "cash",   // cash | khqr_usd | khqr_khr
     *   "paid_amount": 10.00        // required for cash
     * }
     */
    public function store(Request $request)
    {
        $request->validate([
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|integer|exists:products,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'payment_method'       => 'required|in:cash,khqr_usd,khqr_khr',
            'paid_amount'          => 'required_if:payment_method,cash|numeric|min:0',
        ]);

        \DB::beginTransaction();

        try {
            $totalAmount = 0;
            $saleItems   = [];

            // ── Validate stock and calculate total ───────────────────
            foreach ($request->items as $item) {
                $product = \App\Models\Product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    \DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for: {$product->name}. Available: {$product->stock}",
                    ], 422);
                }

                $lineTotal    = $product->sell_price * $item['quantity'];
                $totalAmount += $lineTotal;

                $saleItems[] = [
                    'product'   => $product,
                    'quantity'  => $item['quantity'],
                    'price'     => $product->sell_price,
                    'total'     => $lineTotal,
                ];
            }

            // ── Calculate change (cash only) ─────────────────────────
            $paidAmount   = $request->payment_method === 'cash'
                ? (float) $request->paid_amount
                : $totalAmount;
            $changeAmount = max(0, $paidAmount - $totalAmount);

            if ($request->payment_method === 'cash' && $paidAmount < $totalAmount) {
                \DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient payment. Total is \${$totalAmount}, paid \${$paidAmount}.",
                ], 422);
            }

            // ── Generate invoice number ──────────────────────────────
            $invoiceNo = 'INV-' . now()->format('Ymd') . '-' .
                str_pad(\App\Models\Sale::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            // ── Create sale record ───────────────────────────────────
            $sale = \App\Models\Sale::create([
                'invoice_no'     => $invoiceNo,
                'user_id'        => auth()->id(),
                'total_amount'   => $totalAmount,
                'paid_amount'    => $paidAmount,
                'change_amount'  => $changeAmount,
                'payment_method' => $request->payment_method,
            ]);

            // ── Create sale items & decrement stock ──────────────────
            foreach ($saleItems as $item) {
                $sale->items()->create([
                    'product_id' => $item['product']->id,
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'total'      => $item['total'],
                ]);

                $item['product']->decrement('stock', $item['quantity']);
            }

            \DB::commit();

            // ── Load sale with items for response ────────────────────
            $sale->load('items.product:id,name,barcode');

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully.',
                'data'    => [
                    'sale'          => $sale,
                    'change_amount' => $changeAmount,
                    'invoice_no'    => $invoiceNo,
                ],
            ], 201);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Sale failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
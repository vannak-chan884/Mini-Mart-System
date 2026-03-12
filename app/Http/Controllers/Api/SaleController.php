<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SaleResource;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SaleController extends ApiController
{
    // GET /api/sales
    public function index(Request $request)
    {
        $user  = $request->user();
        $query = Sale::with('items.product', 'user', 'confirmedBy')->latest();

        if ($user->role === 'customer') {
            $query->where('user_id', $user->id);
        } else {
            $this->requirePermission($request, 'sales.view');
            if ($request->filled('search'))         $query->where('invoice_no', 'like', "%{$request->search}%");
            if ($request->filled('payment_method')) $query->where('payment_method', $request->payment_method);
            if ($request->filled('status'))         $query->where('status', $request->status);
            if ($request->filled('date_from'))      $query->whereDate('created_at', '>=', $request->date_from);
            if ($request->filled('date_to'))        $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sales = $query->paginate($request->get('per_page', 15));
        return $this->paginated(SaleResource::collection($sales), 'Sales loaded.');
    }

    // GET /api/sales/{id}
    public function show(Request $request, Sale $sale)
    {
        $user = $request->user();

        if ($user->role === 'customer' && $sale->user_id !== $user->id) {
            return $this->forbidden();
        } elseif ($user->role !== 'customer') {
            $this->requirePermission($request, 'sales.view');
        }

        $sale->load('items.product', 'user', 'confirmedBy');
        return $this->success(new SaleResource($sale));
    }

    // POST /api/sales
    public function store(Request $request)
    {
        $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'payment_method'     => 'required|in:cash,khqr,khqr_usd,khqr_khr',
            'paid_amount'        => 'required_if:payment_method,cash|numeric|min:0',
            'notes'              => 'nullable|string|max:500',
        ]);

        \DB::beginTransaction();
        try {
            $totalAmount = 0;
            $saleItems   = [];

            foreach ($request->items as $item) {
                $product = \App\Models\Product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    \DB::rollBack();
                    return response()->json(['success' => false,
                        'message' => "Insufficient stock for: {$product->name}. Available: {$product->stock}"], 422);
                }

                $lineTotal    = $product->sell_price * $item['quantity'];
                $totalAmount += $lineTotal;
                $saleItems[] = ['product' => $product, 'quantity' => $item['quantity'],
                                'price' => $product->sell_price, 'total' => $lineTotal];
            }

            $paidAmount   = $request->payment_method === 'cash' ? (float)$request->paid_amount : $totalAmount;
            $changeAmount = max(0, $paidAmount - $totalAmount);

            if ($request->payment_method === 'cash' && $paidAmount < $totalAmount) {
                \DB::rollBack();
                return response()->json(['success' => false,
                    'message' => "Insufficient payment. Total is \${$totalAmount}, paid \${$paidAmount}."], 422);
            }

            // Invoice number
            $lastSale  = Sale::latest()->first();
            $nextId    = $lastSale ? $lastSale->id + 1 : 1;
            $invoiceNo = 'INV-' . now()->format('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // ── Status logic ─────────────────────────────────────────────
            // Cash on delivery  → pending (admin/cashier confirms after delivery)
            // KHQR              → paid    (payment already verified before store() is called)
            $isCash   = $request->payment_method === 'cash';
            $status   = $isCash ? 'pending' : 'paid';

            $sale = Sale::create([
                'invoice_no'     => $invoiceNo,
                'user_id'        => auth()->id(),
                'total_amount'   => $totalAmount,
                'paid_amount'    => $paidAmount,
                'change_amount'  => $changeAmount,
                'payment_method' => $request->payment_method,
                'status'         => $status,
                'notes'          => $request->notes,
                'confirmed_at'   => $isCash ? null : now(),
                'confirmed_by'   => $isCash ? null : auth()->id(),
            ]);

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
            $sale->load('items.product', 'user', 'confirmedBy');
            return $this->created(new SaleResource($sale), 'Sale completed successfully.');

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Sale failed: ' . $e->getMessage()], 500);
        }
    }

    // PATCH /api/sales/{id}/status  ← NEW
    // Admin or cashier updates COD order status + uploads payment proof
    public function updateStatus(Request $request, Sale $sale)
    {
        $this->requirePermission($request, 'sales.view');

        $request->merge($request->json()->all() ?: []);

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
            // Delete old proof if it exists
            if ($sale->payment_proof) {
                Storage::disk('public')->delete($sale->payment_proof);
            }
            $data['payment_proof'] = $request->file('payment_proof')
                ->store('payment_proofs', 'public');
        }

        // Mark confirmed when moving to paid
        if ($request->status === 'paid') {
            $data['confirmed_by'] = $request->user()->id;
            $data['confirmed_at'] = now();
        }

        // Clear confirmation if moved back from paid
        if (in_array($request->status, ['pending', 'delivering', 'cancelled'])) {
            $data['confirmed_by'] = null;
            $data['confirmed_at'] = null;
        }

        $sale->update($data);
        $sale->load('items.product', 'user', 'confirmedBy');

        return $this->success(new SaleResource($sale), 'Order status updated.');
    }

    // DELETE /api/sales/{id}
    public function destroy(Request $request, Sale $sale)
    {
        $this->requirePermission($request, 'sales.view');
        if ($sale->payment_proof) {
            Storage::disk('public')->delete($sale->payment_proof);
        }
        $sale->delete();
        return $this->success(null, 'Sale deleted.');
    }

    // GET /api/sales/export
    public function export(Request $request)
    {
        $this->requirePermission($request, 'sales.view');
        $sales = Sale::with('items.product', 'user', 'confirmedBy')
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()->get();
        return $this->success(SaleResource::collection($sales));
    }

    private function requirePermission(Request $request, string $permission): void
    {
        if (!$request->user()->canDo($permission)) {
            abort(response()->json(['error' => 'Permission denied.'], 403));
        }
    }
}
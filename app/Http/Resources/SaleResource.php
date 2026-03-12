<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                 => $this->id,
            'invoice_no'         => $this->invoice_no,
            'total_amount'       => $this->total_amount,
            'paid_amount'        => $this->paid_amount,
            'change_amount'      => $this->change_amount,
            'payment_method'     => $this->payment_method,

            // Payment status (cash: pending→paid, khqr: paid immediately)
            'status'             => $this->status,
            'status_label'       => $this->statusLabel(),

            // Delivery status (ALL orders: pending→delivering→delivered)
            'delivery_status'    => $this->delivery_status,
            'delivery_status_label' => $this->deliveryStatusLabel(),

            // Notes & proof
            'notes'              => $this->notes,
            'payment_reference'  => $this->payment_reference,
            'payment_proof_url'  => $this->payment_proof_url,

            // Confirmation info
            'confirmed_at'       => $this->confirmed_at?->toISOString(),
            'confirmed_by'       => $this->confirmedBy ? [
                'id'   => $this->confirmedBy->id,
                'name' => $this->confirmedBy->name,
            ] : null,

            // Bakong
            'bakong_hash'        => $this->bakong_hash,
            'aba_tran_id'        => $this->aba_tran_id,

            // Relations
            'user'               => $this->user ? [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'email' => $this->user->email,
            ] : null,
            'items'              => SaleItemResource::collection($this->whenLoaded('items')),

            'created_at'         => $this->created_at->toISOString(),
            'updated_at'         => $this->updated_at->toISOString(),
        ];
    }

    private function statusLabel(): string
    {
        return match($this->status) {
            'pending'    => '🟡 Pending',
            'delivering' => '🔵 Delivering',
            'paid'       => '🟢 Paid',
            'cancelled'  => '🔴 Cancelled',
            default      => $this->status,
        };
    }

    private function deliveryStatusLabel(): string
    {
        return match($this->delivery_status) {
            'pending'    => '📦 Preparing',
            'delivering' => '🚚 Out for Delivery',
            'delivered'  => '✅ Delivered',
            default      => $this->delivery_status ?? 'pending',
        };
    }
}
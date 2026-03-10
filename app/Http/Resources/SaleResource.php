<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'invoice_no'     => $this->invoice_no,
            'total_amount'   => (float) $this->total_amount,
            'paid_amount'    => (float) $this->paid_amount,
            'change_amount'  => (float) $this->change_amount,
            'payment_method' => $this->payment_method,
            'payment_label'  => match($this->payment_method) {
                'cash'      => 'Cash 💵',
                'khqr_usd'  => 'KHQR USD 🇺🇸',
                'khqr_khr'  => 'KHQR KHR 🇰🇭',
                default     => $this->payment_method,
            },
            'cashier'        => new UserResource($this->whenLoaded('user')),
            'items'          => SaleItemResource::collection($this->whenLoaded('items')),
            'items_count'    => $this->whenCounted('items'),
            'created_at'     => $this->created_at?->format('d M Y H:i'),
        ];
    }
}
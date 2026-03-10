<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'barcode'         => $this->barcode,
            'cost_price'      => (float) $this->cost_price,
            'sell_price'      => (float) $this->sell_price,
            'stock'           => $this->stock,
            'low_stock_alert' => $this->low_stock_alert,
            'is_low_stock'    => $this->stock <= ($this->low_stock_alert ?? 5),
            'image'           => $this->image
                ? asset('storage/' . $this->image)
                : null,
            'category'        => new CategoryResource($this->whenLoaded('category')),
            'created_at'      => $this->created_at?->format('d M Y'),
        ];
    }
}

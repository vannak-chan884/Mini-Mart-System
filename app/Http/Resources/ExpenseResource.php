<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'description'  => $this->description,
            'amount'       => (float) $this->amount,
            'expense_date' => $this->expense_date?->format('d M Y'),
            'category'     => new ExpenseCategoryResource($this->whenLoaded('expenseCategory')),
            'created_by'   => new UserResource($this->whenLoaded('user')),
            'created_at'   => $this->created_at?->format('d M Y H:i'),
        ];
    }
}

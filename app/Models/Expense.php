<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'title',
        'amount',
        'date',
        'note',
        'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }
}
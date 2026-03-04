<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $fillable = [
        'category_id',
        'barcode',
        'name',
        'image',
        'cost_price',
        'sell_price',
        'stock',
        'low_stock_alert',
    ];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }

        return asset('images/no-image.png');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function isOutOfStock()
    {
        return $this->stock <= 0;
    }

    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class);
    }
}

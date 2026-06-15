<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price'
    ];

    /**
     * Chaque ligne de la commande est liée à un produit
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
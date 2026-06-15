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

    // Relation vers la commande parente
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relation vers le produit (indispensable pour récupérer le nom de l'article !)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
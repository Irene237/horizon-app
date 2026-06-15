<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'user_id',
        'subtotal',
        'discount',
        'discount_type',
        'total',
        'payment_mode'
    ];

    /**
     * Une commande contient plusieurs lignes d'articles
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Une commande appartient à un client (nullable si vente anonyme)
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
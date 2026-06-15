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
        'payment_mode',
        'payment_status'
    ];

    // Relation vers les lignes de la commande
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relation vers le client (indispensable pour la facture !)
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
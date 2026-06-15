<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    // Indique à Laravel quels champs sont accessibles en écriture
    protected $fillable = [
        'total_amount', 
        'customer_id', 
        'created_at'
    ];
}
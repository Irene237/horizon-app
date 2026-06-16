<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintOrder extends Model
{
    protected $fillable = [
        'customer_id', 'support_type', 'width', 'height', 
        'quantity', 'file_path', 'unit_price', 'total_price', 'status'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
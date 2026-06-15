<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'training_id',
        'payment_status',
        'amount_paid'
    ];

    // Relation vers le client
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relation vers la formation
    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}
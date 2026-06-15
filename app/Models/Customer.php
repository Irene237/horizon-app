<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    // Autoriser Laravel à remplir ces champs automatiquement
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'balance'
    ];

    /**
     * Un client peut avoir plusieurs commandes (Historique)
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
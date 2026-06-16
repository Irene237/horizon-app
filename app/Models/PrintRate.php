<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintRate extends Model
{
    protected $fillable = ['support_type', 'unit_type', 'price'];
}
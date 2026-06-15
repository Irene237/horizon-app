<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingSession extends Model
{
    use HasFactory;

    protected $fillable = ['training_id', 'title', 'session_date', 'start_time'];

    public function attendances()
    {
        return $this->hasMany(AttendanceSheet::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'duration_hours',
        'level',
        'trainer_name',
        'price',
        'max_capacity',
        'start_date',
        'end_date',
    ];
    /**
     * Obtenir toutes les inscriptions pour cette formation
     */
    public function enrollments()
    {
        return $this->hasMany(TrainingEnrollment::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSheet extends Model
{
    use HasFactory;

    protected $fillable = ['training_session_id', 'training_enrollment_id', 'status'];

    public function session()
    {
        return $this->belongsTo(TrainingSession::class, 'training_session_id');
    }

    public function enrollment()
    {
        return $this->belongsTo(TrainingEnrollment::class, 'training_enrollment_id');
    }
}
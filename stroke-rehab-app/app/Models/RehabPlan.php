<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RehabPlan extends Model
{
    protected $fillable = [
        'patient_id',
        'clinician_id',
        'plan_name',
        'description',
        'recovery_probability',
        'ml_confidence_score',
        'difficulty_level',
        'start_date',
        'end_date',
        'status',
        'ml_metadata',
        'feedback_requested',
        'feedback_requested_at',
    ];

    protected $casts = [
        'ml_metadata' => 'json',
        'start_date' => 'date',
        'end_date' => 'date',
        'feedback_requested' => 'boolean',
        'feedback_requested_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function clinician()
    {
        return $this->belongsTo(User::class, 'clinician_id');
    }

    public function exercises()
    {
        return $this->hasMany(PlanExercise::class);
    }
}

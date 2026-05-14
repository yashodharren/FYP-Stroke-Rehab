<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientFeedback extends Model
{
    protected $fillable = [
        'patient_id',
        'plan_exercise_id',
        'rehab_plan_id',
        'is_plan_feedback',
        'pain_level',
        'difficulty_rating',
        'mood_rating',
        'comments',
        'overall_comments',
        'completed_exercise',
        'feedback_date',
    ];

    protected $casts = [
        'feedback_date' => 'timestamp',
        'completed_exercise' => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function planExercise()
    {
        return $this->belongsTo(PlanExercise::class);
    }
}

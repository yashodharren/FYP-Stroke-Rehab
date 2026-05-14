<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanExercise extends Model
{
    protected $fillable = [
        'rehab_plan_id',
        'exercise_id',
        'day_of_week',
        'frequency_per_week',
        'scheduled_time',
        'scheduled_times',
        'custom_repetitions',
        'custom_duration_minutes',
        'notes',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'scheduled_times' => 'json',
        'completed_at' => 'datetime',
    ];

    public function rehabPlan()
    {
        return $this->belongsTo(RehabPlan::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    public function feedback()
    {
        return $this->hasMany(PatientFeedback::class);
    }
}

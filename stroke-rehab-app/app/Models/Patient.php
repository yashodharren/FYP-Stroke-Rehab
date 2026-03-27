<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'user_id',
        'clinician_id',
        'age',
        'stroke_type',
        'deficit_area',
        'medical_history',
        'recovery_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clinician()
    {
        return $this->belongsTo(User::class, 'clinician_id');
    }

    public function rehabPlans()
    {
        return $this->hasMany(RehabPlan::class);
    }

    public function feedback()
    {
        return $this->hasMany(PatientFeedback::class);
    }
}

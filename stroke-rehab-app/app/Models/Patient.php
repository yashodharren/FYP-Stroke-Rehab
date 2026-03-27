<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'user_id',
        'clinician_id',
        'age',
        'recovery_status',
        'gender',
        'rsbp',
        'stroke_subtype',
        'conscious_state',
        'rdef1',
        'rdef2',
        'rdef3',
        'rdef4',
        'rdef5',
        'rdef6',
        'rdef7',
        'rdef8',
    ];

    protected $casts = [
        'rdef1' => 'boolean',
        'rdef2' => 'boolean',
        'rdef3' => 'boolean',
        'rdef4' => 'boolean',
        'rdef5' => 'boolean',
        'rdef6' => 'boolean',
        'rdef7' => 'boolean',
        'rdef8' => 'boolean',
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

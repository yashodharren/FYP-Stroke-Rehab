<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clinician extends Model
{
    protected $fillable = [
        'user_id',
        'specialization',
        'license_number',
        'bio',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }
}

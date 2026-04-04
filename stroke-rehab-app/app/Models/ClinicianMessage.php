<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicianMessage extends Model
{
    protected $fillable = [
        'clinician_id',
        'patient_id',
        'message',
        'type',
    ];

    public function clinician()
    {
        return $this->belongsTo(User::class, 'clinician_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}

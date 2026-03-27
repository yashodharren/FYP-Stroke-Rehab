<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    protected $fillable = [
        'name',
        'description',
        'difficulty_level',
        'target_area',
        'duration_minutes',
        'repetitions',
        'instructions',
        'image_url',
        'video_url',
    ];

    public function planExercises()
    {
        return $this->hasMany(PlanExercise::class);
    }
}

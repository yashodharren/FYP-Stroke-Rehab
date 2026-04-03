<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Exercise;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $exercises = [
            [
                'name' => 'Shoulder Shrug',
                'description' => 'Sit straight; raise shoulders toward ears; hold for 3 seconds',
                'difficulty_level' => '1',
                'target_area' => 'arm',
                'duration_minutes' => 10,
                'repetitions' => 15,
                'instructions' => 'Sit upright in a chair. Raise both shoulders up toward your ears, hold for 3 seconds, then lower. Repeat 15 times.',
            ],
            [
                'name' => 'Hand & Wrist Stretch',
                'description' => 'Place palms together; push left hand against right; hold for 3 seconds',
                'difficulty_level' => '1',
                'target_area' => 'arm',
                'duration_minutes' => 10,
                'repetitions' => 10,
                'instructions' => 'Place palms together in front of chest. Push left hand against right hand, hold for 3 seconds. Repeat 10 times on each side.',
            ],
            [
                'name' => 'Finger Flexion',
                'description' => 'Make a fist and release; repeat slowly',
                'difficulty_level' => '1',
                'target_area' => 'arm',
                'duration_minutes' => 10,
                'repetitions' => 20,
                'instructions' => 'Hold hand out in front. Make a tight fist, hold for 2 seconds, then open hand wide. Repeat 20 times.',
            ],
            [
                'name' => 'Elbow Flexion',
                'description' => 'Bend elbow bringing hand toward shoulder; straighten',
                'difficulty_level' => '2',
                'target_area' => 'arm',
                'duration_minutes' => 15,
                'repetitions' => 15,
                'instructions' => 'Sit or stand upright. Bend elbow bringing hand toward shoulder, hold for 2 seconds, then straighten. Repeat 15 times per arm.',
            ],
            [
                'name' => 'Ankle Pumps',
                'description' => 'Point toes down then pull toes toward body',
                'difficulty_level' => '1',
                'target_area' => 'leg',
                'duration_minutes' => 10,
                'repetitions' => 20,
                'instructions' => 'Sit in a chair with feet flat. Point toes down, then pull toes toward body. Repeat 20 times.',
            ],
            [
                'name' => 'Sitting Knee Extension',
                'description' => 'Straighten leg out in front while sitting',
                'difficulty_level' => '2',
                'target_area' => 'leg',
                'duration_minutes' => 15,
                'repetitions' => 12,
                'instructions' => 'Sit in a chair. Straighten one leg out in front, hold for 2 seconds, then lower. Repeat 12 times per leg.',
            ],
        ];

        foreach ($exercises as $exercise) {
            Exercise::firstOrCreate(
                ['name' => $exercise['name']],
                $exercise
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Exercise::whereIn('name', [
            'Shoulder Shrug',
            'Hand & Wrist Stretch',
            'Finger Flexion',
            'Elbow Flexion',
            'Ankle Pumps',
            'Sitting Knee Extension',
        ])->delete();
    }
};

<?php

namespace Database\Seeders;

use App\Models\Exercise;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exercises = [
            [
                'name' => 'Seated Marching',
                'description' => 'Lift knees alternately while sitting in a chair',
                'difficulty_level' => '1',
                'target_area' => 'leg',
                'duration_minutes' => 15,
                'repetitions' => 20,
                'instructions' => 'Sit upright in a chair. Lift one knee up towards chest, then lower. Repeat with other leg. Do 20 repetitions per leg.',
            ],
            [
                'name' => 'Arm Raises',
                'description' => 'Raise arms forward and to the sides',
                'difficulty_level' => '1',
                'target_area' => 'arm',
                'duration_minutes' => 10,
                'repetitions' => 15,
                'instructions' => 'Stand or sit upright. Raise both arms forward to shoulder height, then lower. Repeat 15 times.',
            ],
            [
                'name' => 'Leg Lifts',
                'description' => 'Lift leg while standing or lying down',
                'difficulty_level' => '2',
                'target_area' => 'leg',
                'duration_minutes' => 15,
                'repetitions' => 12,
                'instructions' => 'Stand with support. Lift one leg to the side, hold for 2 seconds, lower. Repeat 12 times per leg.',
            ],
            [
                'name' => 'Hand Grip Exercises',
                'description' => 'Squeeze and release hand exercises',
                'difficulty_level' => '1',
                'target_area' => 'arm',
                'duration_minutes' => 10,
                'repetitions' => 20,
                'instructions' => 'Hold a soft ball or therapy putty. Squeeze for 3 seconds, release. Repeat 20 times.',
            ],
            [
                'name' => 'Walking with Support',
                'description' => 'Walk with walker or parallel bars',
                'difficulty_level' => '2',
                'target_area' => 'leg',
                'duration_minutes' => 20,
                'repetitions' => 1,
                'instructions' => 'Use walker or parallel bars for support. Walk slowly for 20 minutes.',
            ],
            [
                'name' => 'Speech Exercises',
                'description' => 'Articulation and pronunciation exercises',
                'difficulty_level' => '2',
                'target_area' => 'speech',
                'duration_minutes' => 15,
                'repetitions' => 10,
                'instructions' => 'Repeat vowels (A, E, I, O, U) slowly and clearly. Do 10 repetitions.',
            ],
            [
                'name' => 'Cognitive Exercises',
                'description' => 'Memory and concentration games',
                'difficulty_level' => '2',
                'target_area' => 'cognitive',
                'duration_minutes' => 20,
                'repetitions' => 1,
                'instructions' => 'Complete memory games or puzzles for 20 minutes.',
            ],
            [
                'name' => 'Resistance Band Exercises',
                'description' => 'Exercises using resistance bands',
                'difficulty_level' => '3',
                'target_area' => 'arm',
                'duration_minutes' => 15,
                'repetitions' => 15,
                'instructions' => 'Use resistance band for arm exercises. Perform 15 repetitions with moderate resistance.',
            ],
        ];

        foreach ($exercises as $exercise) {
            Exercise::create($exercise);
        }
    }
}

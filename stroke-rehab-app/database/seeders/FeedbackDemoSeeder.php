<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Patient;
use App\Models\RehabPlan;
use App\Models\Exercise;
use App\Models\PlanExercise;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FeedbackDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create patient user
        $user = User::firstOrCreate(
            ['email' => 'feedbackdemo@test.com'],
            [
                'name'     => 'Demo Feedback Patient',
                'password' => Hash::make('password123'),
                'role'     => 'patient',
            ]
        );

        // 2. Create patient profile
        $patient = Patient::firstOrCreate(
            ['user_id' => $user->id],
            [
                'age'             => 58,
                'gender'          => 1,
                'rsbp'            => 140,
                'stroke_subtype'  => 'PACS',
                'conscious_state' => 'Alert',
                'rdef2'           => true,
                'rdef3'           => true,
                'recovery_status' => 'in_progress',
            ]
        );

        // 3. Assign to first available clinician
        $clinician = User::where('role', 'clinician')->first();
        if ($clinician) {
            $patient->update(['clinician_id' => $clinician->id]);
        }

        $clinicianId = $clinician ? $clinician->id : $user->id;

        // 4. Create active plan started 35 days ago
        $plan = RehabPlan::create([
            'patient_id'          => $patient->id,
            'clinician_id'        => $clinicianId,
            'plan_name'           => 'Upper & Lower Limb Recovery Plan',
            'description'         => 'Rehabilitation plan for arm and leg deficit recovery.',
            'recovery_probability' => 0.68,
            'ml_confidence_score' => 0.81,
            'difficulty_level'    => 2,
            'start_date'          => Carbon::now()->subDays(35)->toDateString(),
            'status'              => 'active',
            'feedback_requested'  => false,
        ]);

        // 5. Add 10 exercises — 7 marked complete (70% completion)
        $exercises = Exercise::limit(10)->get();

        $days  = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $times = ['09:00',  '10:00',   '11:00',     '12:00',    '09:00',  '10:00',  '11:00',   '12:00',     '09:00',   '10:00'];

        foreach ($exercises as $i => $exercise) {
            $isCompleted = $i < 7;
            PlanExercise::create([
                'rehab_plan_id'          => $plan->id,
                'exercise_id'            => $exercise->id,
                'day_of_week'            => $days[$i],
                'frequency_per_week'     => 3,
                'scheduled_time'         => $times[$i],
                'custom_repetitions'     => 10,
                'custom_duration_minutes' => 30,
                'is_completed'           => $isCompleted,
                'completed_at'           => $isCompleted ? Carbon::now()->subDays(rand(1, 30)) : null,
            ]);
        }

        $this->command->info('✓ Demo patient created: feedbackdemo@test.com / password123');
        $this->command->info('✓ Plan started 35 days ago with 70% completion — feedback eligible.');
    }
}

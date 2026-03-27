<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Patient;
use App\Models\Clinician;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@rehab.local',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $clinician = User::create([
            'name' => 'Dr. Sarah Johnson',
            'email' => 'clinician@rehab.local',
            'password' => bcrypt('password'),
            'role' => 'clinician',
        ]);

        Clinician::create([
            'user_id' => $clinician->id,
            'specialization' => 'Stroke Rehabilitation',
            'license_number' => 'LIC-001',
            'bio' => 'Experienced stroke rehabilitation specialist with 10+ years of practice.',
        ]);

        $patient1 = User::create([
            'name' => 'John Doe',
            'email' => 'patient1@rehab.local',
            'password' => bcrypt('password'),
            'role' => 'patient',
        ]);

        Patient::create([
            'user_id' => $patient1->id,
            'clinician_id' => $clinician->id,
            'age' => 70,
            'stroke_type' => 'ischemic',
            'deficit_area' => 'leg',
            'medical_history' => 'Hypertension, Diabetes Type 2',
            'recovery_status' => 'new',
        ]);

        $patient2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'patient2@rehab.local',
            'password' => bcrypt('password'),
            'role' => 'patient',
        ]);

        Patient::create([
            'user_id' => $patient2->id,
            'clinician_id' => $clinician->id,
            'age' => 65,
            'stroke_type' => 'hemorrhagic',
            'deficit_area' => 'arm',
            'medical_history' => 'High cholesterol',
            'recovery_status' => 'in_progress',
        ]);
    }
}

<?php
require 'stroke-rehab-app/vendor/autoload.php';
$app = require_once 'stroke-rehab-app/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Create unassigned patient user
$user = User::create([
    'name' => 'John Test Patient',
    'email' => 'john.test@example.com',
    'password' => Hash::make('password'),
    'role' => 'patient',
]);

echo "✓ User created successfully!\n";
echo "ID: {$user->id}\n";
echo "Name: {$user->name}\n";
echo "Email: {$user->email}\n";
echo "Password: password\n";
echo "Status: Unassigned (no clinician)\n";

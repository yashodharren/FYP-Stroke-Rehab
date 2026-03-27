<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('clinician_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('age');
            $table->enum('stroke_type', ['ischemic', 'hemorrhagic', 'tia'])->nullable();
            $table->enum('deficit_area', ['arm', 'leg', 'both', 'speech', 'cognitive'])->nullable();
            $table->text('medical_history')->nullable();
            $table->enum('recovery_status', ['new', 'in_progress', 'completed', 'paused'])->default('new');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};

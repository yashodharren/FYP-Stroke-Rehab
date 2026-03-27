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
        Schema::create('patient_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_exercise_id')->constrained()->onDelete('cascade');
            $table->integer('pain_level')->nullable();
            $table->integer('difficulty_rating')->nullable();
            $table->integer('mood_rating')->nullable();
            $table->text('comments')->nullable();
            $table->boolean('completed_exercise')->default(false);
            $table->timestamp('feedback_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_feedback');
    }
};

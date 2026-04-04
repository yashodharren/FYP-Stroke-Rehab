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
        Schema::create('clinician_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clinician_id');
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->text('message');
            $table->string('type')->default('info');
            $table->timestamps();

            $table->foreign('clinician_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinician_messages');
    }
};

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
        Schema::table('clinicians', function (Blueprint $table) {
            $table->string('hospital_affiliation')->nullable()->after('specialization');
            $table->string('phone')->nullable()->after('hospital_affiliation');
            $table->boolean('is_verified')->default(false)->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinicians', function (Blueprint $table) {
            $table->dropColumn(['hospital_affiliation', 'phone', 'is_verified']);
        });
    }
};

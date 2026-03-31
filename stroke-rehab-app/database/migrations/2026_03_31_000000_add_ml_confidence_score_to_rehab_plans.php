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
        Schema::table('rehab_plans', function (Blueprint $table) {
            $table->decimal('ml_confidence_score', 3, 2)->nullable()->after('recovery_probability');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rehab_plans', function (Blueprint $table) {
            $table->dropColumn('ml_confidence_score');
        });
    }
};

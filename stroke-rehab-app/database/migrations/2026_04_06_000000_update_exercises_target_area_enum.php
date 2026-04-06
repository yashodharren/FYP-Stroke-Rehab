<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the target_area enum to include all new values
        DB::statement("ALTER TABLE exercises MODIFY COLUMN target_area ENUM('arm', 'leg', 'both', 'speech', 'cognitive', 'upper_limb', 'lower_limb', 'face', 'emotional', 'coordination', 'core/upper')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE exercises MODIFY COLUMN target_area ENUM('arm', 'leg', 'both', 'speech', 'cognitive')");
    }
};

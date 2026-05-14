<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rehab_plans', function (Blueprint $table) {
            $table->boolean('feedback_requested')->default(false)->after('status');
            $table->timestamp('feedback_requested_at')->nullable()->after('feedback_requested');
        });
    }

    public function down(): void
    {
        Schema::table('rehab_plans', function (Blueprint $table) {
            $table->dropColumn(['feedback_requested', 'feedback_requested_at']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patient_feedback', function (Blueprint $table) {
            $table->foreignId('rehab_plan_id')->nullable()->constrained()->onDelete('cascade')->after('plan_exercise_id');
            $table->boolean('is_plan_feedback')->default(false)->after('rehab_plan_id');
            $table->string('overall_comments')->nullable()->after('comments');
        });
    }

    public function down(): void
    {
        Schema::table('patient_feedback', function (Blueprint $table) {
            $table->dropForeign(['rehab_plan_id']);
            $table->dropColumn(['rehab_plan_id', 'is_plan_feedback', 'overall_comments']);
        });
    }
};

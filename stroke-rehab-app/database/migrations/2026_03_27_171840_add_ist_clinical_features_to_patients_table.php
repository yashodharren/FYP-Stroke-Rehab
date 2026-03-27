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
        Schema::table('patients', function (Blueprint $table) {
            // Demographics & Vitals
            $table->integer('gender')->nullable()->comment('0=Female, 1=Male');
            $table->integer('rsbp')->nullable()->comment('Systolic Blood Pressure (mmHg)');

            // Stroke Characterization
            $table->string('stroke_subtype')->nullable()->comment('TACS, PACS, LACS, POCS, OTH');
            $table->string('conscious_state')->nullable()->comment('Alert, Drowsy, Unconscious');

            // Functional Deficits (RDEF fields)
            $table->boolean('rdef1')->default(false)->comment('Face Deficit');
            $table->boolean('rdef2')->default(false)->comment('Arm/Hand Deficit');
            $table->boolean('rdef3')->default(false)->comment('Leg/Foot Deficit');
            $table->boolean('rdef4')->default(false)->comment('Dysphasia (Speech)');
            $table->boolean('rdef5')->default(false)->comment('Hemianopia (Vision)');
            $table->boolean('rdef6')->default(false)->comment('Visuospatial Disorder');
            $table->boolean('rdef7')->default(false)->comment('Brainstem/Cerebellar Signs');
            $table->boolean('rdef8')->default(false)->comment('Other Deficits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'gender',
                'rsbp',
                'stroke_subtype',
                'conscious_state',
                'rdef1',
                'rdef2',
                'rdef3',
                'rdef4',
                'rdef5',
                'rdef6',
                'rdef7',
                'rdef8'
            ]);
        });
    }
};

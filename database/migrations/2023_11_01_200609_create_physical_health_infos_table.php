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
        Schema::create('physical_health_infos', function (Blueprint $table) {
            $table->id();
            $table->string('currentPhysicalHealth')->nullable();
            $table->string('physicalActivityEngagement')->nullable();
            $table->string('reasonForMedCare')->nullable();
            $table->string('medicineType')->nullable();
            $table->string('reasonForMedication')->nullable();
            $table->string('previousCounselingDetail')->nullable();
            $table->string('ongoingCounselingDetail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('physical_health_infos');
    }
};

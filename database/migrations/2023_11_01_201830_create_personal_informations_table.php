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
        Schema::create('personal_informations', function (Blueprint $table) {
            $table->id();
            $table->string('sex');
            $table->date('DOB');
            $table->string('religion');
            $table->string('civilStatus');
            $table->string('emailAddress');
            $table->string('mobileNo');
            $table->string('telNo')->nullable();
            $table->string('presentAddress');
            $table->string('permanentAddress');
            $table->integer('emergencyContactID');
            $table->string('bestCharacteristics')->nullable();
            $table->string('specialSkills')->nullable();
            $table->string('goals')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_informations');
    }
};

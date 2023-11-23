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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('studentID')->unique();
            $table->unsignedBigInteger('userID');
            $table->string('name');
            $table->integer('personalInformationID')->nullable();
            $table->integer('familyBackgroundID')->nullable();
            $table->integer('physicalHealthInfoID')->nullable();
            $table->integer('careerID')->nullable();
            $table->integer('yearLevel');
            $table->integer('blockID')->nullable();
            $table->timestamps();

            $table->foreign('userID')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

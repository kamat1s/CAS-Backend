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
        Schema::create('careers', function (Blueprint $table) {
            $table->id();
            $table->integer('firstCourseID')->nullable();
            $table->integer('secondCourseID')->nullable();
            $table->integer('thirdCourseID')->nullable();
            $table->text('factors')->nullable();
            $table->string('otherFactors')->nullable();
            $table->string('futureVision')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('careers');
    }
};

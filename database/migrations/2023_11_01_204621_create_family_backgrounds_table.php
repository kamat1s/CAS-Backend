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
        Schema::create('family_backgrounds', function (Blueprint $table) {
            $table->id();
            $table->integer('fatherID');
            $table->integer('motherID');
            $table->integer('guardianID');
            $table->string('relationshipStatus')->nullable();
            $table->string('livingArrangement')->nullable();
            $table->string('siblingRank');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_backgrounds');
    }
};

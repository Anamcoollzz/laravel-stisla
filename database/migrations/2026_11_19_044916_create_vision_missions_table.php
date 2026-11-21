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
        Schema::create('vision_missions', function (Blueprint $table) {
            $table->id();
            $table->string('vision_eyebrow', 100)->default('Our Vision');
            $table->string('vision_title', 200);
            $table->text('vision_description')->nullable();
            $table->string('mission_eyebrow', 100)->default('Our Mission');
            $table->string('mission_title', 200);
            $table->json('mission_points')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vision_missions');
    }
};

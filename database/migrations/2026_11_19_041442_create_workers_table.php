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
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 160)->unique();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->string('category', 60);
            $table->string('category_label', 120);
            $table->unsignedTinyInteger('age')->default(25);
            $table->string('age_bracket', 20)->default('26-35');
            $table->string('origin', 150)->nullable();
            $table->string('origin_region', 100)->nullable();
            $table->string('education', 150)->nullable();
            $table->decimal('experience_years', 4, 1)->default(0);
            $table->string('experience_label', 60)->nullable();
            $table->string('experience_bracket', 20)->default('1-3');
            $table->string('status', 40)->default('Available');
            $table->boolean('is_featured')->default(false);
            $table->date('published_date')->nullable();
            $table->string('photo', 255)->nullable();
            $table->json('gallery')->nullable();
            $table->json('languages')->nullable();
            $table->text('bio')->nullable();
            $table->json('skills')->nullable();
            $table->json('work_history')->nullable();
            $table->json('placement_preferences')->nullable();
            $table->json('training_certifications')->nullable();
            $table->timestamps();

            $table->index(['category', 'status', 'is_featured']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};

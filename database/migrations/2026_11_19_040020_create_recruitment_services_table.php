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
        Schema::create('recruitment_services', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 160)->unique();
            $table->string('category_key', 60);
            $table->string('title', 150);
            $table->string('subtitle', 200)->nullable();
            $table->string('icon', 100)->default('briefcase');
            $table->string('image', 255)->nullable();
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->json('responsibilities')->nullable();
            $table->json('skills')->nullable();
            $table->json('benefits')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recruitment_services');
    }
};

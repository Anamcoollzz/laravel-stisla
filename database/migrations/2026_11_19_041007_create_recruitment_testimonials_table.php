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
        Schema::create('recruitment_testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('location', 150)->nullable();
            $table->string('service', 150)->nullable();
            $table->text('quote');
            $table->string('avatar', 255)->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
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
        Schema::dropIfExists('recruitment_testimonials');
    }
};

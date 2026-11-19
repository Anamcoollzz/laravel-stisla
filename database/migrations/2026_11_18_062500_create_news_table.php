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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_category_id')->nullable()->constrained('news_categories')->nullOnDelete();
            $table->foreignId('publisher_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->string('thumbnail_mini')->nullable();
            $table->string('thumbnail')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('status', 30)->default('draft');
            $table->timestamps();

            $table->index('status');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};

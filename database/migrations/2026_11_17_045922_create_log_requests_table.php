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
        Schema::create('log_requests', function (Blueprint $table) {
            $table->id();
            $table->text('uri');
            $table->text('query_string')->nullable();
            $table->string('method', 10);
            $table->json('request_data')->nullable();
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('roles')->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('platform', 100)->nullable();
            $table->string('device', 100)->nullable();
            $table->boolean('is_ajax')->default(false);
            $table->timestamps();

            $table->index('created_at');
            $table->index('method');
            $table->index('ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_requests');
    }
};

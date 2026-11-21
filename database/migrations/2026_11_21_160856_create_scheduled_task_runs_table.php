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
        Schema::create('scheduled_task_runs', function (Blueprint $table) {
            $table->id();
            $table->string('task_key', 120)->index();
            $table->string('command');
            $table->string('description')->nullable();
            $table->string('expression', 60);
            $table->string('status', 20)->index();
            $table->integer('exit_code')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->longText('output')->nullable();
            $table->string('triggered_by', 20)->default('scheduler')->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('started_at')->nullable()->index();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_task_runs');
    }
};

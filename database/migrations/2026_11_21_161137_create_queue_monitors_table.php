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
        Schema::create('queue_monitors', function (Blueprint $table) {
            $table->id();
            $table->string('job_id', 100)->nullable()->index();
            $table->string('job_name');
            $table->string('connection', 60)->default('database');
            $table->string('queue', 60)->default('default')->index();
            $table->string('status', 20)->index();
            $table->unsignedInteger('attempts')->default(1);
            $table->unsignedInteger('duration_ms')->nullable();
            $table->longText('exception')->nullable();
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
        Schema::dropIfExists('queue_monitors');
    }
};

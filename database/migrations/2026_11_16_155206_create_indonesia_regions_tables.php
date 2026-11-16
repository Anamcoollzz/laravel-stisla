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
        if (! Schema::hasTable('provinces')) {
            Schema::create('provinces', function (Blueprint $table) {
                $table->char('id', 2)->primary();
                $table->string('name');
            });
        }

        if (! Schema::hasTable('regencies')) {
            Schema::create('regencies', function (Blueprint $table) {
                $table->char('id', 4)->primary();
                $table->char('province_id', 2)->index();
                $table->string('name');

                $table->foreign('province_id')
                    ->references('id')
                    ->on('provinces')
                    ->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regencies');
        Schema::dropIfExists('provinces');
    }
};

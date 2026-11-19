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
        Schema::create('company_introductions', function (Blueprint $table) {
            $table->id();
            $table->string('image', 255)->nullable();
            $table->string('image_alt', 255)->nullable();
            $table->string('mission_label', 100)->default('Our Mission');
            $table->text('mission_text')->nullable();
            $table->string('eyebrow', 100)->default('Company Introduction');
            $table->string('title', 200);
            $table->text('paragraph_1')->nullable();
            $table->text('paragraph_2')->nullable();
            $table->string('feature_1_icon', 100)->default('shield-check');
            $table->string('feature_1_title', 150)->nullable();
            $table->text('feature_1_description')->nullable();
            $table->string('feature_2_icon', 100)->default('handshake');
            $table->string('feature_2_title', 150)->nullable();
            $table->text('feature_2_description')->nullable();
            $table->string('primary_button_label', 100)->default('Learn More');
            $table->string('primary_button_url', 255)->nullable();
            $table->string('whatsapp_button_label', 100)->default('Consult Our Team');
            $table->text('whatsapp_button_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_introductions');
    }
};

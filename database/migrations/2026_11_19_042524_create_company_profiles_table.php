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
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('legal_name', 150);
            $table->string('tagline', 255)->nullable();
            $table->string('license_number', 150)->nullable();
            $table->string('whatsapp_number', 30);
            $table->string('phone_display', 50);
            $table->string('secondary_phone', 50)->nullable();
            $table->string('email', 150);
            $table->text('address');
            $table->string('short_address', 150)->nullable();
            $table->string('operating_hours_weekday', 150)->nullable();
            $table->string('operating_hours_saturday', 150)->nullable();
            $table->string('operating_hours_sunday', 150)->nullable();
            $table->text('map_embed_url')->nullable();
            $table->string('instagram_url', 255)->nullable();
            $table->string('facebook_url', 255)->nullable();
            $table->string('youtube_url', 255)->nullable();
            $table->text('default_whatsapp_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_profiles');
    }
};

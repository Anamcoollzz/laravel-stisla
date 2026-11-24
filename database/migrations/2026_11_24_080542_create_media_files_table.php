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
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')->nullable()->constrained('media_folders')->nullOnDelete();
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('alt_text')->nullable();
            $table->text('description')->nullable();
            $table->json('tags')->nullable();
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('mime_type');
            $table->string('extension', 20)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('visibility', 20)->default('public');
            $table->json('allowed_roles')->nullable();
            $table->boolean('is_shared')->default(false);
            $table->string('share_token', 64)->nullable()->unique();
            $table->timestamp('share_expires_at')->nullable();
            $table->unsignedInteger('current_version')->default(1);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('media_file_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_file_id')->constrained('media_files')->cascadeOnDelete();
            $table->unsignedInteger('version_number');
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('mime_type');
            $table->unsignedBigInteger('size')->default(0);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_file_versions');
        Schema::dropIfExists('media_files');
    }
};

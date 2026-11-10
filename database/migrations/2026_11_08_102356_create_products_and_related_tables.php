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
        // Clean up partial leftovers if previous migration run failed
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_brands');
        Schema::dropIfExists('product_vendors');
        Schema::dropIfExists('product_colors');
        Schema::dropIfExists('product_sizes');

        // 1. Product Sizes
        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->timestamps();
        });

        // 2. Product Colors
        Schema::create('product_colors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color_code')->nullable();
            $table->timestamps();
        });

        // 3. Product Vendors
        Schema::create('product_vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 4. Product Brands
        Schema::create('product_brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable()->unique();
            $table->timestamps();
        });

        // 5. Products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('product_brands')->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('product_vendors')->nullOnDelete();
            $table->foreignId('size_id')->nullable()->constrained('product_sizes')->nullOnDelete();
            $table->foreignId('color_id')->nullable()->constrained('product_colors')->nullOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->decimal('price', 12, 2);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->text('short_description')->nullable();
            $table->longText('description_detail')->nullable();
            $table->longText('spesifications')->nullable();
            $table->longText('tags')->nullable();
            $table->string('thumbnail')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_brands');
        Schema::dropIfExists('product_vendors');
        Schema::dropIfExists('product_colors');
        Schema::dropIfExists('product_sizes');
    }
};

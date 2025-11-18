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
        Schema::table('products', function (Blueprint $table) {
            // Пол (male, female, unisex)
            $table->enum('gender', ['male', 'female', 'unisex'])->nullable()->after('description');
            
            // Бренд
            $table->foreignId('brand_id')->nullable()->after('category_id')->constrained()->onDelete('set null');
            
            // Множественные изображения (JSON массив URL)
            $table->json('images')->nullable()->after('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn(['gender', 'brand_id', 'images']);
        });
    }
};

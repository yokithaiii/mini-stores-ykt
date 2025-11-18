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
            // Скидка
            $table->enum('discount_type', ['none', 'percent', 'fixed'])->default('none')->after('price');
            $table->decimal('discount_value', 10, 2)->nullable()->after('discount_type');
            
            // Характеристики (JSON)
            $table->json('attributes')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value', 'attributes']);
        });
    }
};

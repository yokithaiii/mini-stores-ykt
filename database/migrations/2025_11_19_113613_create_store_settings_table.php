<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, textarea, image, json
            $table->string('group')->default('general'); // general, contact, social, etc.
            $table->timestamps();
        });

        // Добавляем начальные настройки
        DB::table('store_settings')->insert([
            ['key' => 'store_name', 'value' => 'Mini Stores', 'type' => 'text', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'store_description', 'value' => 'Интернет-магазин одежды и обуви', 'type' => 'textarea', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'store_phone', 'value' => '+7 (999) 123-45-67', 'type' => 'text', 'group' => 'contact', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'store_email', 'value' => 'info@ministores.ru', 'type' => 'text', 'group' => 'contact', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'store_address', 'value' => 'г. Москва, ул. Примерная, д. 1', 'type' => 'textarea', 'group' => 'contact', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'store_working_hours', 'value' => 'Пн-Пт: 9:00-20:00, Сб-Вс: 10:00-18:00', 'type' => 'textarea', 'group' => 'contact', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'social_instagram', 'value' => '', 'type' => 'text', 'group' => 'social', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'social_telegram', 'value' => '', 'type' => 'text', 'group' => 'social', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'social_whatsapp', 'value' => '', 'type' => 'text', 'group' => 'social', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};

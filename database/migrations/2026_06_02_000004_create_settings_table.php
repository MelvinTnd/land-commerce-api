<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed default settings
        $now = now();
        DB::table('settings')->insert([
            ['key' => 'platform_name', 'value' => 'BéninMarket', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'platform_url', 'value' => 'https://beninmarket.bj', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'platform_description', 'value' => 'La première marketplace de produits artisanaux et culturels du Bénin.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact_email', 'value' => 'contact@beninmarket.bj', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'contact_phone', 'value' => '+229 21 30 56 78', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'currency', 'value' => 'CFA (XOF)', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'timezone', 'value' => 'Africa/Porto-Novo (UTC+1)', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'commission_standard', 'value' => '5', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'commission_premium', 'value' => '3', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'delivery_fee', 'value' => '1500', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'maintenance_mode', 'value' => '0', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'seller_registration_open', 'value' => '1', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'buyer_registration_open', 'value' => '1', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

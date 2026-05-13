<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajouter les colonnes category, whatsapp, instagram à la table shops
     */
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->string('category')->nullable()->after('description');
            $table->string('whatsapp', 30)->nullable()->after('location');
            $table->string('instagram', 100)->nullable()->after('whatsapp');
        });
    }

    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn(['category', 'whatsapp', 'instagram']);
        });
    }
};

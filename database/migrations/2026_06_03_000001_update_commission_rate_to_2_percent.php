<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ramène le taux de commission à 2% pour toutes les boutiques
     * et met à jour la table settings.
     */
    public function up(): void
    {
        // Mettre à jour toutes les boutiques existantes
        DB::table('shops')->update(['commission_rate' => 2.00]);

        // Mettre à jour les settings si la table existe
        if (DB::getSchemaBuilder()->hasTable('settings')) {
            DB::table('settings')
                ->where('key', 'commission_standard')
                ->update(['value' => '2', 'updated_at' => now()]);

            DB::table('settings')
                ->where('key', 'commission_premium')
                ->update(['value' => '1.5', 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        DB::table('shops')->update(['commission_rate' => 5.00]);

        if (DB::getSchemaBuilder()->hasTable('settings')) {
            DB::table('settings')
                ->where('key', 'commission_standard')
                ->update(['value' => '5', 'updated_at' => now()]);

            DB::table('settings')
                ->where('key', 'commission_premium')
                ->update(['value' => '3', 'updated_at' => now()]);
        }
    }
};

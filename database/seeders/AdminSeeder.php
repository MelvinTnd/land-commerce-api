<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Créer un compte Super Administrateur par défaut.
     */
    public function run(): void
    {
        $adminEmail = 'admin@heritage.bj';
        $adminPassword = env('ADMIN_SEEDER_PASSWORD', 'password123');

        // Éviter les doublons : ne créer que si l'admin n'existe pas encore
        if (!User::where('email', $adminEmail)->exists()) {
            User::create([
                'name' => 'Super Administrateur',
                'email' => $adminEmail,
                'phone' => '+229 00 00 00 00',
                'password' => Hash::make($adminPassword),
                'role' => 'admin',
                'is_active' => true,
            ]);

            $this->command->info("Compte admin cree : {$adminEmail}. Mot de passe defini par ADMIN_SEEDER_PASSWORD ou 'password123'.");
        }
        else {
            $this->command->warn("⚠️  Le compte admin {$adminEmail} existe déjà.");
        }
    }
}

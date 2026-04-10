<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Créer un compte Super Administrateur par défaut.
     */
    public function run(): void
    {
        $adminEmail = 'admin@heritage.bj';

        // Éviter les doublons : ne créer que si l'admin n'existe pas encore
        if (!User::where('email', $adminEmail)->exists()) {
            User::create([
                'name' => 'Super Administrateur',
                'email' => $adminEmail,
                'phone' => '+229 00 00 00 00',
                'password' => Hash::make('Admin@1234'),
                'role' => 'admin',
                'is_active' => true,
            ]);

            $this->command->info("✅ Compte admin créé : {$adminEmail} / Admin@1234");
        }
        else {
            $this->command->warn("⚠️  Le compte admin {$adminEmail} existe déjà.");
        }
    }
}

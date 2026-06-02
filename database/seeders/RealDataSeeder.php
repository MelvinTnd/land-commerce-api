<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RealDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ACHETEURS
        $acheteurs = [
            ['name' => 'Lucie Gbeho', 'email' => 'lucie@gmail.com', 'role' => 'acheteur'],
            ['name' => 'Samuel Togni', 'email' => 'samuel@outlook.com', 'role' => 'acheteur'],
            ['name' => 'Marie Amoussou', 'email' => 'marie@yahoo.fr', 'role' => 'acheteur'],
        ];

        foreach ($acheteurs as $a) {
            User::create([
                'name' => $a['name'],
                'email' => $a['email'],
                'password' => Hash::make('password123'),
                'role' => $a['role'],
                'is_active' => true,
            ]);
        }

        // 2. VENDEURS ET BOUTIQUES
        $vendeurs = [
            [
                'user' => ['name' => 'Marc Dossou', 'email' => 'marc@blackmaket.bj'],
                'shop' => [
                    'name' => 'Dahomey Crafts',
                    'slug' => 'dahomey-crafts',
                    'description' => 'Objets d\'art et sculptures authentiques du plateau d\'Abomey.',
                    'location' => 'Abomey',
                ]
            ],
            [
                'user' => ['name' => 'Sophie Alapini', 'email' => 'sophie@blackmaket.bj'],
                'shop' => [
                    'name' => 'Indigo Mode',
                    'slug' => 'indigo-mode',
                    'description' => 'Prêt-à-porter en pagne Indigo et tissus traditionnels revisités.',
                    'location' => 'Cotonou',
                ]
            ],
            [
                'user' => ['name' => 'Thomas Okou', 'email' => 'thomas@blackmaket.bj'],
                'shop' => [
                    'name' => 'Saveurs du Plateau',
                    'slug' => 'saveurs-du-plateau',
                    'description' => 'Épices, huiles et produits naturels transformés localement.',
                    'location' => 'Pobè',
                ]
            ],
        ];

        // 2. CATÉGORIES
        $cats = [
            ['name' => 'Artisanat', 'slug' => 'artisanat', 'icon' => 'palette'],
            ['name' => 'Mode & Textile', 'slug' => 'mode-textile', 'icon' => 'checkroom'],
            ['name' => 'Alimentation & Épices', 'slug' => 'alimentation-epices', 'icon' => 'restaurant_menu'],
            ['name' => 'Maison & Déco', 'slug' => 'maison-deco', 'icon' => 'home'],
        ];
        foreach ($cats as $c) {
            Category::updateOrCreate(['slug' => $c['slug']], $c);
        }

        $artisanat = Category::where('slug', 'artisanat')->first();
        $mode = Category::where('slug', 'mode-textile')->first();
        $alim = Category::where('slug', 'alimentation-epices')->first();

        // 3. VENDEURS ET BOUTIQUES
        $vendeurs = [
            [
                'user' => ['name' => 'Marc Dossou', 'email' => 'marc@blackmaket.bj'],
                'shop' => [
                    'name' => 'Dahomey Crafts',
                    'slug' => 'dahomey-crafts',
                    'description' => 'Objets d\'art et sculptures authentiques du plateau d\'Abomey.',
                    'location' => 'Abomey',
                    'logo' => 'https://images.unsplash.com/photo-1594736797933-d0501ba2fe65?auto=format&fit=crop&q=80&w=200',
                ]
            ],
            [
                'user' => ['name' => 'Sophie Alapini', 'email' => 'sophie@blackmaket.bj'],
                'shop' => [
                    'name' => 'Indigo Mode',
                    'slug' => 'indigo-mode',
                    'description' => 'Prêt-à-porter en pagne Indigo et tissus traditionnels revisités.',
                    'location' => 'Cotonou',
                    'logo' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&q=80&w=200',
                ]
            ],
            [
                'user' => ['name' => 'Thomas Okou', 'email' => 'thomas@blackmaket.bj'],
                'shop' => [
                    'name' => 'Saveurs du Plateau',
                    'slug' => 'saveurs-du-plateau',
                    'description' => 'Épices, huiles et produits naturels transformés localement.',
                    'location' => 'Pobè',
                    'logo' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&q=80&w=200',
                ]
            ],
        ];

        foreach ($vendeurs as $v) {
            $user = User::create([
                'name' => $v['user']['name'],
                'email' => $v['user']['email'],
                'password' => Hash::make('password123'),
                'role' => 'vendeur',
                'is_active' => true,
            ]);

            $shop = Shop::create([
                'user_id' => $user->id,
                'name' => $v['shop']['name'],
                'slug' => $v['shop']['slug'],
                'description' => $v['shop']['description'],
                'location' => $v['shop']['location'],
                'logo' => $v['shop']['logo'],
                'status' => 'active',
            ]);

            // Ajouter quelques produits pour que la boutique ne soit pas vide
            if ($v['shop']['slug'] === 'dahomey-crafts') {
                $shop->products()->create([
                    'name' => 'Masque de Danse Guèlèdè',
                    'slug' => 'masque-gelede-' . uniqid(),
                    'price' => 25000,
                    'stock' => 10,
                    'category_id' => $artisanat->id,
                    'status' => 'publié',
                    'image' => 'https://images.unsplash.com/photo-1578301978693-85fa9c0320b9?auto=format&fit=crop&q=80&w=800'
                ]);
            } elseif ($v['shop']['slug'] === 'indigo-mode') {
                $shop->products()->create([
                    'name' => 'Boubou Indigo Traditionnel',
                    'slug' => 'boubou-indigo-' . uniqid(),
                    'price' => 15000,
                    'stock' => 5,
                    'category_id' => $mode->id,
                    'status' => 'publié',
                    'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&q=80&w=800'
                ]);
            } else {
                $shop->products()->create([
                    'name' => 'Huile de Palme Bio du Plateau',
                    'slug' => 'huile-palme-bio-' . uniqid(),
                    'price' => 3500,
                    'stock' => 50,
                    'category_id' => $alim->id,
                    'status' => 'publié',
                    'image' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&q=80&w=800'
                ]);
            }
        }
    }
}

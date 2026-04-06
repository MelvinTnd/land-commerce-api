<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ==============================
        // 1. CATÉGORIES
        // ==============================
        $categories = [
            ['name' => 'Artisanat', 'slug' => 'artisanat', 'icon' => 'palette', 'sort_order' => 1],
            ['name' => 'Mode & Textile', 'slug' => 'mode-textile', 'icon' => 'checkroom', 'sort_order' => 2],
            ['name' => 'Beauté & Santé', 'slug' => 'beaute-sante', 'icon' => 'spa', 'sort_order' => 3],
            ['name' => 'Alimentation & Épices', 'slug' => 'alimentation-epices', 'icon' => 'restaurant', 'sort_order' => 4],
            ['name' => 'Art & Culture', 'slug' => 'art-culture', 'icon' => 'brush', 'sort_order' => 5],
            ['name' => 'Maison & Déco', 'slug' => 'maison-deco', 'icon' => 'chair', 'sort_order' => 6],
            ['name' => 'Électronique', 'slug' => 'electronique', 'icon' => 'devices', 'sort_order' => 7],
            ['name' => 'Sports & Loisirs', 'slug' => 'sports-loisirs', 'icon' => 'sports_soccer', 'sort_order' => 8],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // ==============================
        // 2. VENDEURS + BOUTIQUES
        // ==============================

        // Vendeur 1 : Koffi Zinsou
        $vendeur1 = User::create([
            'name' => 'Koffi Zinsou',
            'email' => 'koffi@beninmarket.bj',
            'phone' => '+22996001122',
            'password' => Hash::make('password123'),
            'role' => 'vendeur',
            'is_active' => true,
        ]);

        $shop1 = Shop::create([
            'user_id' => $vendeur1->id,
            'name' => 'Atelier Kanvô',
            'slug' => 'atelier-kanvo',
            'description' => 'Expert en sculpture depuis 3 générations, l\'Atelier Kanvô propose des pièces sculptées à la main dans des bois précieux selon les techniques ancestrales du Dahomey.',
            'location' => 'Abomey',
            'status' => 'active',
            'commission_rate' => 5.00,
        ]);

        // Vendeur 2 : Ibrahim Kaba
        $vendeur2 = User::create([
            'name' => 'Ibrahim Kaba',
            'email' => 'ibrahim@beninmarket.bj',
            'phone' => '+22997003344',
            'password' => Hash::make('password123'),
            'role' => 'vendeur',
            'is_active' => true,
        ]);

        $shop2 = Shop::create([
            'user_id' => $vendeur2->id,
            'name' => 'Kaba & Fils',
            'slug' => 'kaba-et-fils',
            'description' => 'Producteur de miel bio et de produits du terroir depuis 2018. L\'excellence du terroir béninois mise en bouteille.',
            'location' => 'Dassa-Zoumé',
            'status' => 'active',
            'commission_rate' => 5.00,
        ]);

        // Vendeur 3 : Aïcha Monteiro
        $vendeur3 = User::create([
            'name' => 'Aïcha Monteiro',
            'email' => 'aicha@beninmarket.bj',
            'phone' => '+22991005566',
            'password' => Hash::make('password123'),
            'role' => 'vendeur',
            'is_active' => true,
        ]);

        $shop3 = Shop::create([
            'user_id' => $vendeur3->id,
            'name' => 'Monteiro Fashion',
            'slug' => 'monteiro-fashion',
            'description' => 'Créatrice de mode béninoise fusionnant le wax traditionnel et les coupes modernes. Chaque pièce raconte une histoire.',
            'location' => 'Cotonou',
            'status' => 'active',
            'commission_rate' => 5.00,
        ]);

        // ==============================
        // 3. ADMIN
        // ==============================
        User::create([
            'name' => 'Admin BéninMarket',
            'email' => 'admin@beninmarket.bj',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // ==============================
        // 4. PRODUITS
        // ==============================
        $artisanat = Category::where('slug', 'artisanat')->first();
        $mode = Category::where('slug', 'mode-textile')->first();
        $beaute = Category::where('slug', 'beaute-sante')->first();
        $alim = Category::where('slug', 'alimentation-epices')->first();
        $art = Category::where('slug', 'art-culture')->first();
        $maison = Category::where('slug', 'maison-deco')->first();
        $elec = Category::where('slug', 'electronique')->first();
        $sport = Category::where('slug', 'sports-loisirs')->first();

        // --- Boutique 1 : Atelier Kanvô (Artisanat / Art) ---
        $produitsShop1 = [
            [
                'name' => 'Statue Royale d\'Abomey',
                'slug' => 'statue-royale-abomey',
                'description' => 'Reproduction fidèle d\'une statue royale du Royaume du Dahomey, sculptée à la main dans du bois d\'iroko.',
                'price' => 45000,
                'stock' => 3,
                'category_id' => $art->id,
                'status' => 'publié',
                'is_featured' => true,
                'avg_rating' => 4.80,
                'total_reviews' => 142,
            ],
            [
                'name' => 'Masque Gèlèdè',
                'slug' => 'masque-gelede',
                'description' => 'Masque traditionnel Gèlèdè, inscrit au patrimoine immatériel de l\'UNESCO. Pièce unique sculptée à la main.',
                'price' => 35000,
                'stock' => 5,
                'category_id' => $artisanat->id,
                'status' => 'publié',
                'is_featured' => true,
                'avg_rating' => 4.90,
                'total_reviews' => 88,
            ],
            [
                'name' => 'Tabouret Nago Sculpté',
                'slug' => 'tabouret-nago-sculpte',
                'description' => 'Tabouret traditionnel Nago en bois massif avec des motifs géométriques gravés à la main.',
                'price' => 28000,
                'stock' => 2,
                'category_id' => $maison->id,
                'status' => 'publié',
                'avg_rating' => 4.70,
                'total_reviews' => 45,
            ],
            [
                'name' => 'Porte Sacrée Miniature',
                'slug' => 'porte-sacree-miniature',
                'description' => 'Réplique miniature d\'une porte du Palais Royal d\'Abomey. Pièce de collection unique.',
                'price' => 55000,
                'stock' => 1,
                'category_id' => $art->id,
                'status' => 'publié',
                'is_featured' => true,
                'avg_rating' => 5.00,
                'total_reviews' => 12,
            ],
        ];

        foreach ($produitsShop1 as $p) {
            $shop1->products()->create($p);
        }

        // --- Boutique 2 : Kaba & Fils (Alimentation) ---
        $produitsShop2 = [
            [
                'name' => 'Miel Pur des Collines',
                'slug' => 'miel-pur-collines',
                'description' => 'Miel 100% naturel récolté dans les collines de Dassa-Zoumé. Sans additif, produit artisanalement.',
                'price' => 4500,
                'stock' => 50,
                'category_id' => $alim->id,
                'status' => 'publié',
                'is_featured' => true,
                'avg_rating' => 4.60,
                'total_reviews' => 108,
            ],
            [
                'name' => 'Coffret Épices du Bénin',
                'slug' => 'coffret-epices-benin',
                'description' => 'Coffret de 6 épices béninoises : piment rouge, gingembre, poivre de Guinée, clou de girofle, muscade et afintin.',
                'price' => 22000,
                'stock' => 30,
                'category_id' => $alim->id,
                'status' => 'publié',
                'avg_rating' => 4.50,
                'total_reviews' => 95,
            ],
            [
                'name' => 'Beurre de Karité Pur (1Kg)',
                'slug' => 'beurre-karite-pur',
                'description' => 'Beurre de karité non raffiné, 100% pur et biologique. Idéal pour la peau et les cheveux.',
                'price' => 4500,
                'stock' => 100,
                'category_id' => $beaute->id,
                'status' => 'publié',
                'is_featured' => true,
                'avg_rating' => 5.00,
                'total_reviews' => 210,
            ],
            [
                'name' => 'Jus d\'Ananas Pain de Sucre (Pack x6)',
                'slug' => 'jus-ananas-pack',
                'description' => 'Jus d\'ananas pain de sucre frais, pressé artisanalement à Allada. Sans sucre ajouté, 100% naturel.',
                'price' => 6000,
                'stock' => 40,
                'category_id' => $alim->id,
                'status' => 'publié',
                'avg_rating' => 4.70,
                'total_reviews' => 67,
            ],
        ];

        foreach ($produitsShop2 as $p) {
            $shop2->products()->create($p);
        }

        // --- Boutique 3 : Monteiro Fashion (Mode) ---
        $produitsShop3 = [
            [
                'name' => 'Sac en Cuir Artisanal',
                'slug' => 'sac-cuir-artisanal',
                'description' => 'Sac à main en cuir véritable, fabriqué à la main à Porto-Novo. Design unique inspiré des motifs Adinkra.',
                'price' => 35000,
                'stock' => 5,
                'category_id' => $mode->id,
                'status' => 'publié',
                'is_featured' => true,
                'avg_rating' => 4.90,
                'total_reviews' => 76,
            ],
            [
                'name' => 'Robe Wax Ankara Premium',
                'slug' => 'robe-wax-ankara',
                'description' => 'Robe longue en tissu wax authentique avec coupe moderne. Disponible en taille S à XL.',
                'price' => 25000,
                'stock' => 15,
                'category_id' => $mode->id,
                'status' => 'publié',
                'avg_rating' => 4.80,
                'total_reviews' => 54,
            ],
            [
                'name' => 'Chemise Homme Bogolan',
                'slug' => 'chemise-bogolan',
                'description' => 'Chemise pour homme en tissu bogolan du Mali, teint naturellement. Coupe slim élégante.',
                'price' => 18000,
                'stock' => 20,
                'category_id' => $mode->id,
                'status' => 'publié',
                'avg_rating' => 4.60,
                'total_reviews' => 38,
            ],
            [
                'name' => 'Sculpture Bronze Roi Ghézo',
                'slug' => 'sculpture-bronze-ghezo',
                'description' => 'Sculpture en bronze à la cire perdue du Roi Ghézo, tradition séculaire de la fonderie béninoise.',
                'price' => 120000,
                'stock' => 1,
                'category_id' => $art->id,
                'status' => 'publié',
                'is_featured' => true,
                'avg_rating' => 4.90,
                'total_reviews' => 23,
            ],
        ];

        foreach ($produitsShop3 as $p) {
            $shop3->products()->create($p);
        }

        $this->command->info('✅ Données de démonstration créées avec succès !');
        $this->command->info('   → 8 catégories');
        $this->command->info('   → 3 vendeurs + 3 boutiques');
        $this->command->info('   → 12 produits');
        $this->command->info('   → 1 administrateur (admin@beninmarket.bj / admin123)');
    }
}

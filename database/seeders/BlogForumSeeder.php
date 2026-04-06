<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ForumTopic;
use Illuminate\Database\Seeder;

class BlogForumSeeder extends Seeder
{
    public function run(): void
    {
        // ==============================
        // ARTICLES
        // ==============================
        $articles = [
            [
                'titre'       => 'Le Bronze d\'Abomey : Art Vivant du Royaume du Dahomey',
                'slug'        => 'bronze-abomey-art-vivant-royame-dahomey',
                'categorie'   => 'Patrimoine',
                'description' => 'Les maîtres fondeurs d\'Abomey perpétuent une tradition séculaire de la technique de la cire perdue. Découvrez cet art fascinant inscrit au patrimoine immatériel de l\'UNESCO.',
                'content'     => '<p>Depuis des siècles, les artisans d\'Abomey façonnent le bronze avec une maîtrise héritée des cours royales du Dahomey...</p><p>La technique de la cire perdue permet de créer des pièces uniques, chaque sculpture racontant une page de l\'histoire béninoise.</p>',
                'auteur'      => 'Équipe BéninMarket',
                'image'       => 'https://images.unsplash.com/photo-1618022325802-7e5e732d97a1?auto=format&fit=crop&q=80&w=800',
                'featured'    => true,
                'read_time'   => 5,
                'tags'        => 'patrimoine,artisanat,bronze,abomey',
            ],
            [
                'titre'       => 'Comment Choisir un Tissu Wax Authentique ?',
                'slug'        => 'choisir-tissu-wax-authentique',
                'categorie'   => 'Guide Achat',
                'description' => 'Le marché du wax est inondé de contrefaçons. Voici les critères clés pour identifier un tissu de qualité et soutenir les artisans locaux.',
                'content'     => '<p>Le vrai wax africain se distingue par sa vivacité des couleurs et la régularité de son motif des deux côtés du tissu...</p>',
                'auteur'      => 'Aïcha Monteiro',
                'image'       => 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&q=80&w=800',
                'featured'    => false,
                'read_time'   => 4,
                'tags'        => 'wax,mode,guide,textile',
            ],
            [
                'titre'       => 'Le Karité : Or Blanc du Bénin',
                'slug'        => 'karite-or-blanc-benin',
                'categorie'   => 'Beauté & Santé',
                'description' => 'Du champ à votre salle de bain : l\'itinéraire du beurre de karité béninois, de la récolte artisanale à l\'exportation mondiale.',
                'content'     => '<p>L\'arbre à karité, ou Vitellaria paradoxa, pousse naturellement dans la savane béninoise...</p>',
                'auteur'      => 'Koffi Zinsou',
                'image'       => 'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&q=80&w=800',
                'featured'    => false,
                'read_time'   => 3,
                'tags'        => 'karité,beauté,naturel,bénin',
            ],
            [
                'titre'       => 'Miel des Collines de Dassa : Un Terroir d\'Exception',
                'slug'        => 'miel-collines-dassa-terroir',
                'categorie'   => 'Alimentation',
                'description' => 'Les apiculteurs des collines de l\'Atacora produisent certains des miels les plus purs d\'Afrique de l\'Ouest. Rencontre avec Ibrahim Kaba, éleveur de 5e génération.',
                'content'     => '<p>À 450 mètres d\'altitude, les ruches d\'Ibrahim dominent un paysage préservé...</p>',
                'auteur'      => 'Ibrahim Kaba',
                'image'       => 'https://images.unsplash.com/photo-1622485521746-0ce71ae540c4?auto=format&fit=crop&q=80&w=800',
                'featured'    => true,
                'read_time'   => 4,
                'tags'        => 'miel,dassa,terroir,apiculture',
            ],
            [
                'titre'       => 'Ouvrir sa Boutique en Ligne au Bénin : Le Guide Complet',
                'slug'        => 'ouvrir-boutique-en-ligne-benin-guide',
                'categorie'   => 'E-commerce',
                'description' => 'Tout ce que vous devez savoir pour lancer votre activité en ligne sur BéninMarket : inscription, catalogue, livraison et paiement mobile.',
                'content'     => '<p>BéninMarket simplifie la création de votre e-boutique en 3 étapes...</p>',
                'auteur'      => 'Équipe BéninMarket',
                'image'       => 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?auto=format&fit=crop&q=80&w=800',
                'featured'    => false,
                'read_time'   => 6,
                'tags'        => 'e-commerce,vendeur,guide,boutique',
            ],
        ];

        foreach ($articles as $data) {
            Article::create($data);
        }

        // ==============================
        // FORUM TOPICS
        // ==============================
        $topics = [
            [
                'tag'          => 'technique',
                'auteur'       => 'Koffi_Sculpteur',
                'titre'        => 'Quel bois local est le meilleur pour commencer la sculpture ?',
                'description'  => 'Je débute dans la sculpture et je cherche un bois facile à travailler, disponible localement à Cotonou. Le bambou est-il une bonne option ?',
                'votes'        => 47,
                'commentaires' => 12,
            ],
            [
                'tag'          => 'patrimoine',
                'auteur'       => 'MariamBa',
                'titre'        => 'Les motifs Adinkra : signification et utilisation dans le tissu béninois',
                'description'  => 'Je prépare une collection inspirée des symboles Adinkra. Qui peut m\'aider à comprendre les significations pour éviter les erreurs culturelles ?',
                'image'        => 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&q=80&w=600',
                'votes'        => 89,
                'commentaires' => 24,
            ],
            [
                'tag'          => 'création',
                'auteur'       => 'ArtisanCotonou',
                'titre'        => 'Première collection de bijoux en bronze : vos retours ?',
                'description'  => 'Je viens de terminer mes 10 premières pièces de bijoux en bronze à la cire perdue. Je cherche des retours avant de les mettre en ligne.',
                'image'        => 'https://images.unsplash.com/photo-1618022325802-7e5e732d97a1?auto=format&fit=crop&q=80&w=600',
                'votes'        => 63,
                'commentaires' => 18,
            ],
            [
                'tag'          => 'technique',
                'auteur'       => 'Aicha_Fashion',
                'titre'        => 'Comment fixer les couleurs naturelles sur le bogolan ?',
                'description'  => 'Mes tissus bogolan perdent leur couleur après quelques lavages. Quelqu\'un a une technique pour fixer les teintures naturelles durablement ?',
                'votes'        => 34,
                'commentaires' => 9,
            ],
            [
                'tag'          => 'patrimoine',
                'auteur'       => 'HistoireDahomey',
                'titre'        => 'Le masque Gèlèdè : peut-on le vendre à des collectionneurs étrangers ?',
                'description'  => 'Je me pose des questions légales et éthiques sur la vente de répliques de masques Gèlèdè à l\'international. Y a-t-il des restrictions UNESCO ?',
                'votes'        => 112,
                'commentaires' => 31,
            ],
        ];

        foreach ($topics as $data) {
            ForumTopic::create($data);
        }

        $this->command->info('✅ Blog & Forum seedés avec succès !');
        $this->command->info('   → 5 articles (dont 2 featured)');
        $this->command->info('   → 5 sujets de forum');
    }
}

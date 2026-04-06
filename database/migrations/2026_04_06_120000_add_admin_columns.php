<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ajouter 'statut' à la table articles (publié/brouillon/en_attente)
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'statut')) {
                $table->string('statut')->default('publié')->after('featured');
            }
            if (!Schema::hasColumn('articles', 'views')) {
                $table->unsignedInteger('views')->default(0)->after('statut');
            }
        });

        // Ajouter is_reported, is_pinned aux forum_topics
        Schema::table('forum_topics', function (Blueprint $table) {
            if (!Schema::hasColumn('forum_topics', 'is_reported')) {
                $table->boolean('is_reported')->default(false)->after('commentaires');
            }
            if (!Schema::hasColumn('forum_topics', 'is_pinned')) {
                $table->boolean('is_pinned')->default(false)->after('is_reported');
            }
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumnIfExists('statut');
            $table->dropColumnIfExists('views');
        });

        Schema::table('forum_topics', function (Blueprint $table) {
            $table->dropColumnIfExists('is_reported');
            $table->dropColumnIfExists('is_pinned');
        });
    }
};

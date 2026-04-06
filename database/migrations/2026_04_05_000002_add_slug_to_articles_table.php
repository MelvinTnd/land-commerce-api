<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('titre');
            $table->text('content')->nullable()->after('description'); // contenu complet
            $table->integer('read_time')->default(3)->after('content');  // minutes de lecture
            $table->string('tags')->nullable()->after('read_time');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['slug', 'content', 'read_time', 'tags']);
        });
    }
};

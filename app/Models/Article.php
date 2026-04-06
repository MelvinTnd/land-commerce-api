<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = [
        'titre',
        'slug',
        'categorie',
        'description',
        'content',
        'auteur',
        'image',
        'featured',
        'statut',
        'views',
        'read_time',
        'tags',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'views'    => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $slug = Str::slug($article->titre);
                $original = $slug;
                $i = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $original . '-' . $i++;
                }
                $article->slug = $slug;
            }
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'titre', 'slug', 'categorie', 'description', 'content',
        'auteur', 'image', 'featured', 'read_time', 'tags',
    ];

    protected $casts = [
        'featured' => 'boolean',
    ];
}

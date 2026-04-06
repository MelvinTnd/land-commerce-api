<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumTopic extends Model
{
    protected $fillable = [
        'tag',
        'auteur',
        'titre',
        'description',
        'image',
        'votes',
        'commentaires',
        'is_reported',
        'is_pinned',
    ];

    protected $casts = [
        'votes'        => 'integer',
        'commentaires' => 'integer',
        'is_reported'  => 'boolean',
        'is_pinned'    => 'boolean',
    ];
}

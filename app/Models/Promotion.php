<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'titre',
        'description',
        'reduction',
        'image',
        'categorie',
        'date_debut',
        'date_fin',
        'actif',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin'   => 'date',
        'actif'      => 'boolean',
        'reduction'  => 'float',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre', 'description', 'reduction',
        'image', 'categorie', 'date_debut', 'date_fin', 'actif',
    ];

    protected $casts = [
        'actif'      => 'boolean',
        'reduction'  => 'float',
        'date_debut' => 'date',
        'date_fin'   => 'date',
    ];

    public function isExpired(): bool
    {
        return $this->date_fin->isPast();
    }

    public function isActive(): bool
    {
        return $this->actif && !$this->isExpired();
    }
}

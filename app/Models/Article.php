<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    // protected $fillable = ['reference', 'libelle', 'prix', 'quantite'];
    protected $fillable = ['libelle', 'reference', 'prix', 'quantite'];

    protected $hidden = ['created_at', 'updated_at'];

    public function dettes()
    {
        return $this->belongsToMany(Dette::class, 'article_dette')->withPivot('qteVente', 'prixVente');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    // protected $fillable = ['reference', 'libelle', 'prix', 'quantite'];
    protected $fillable = ['libelle', 'reference', 'prix', 'quantite'];




    public function scopeArticleFilter($query, $disponible)
    {
        if ($disponible === 'oui') {
            return $query->where('quantite', '>', 0);
        } elseif ($disponible === 'non') {
            return $query->where('quantite', '=', 0);
        }
        return $query;
    }
    protected $hidden = ['created_at', 'updated_at'];

    // public function dettes()
    // {
    //     return $this->belongsToMany(Dette::class, 'article_dette')->withPivot('qteVente', 'prixVente');
    // }

    public function dettes()
    {
        return $this->belongsToMany(Dette::class, 'article_dette')->withPivot('qteVente', 'prixVente');
    }


}

<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class Dette extends Model
// {
//     use HasFactory;

//     protected $fillable = ['montantTotal', 'montantRestant', 'client_id'];

//     protected $hidden = ['updated_at'];

//     public function client()
//     {
//         return $this->belongsTo(Client::class);
//     }

//     public function articles()
//     {
//         return $this->belongsToMany(Article::class, 'article_dette')->withPivot('qteVente', 'prixVente');
//     }
// }





namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Observers\DetteObserver;


class Dette extends Model
{
    use HasFactory;

    protected $fillable = ['montant', 'montantRestant', 'client_id'];

    protected   static function boot()
    {
        parent::boot();
        self::observe(DetteObserver::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_dette')->withPivot('qteVente', 'prixVente');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    // Calcul du montant restant
    public function getMontantRestantAttribute()
    {
        $montantDu = $this->paiements->sum('montant'); // Somme des paiements associés
        return $this->montant - $montantDu;
    }
}

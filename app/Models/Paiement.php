<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = ['montant', 'dette_id'];

    public function dette()
    {
        return $this->belongsTo(Dette::class);
    }

    // Accessor pour obtenir le montant total payé pour une dette
    public function getMontantDuAttribute()
    {
        return $this->dette->paiements->sum('montant');
    }
}


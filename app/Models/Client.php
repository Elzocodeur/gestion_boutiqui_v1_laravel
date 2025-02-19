<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Observers\UserObserver;

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['surname','telephone', 'adresse', 'user_id'];

    protected $hidden = ['created_at', 'updated_at'];




    public function user()
    {
        return $this->belongsTo(User::class);
    }

    function dettes() {
        return $this->hasMany(Dette::class);
    }


        // Ajoutez la méthode booted ici
        protected static function booted()
        {
            static::addGlobalScope(new ClientFilter);
        }

}

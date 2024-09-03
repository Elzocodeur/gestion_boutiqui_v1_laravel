<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ClientFilter implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // Filtering clients based on the 'comptes' query parameter
        if (request()->has('comptes')) {
            if (request()->input('comptes') === 'oui') {
                $builder->whereNotNull('user_id');
            } elseif (request()->input('comptes') === 'non') {
                $builder->whereNull('user_id');
            }
        }

        // Filtering clients based on the 'active' query parameter
        if (request()->has('active')) {
            if (request()->input('active') === 'oui') {
                $builder->whereHas('user', function ($q) {
                    $q->where('active', 'OUI');
                });
            } elseif (request()->input('active') === 'non') {
                $builder->whereHas('user', function ($q) {
                    $q->where('active', 'NON');
                });
            }
        }
    }
}


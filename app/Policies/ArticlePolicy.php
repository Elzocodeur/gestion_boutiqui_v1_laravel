<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function viewAny(User $user)
    {
        return $user->role->nomRole === 'BOUTIQUIER';
    }

    public function view(User $user, Article $article)
    {
        return $user->role->nomRole === 'BOUTIQUIER';
    }

    public function create(User $user)
    {
        return $user->role->nomRole === 'BOUTIQUIER';
    }

    // public function update(User $user, Article $article)
    // {
    //     return $user->role->nomRole === 'BOUTIQUIER';
    // }

    public function update(User $user, Article $article)
{
    return $user->role->nomRole === 'BOUTIQUIER' ;
}

    public function delete(User $user, Article $article)
    {
        return $user->role->nomRole === 'BOUTIQUIER';
    }

    public function updateStock(User $user)
    {
        return $user->role->nomRole === 'BOUTIQUIER';
    }

    public function updateStockById(User $user)
    {
        return $user->role->nomRole === 'BOUTIQUIER';
    }

}


<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    /**
     * Vérifie si l'utilisateur peut afficher la liste des articles.
     */
    public function viewAny(User $user)
    {
        return $user->role->name === 'BOUTIQUIER' ;
    }

    /**
     * Vérifie si l'utilisateur peut afficher un article spécifique.
     */
    public function view(User $user, Article $article)
    {
        return $user->role->name === 'BOUTIQUIER';
    }

    /**
     * Vérifie si l'utilisateur peut créer un article.
     */
    public function create(User $user)
    {
        return $user->role->name === 'BOUTIQUIER';
    }

    /**
     * Vérifie si l'utilisateur peut mettre à jour un article.
     */
    public function update(User $user, Article $article)
    {
        return $user->role->name === 'BOUTIQUIER';
    }

    /**
     * Vérifie si l'utilisateur peut supprimer un article.
     */
    public function delete(User $user, Article $article)
    {
        return $user->role->name === 'BOUTIQUIER';
    }

    /**
     * Vérifie si l'utilisateur peut mettre à jour le stock d'un article.
     */
    public function updateStock(User $user)
    {
        return $user->role->name === 'BOUTIQUIER';
    }

    public function updateStockById(User $user)
    {
        return $user->role->name === 'BOUTIQUIER';
    }
}


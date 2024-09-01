<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClientPolicy
{
    public function viewAny(User $user)
    {
        return $user->role->name === 'BOUTIQUIER' || $user->role->name === 'ADMIN';
    }

    /**
     * Vérifie si l'utilisateur peut afficher un article spécifique.
     */
    public function view(User $user, Client $client)
    {
        return $user->role->name === 'BOUTIQUIER' || $user->role->name === 'ADMIN';
    }

    /**
     * Vérifie si l'utilisateur peut créer un article.
     */
    public function create(User $user)
    {
        return $user->role->name === 'BOUTIQUIER' || $user->role->name === 'CLIENT';
    }

    /**
     * Vérifie si l'utilisateur peut mettre à jour un article.
     */
    public function update(User $user, Client $client)
    {
        return $user->role->name === 'BOUTIQUIER' || $user->role->name === 'CLIENT';
    }

    /**
     * Vérifie si l'utilisateur peut supprimer un article.
     */
    public function delete(User $user, Client $client)
    {
        return $user->role->name === 'BOUTIQUIER' || $user->role->name === 'CLIENT';
    }

    public function showClientByTelephone(User $user)
    {

        return $user->role->name === 'BOUTIQUIER' || $user->role->name === 'CLIENT';
    }

    public function addUserToClient(User $user, Client $client)
    {

        return $user->role->name === 'BOUTIQUIER' || $user->role->name === 'ADMIN';
    }

    public function listDettesClient(User $user, Client $client)
    {
        return $user->role->name === 'BOUTIQUIER' || $user->role->name === 'CLIENT';
    }

    public function showClientWithUser(User $user, Client $client)
    {
        return $user->role->name === 'BOUTIQUIER' || $user->role->name === 'CLIENT';
    }
}

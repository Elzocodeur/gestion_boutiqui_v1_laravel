<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClientPolicy
{
    public function viewAny(User $user)
    {
        return $user->role->nomRole === 'BOUTIQUIER' || $user->role->nomRole === 'CLIENT';
    }

    /**
     * Vérifie si l'utilisateur peut afficher un article spécifique.
     */
    public function view(User $user, Client $client)
    {
        return $user->role->nomRole === 'BOUTIQUIER' || $user->role->nomRole === 'CLIENT' ;
    }

    /**
     * Vérifie si l'utilisateur peut créer un article.
     */
    public function create(User $user)
    {
        return $user->role->nomRole === 'BOUTIQUIER' || $user->role->nomRole === 'CLIENT';
    }

    /**
     * Vérifie si l'utilisateur peut mettre à jour un article.
     */
    public function update(User $user, Client $client)
    {
        return $user->role->nomRole === 'BOUTIQUIER' || $user->role->nomRole === 'CLIENT';
    }

    /**
     * Vérifie si l'utilisateur peut supprimer un article.
     */
    public function delete(User $user, Client $client)
    {
        return $user->role->nomRole === 'BOUTIQUIER' || $user->role->nomRole === 'CLIENT';
    }

    public function showClientByTelephone(User $user)
    {

        return $user->role->nomRole === 'BOUTIQUIER' || $user->role->nomRole === 'CLIENT';
    }

    public function addUserToClient(User $user, Client $client)
    {

        return $user->role->nomRole === 'BOUTIQUIER' || $user->role->nomRole === 'CLIENT';
    }

    public function listDettesClient(User $user, Client $client)
    {
        return $user->role->nomRole === 'BOUTIQUIER' || $user->role->nomRole === 'CLIENT';
    }

    public function showClientWithUser(User $user, Client $client)
    {
        return $user->role->nomRole === 'BOUTIQUIER' || $user->role->nomRole === 'CLIENT';
    }
}

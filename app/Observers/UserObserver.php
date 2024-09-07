<?php

namespace App\Observers;

use App\Models\User;
use App\Events\UserCreated;

class UserObserver
{
    public function created(User $user)
    {
        // Déclencher l'événement après la création d'un utilisateur
        event(new UserCreated($user));
    }
}


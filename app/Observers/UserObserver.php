<?php
    // namespace App\Observers;

    // use App\Models\User;
    // use App\Events\UserCreated;
    // use Illuminate\Support\Facades\Log;
    // use Illuminate\Http\File;

    // class UserObserver
    // {
    //     public function created(User $user)
    //     {
    //         Log::info('User créé : ');

    //         // Passer la photo correctement au job
    //         $photoPath = $user->photo;
    //         // dd($photoPath);
    //         event(new UserCreated($user, $photoPath));
    //         Log::info('User créé et Event déclenché.');

    //     }

    // }




        // release
        // public function created(User $user): void
        // {
        //     event(new UserCreated($user));
        // }



        namespace App\Observers;

use App\Models\User;
use App\Events\UserCreated;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    public function created(User $user)
    {
        Log::info('Utilisateur créé, déclenchement de l\'événement UserCreated.');

        $photoPath = $user->photo;
        event(new UserCreated($user, $photoPath)); // Déclenchement de l'événement avec la photo
    }
}

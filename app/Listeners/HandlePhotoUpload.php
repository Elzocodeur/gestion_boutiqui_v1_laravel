<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\UploadPhotoToCloudinary;
use App\Events\UserCreatedEvent;
use App\Models\User;

class HandlePhotoUpload
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserCreatedEvent $event)
    {
        // Récupérer l'utilisateur via l'ID
        $user = User::find($event->userId);

        if ($user) {
            // Lancer le job asynchrone pour uploader la photo et générer le QR code, PDF et envoyer l'email
            UploadPhotoToCloudinary::dispatch($user);
        }
    }
}

<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Jobs\UploadPhotoJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\File;



class UploadUserPhotoListener
{
    public function handle(UserCreated $event)
    {
        Log::info('Event reçu pour l\'utilisateur pour photo cloudinary  avant dispatch: ' . $event->user->id);

        $user = $event->user;
        $photoPath = $event->photoPath;

        UploadPhotoJob::dispatch($user,  $photoPath );
        Log::info('Event reçu pour l\'utilisateur  et envoie de job photo sur cloudinary : ');
        // dd(UploadPhotoJob::dispatch($user,  $photoPath ));


    }
}


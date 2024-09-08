<?php
// app/Listeners/HandlePhotoUpload.php
namespace App\Listeners;

use App\Events\PhotoUploadEvent;
use App\Jobs\UploadPhotoToCloudinary;

class HandlePhotoUpload
{
    public function handle(PhotoUploadEvent $event)
    {
        $userData = $event->userData;

        // Lancer le job asynchrone pour uploader la photo
        UploadPhotoToCloudinary::dispatch($userData, $userData['user_id']);
    }
}


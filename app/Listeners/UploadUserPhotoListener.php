<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Jobs\UploadPhotoJob;


// class UploadUserPhotoListener
// {
//     public function handle(UserCreated $event)
//     {
//         $user = $event->user;

//         if (request()->hasFile('photo')) {
//             $photo = request()->file('photo');
//             UploadPhotoJob::dispatch($user, $photo);
//         }
//     }
// }

class UploadUserPhotoListener
{
    public function handle(UserCreated $event)
    {
        $user = $event->user;

        if (request()->hasFile('photo')) {
            $photo = request()->file('photo');
            UploadPhotoJob::dispatch($user->id, $photo);
        }
    }
}


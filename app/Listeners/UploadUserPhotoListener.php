<?php

// namespace App\Listeners;

// use App\Events\UserCreated;
// use App\Jobs\UploadPhotoJob;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Http\File;



// class UploadUserPhotoListener
// {
//     public function handle(UserCreated $event)
//     {

//         log::info("listener uploadPhotoListener  : ");


//         $user = $event->user;
//         $photoPath = $event->photoPath;

//         UploadPhotoJob::dispatch($user,  $photoPath );

//         log::info("listener uploadPhotoListener   call job: ");

//         // dd(UploadPhotoJob::dispatch($user,  $photoPath ));


//     }







    // public function handle(UserCreated $event)
    // {
    //     $user = $event->user;

    //     if (request()->hasFile('photo')) {
    //         $photo = request()->file('photo');
    //         UploadPhotoJob::dispatch($user->id, $photo);
    //     }
    // }
// }




namespace App\Listeners;

use App\Events\UserCreated;
use App\Jobs\UploadPhotoJob;
use Illuminate\Support\Facades\Log;

class UploadUserPhotoListener
{
    public function handle(UserCreated $event)
    {
        Log::info("Listener appelé: UploadPhotoJob pour l'utilisateur.");

        $user = $event->user;
        $photoPath = $event->photoPath;

        // Dispatcher le job pour l'upload de la photo
        UploadPhotoJob::dispatch($user, $photoPath);

        Log::info("Listener: Job UploadPhotoJob dispatché.");
    }
}


















// namespace App\Listeners;

// use App\Events\ClientEvent;
// use App\Events\UserCreated;
// use App\Jobs\UploadPhotoJob;
// use Exception;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Support\Facades\Log;

// class UploadUserPhotoListener
// {
//     /**
//      * Create the event listener.
//      */
//     public function __construct()
//     {
//         //
//     }

//     /**
//      * Handle the event.
//      */
//     public function handle(UserCreated $event): void
//     {
//         if(isset($event->user)){
//             UploadPhotoJob::dispatch($event->user);
//         }
//     }
// }

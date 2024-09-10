<?php

// namespace App\Listeners;

// use App\Events\ClientEvent;
// use App\Events\UserCreated;
// use App\Jobs\SendLoyaltyCardJob;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Support\Facades\Log;

// class SendLoyaltyCardListener
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
//         log::info("listener SendLoyaltyCardJob  : ");

//         SendLoyaltyCardJob::dispatch($event->user);

//         log::info("listener SendLoyaltyCardJob   call job: ");
//     }

// }



    // public function handle(UserCreated $event)
    // {
    //     $user = $event->user;
    //     $client = $user->client;

    //     if ($client) {
    //         SendLoyaltyCardJob::dispatch($user, $client);
    //     }
    // }




    namespace App\Listeners;

use App\Events\UserCreated;
use App\Jobs\SendLoyaltyCardJob;
use Illuminate\Support\Facades\Log;

class SendLoyaltyCardListener
{
    public function handle(UserCreated $event)
    {
        Log::info("Listener appelé: SendLoyaltyCardJob pour l'utilisateur.");

        SendLoyaltyCardJob::dispatch($event->user);

        Log::info("Listener: Job SendLoyaltyCardJob dispatché.");
    }
}

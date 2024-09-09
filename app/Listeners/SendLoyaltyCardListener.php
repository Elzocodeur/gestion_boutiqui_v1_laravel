<?php

namespace App\Listeners;

use App\Events\ClientEvent;
use App\Events\UserCreated;
use App\Jobs\SendLoyaltyCardJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendLoyaltyCardListener
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
    // public function handle(UserCreated $event): void
    // {
    //     SendLoyaltyCardJob::dispatch($event->user);
    // }

    public function handle(UserCreated $event)
    {
        $user = $event->user;
        $client = $user->client;

        if ($client) {
            SendLoyaltyCardJob::dispatch($user, $client);
        }
    }
}




























// namespace App\Listeners;

// use App\Events\ClientEvent;
// use App\Events\UserCreated;
// use App\Jobs\SendLoyaltyCardJob;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Queue\InteractsWithQueue;

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
//         SendLoyaltyCardJob::dispatch($event->user);
//     }
// }


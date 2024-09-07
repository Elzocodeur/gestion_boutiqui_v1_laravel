<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Jobs\SendLoyaltyCardJob;



// class SendLoyaltyCardListener
// {
//     public function handle(UserCreated $event)
//     {
//         $user = $event->user;
//         $client = $user->client;

//         if ($client) {
//             SendLoyaltyCardJob::dispatch($user, $client);
//         }
//     }
// }

class SendLoyaltyCardListener
{
    public function handle(UserCreated $event)
    {
        $user = $event->user;
        $client = $user->client;

        if ($client) {
            SendLoyaltyCardJob::dispatch($user, $client);
        }
    }
}


<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Jobs\SendLoyaltyCardJob;
use Illuminate\Support\Facades\Log;


// class SendLoyaltyCardListener
// {
//     public function handle(UserCreated $event)
//     {
//         $user = $event->user;
//         $client = $user->client;

//         if ($client) {
//             SendLoyaltyCardJob::dispatch($user, $client); 772642240
//         }
//     }
// }


class SendLoyaltyCardListener
{
    public function handle(UserCreated $event)
    {
        Log::info('Event reçu pour l\'utilisateur pour mail  avant dispatch: ' . $event->user->id);

        $user = $event->user;
        $client = $user->client;
        // dd($client);

        if ($client) {
            SendLoyaltyCardJob::dispatch($user, $client);
        }
        // SendLoyaltyCardJob::dispatch($event->user, $client->user->client);

        Log::info('Event requé pour l\'utilisateur et envoie de job  pour mail aprés dispatch: ' . $event->user->id);
    }
}


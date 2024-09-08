<?php
namespace App\Jobs;

use App\Models\User;
use App\Models\Client;
use App\Services\MailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class SendLoyaltyCardJob  implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // protected $user;
    // protected $client;

    // public function __construct(User $user, Client $client)
    // {
    //     $this->user = $user;
    //     $this->client = $client;
    // }

    // public function handle(MailService $mailService)
    // {
    //     $mailService->sendLoyaltyCard($this->user, $this->client);
    // }


    protected $user;
    protected $client;

    public function __construct(User $user, Client $client)
    {
        $this->user = $user;
        $this->client = $client;
    }

    public function handle(MailService $mailService)
    {
        Log::info("Job SendLoyaltyCardJob pour l'utilisateur : " . $this->user->id);
        $mailService->sendLoyaltyCard($this->user, $this->client);
    }
}

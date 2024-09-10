<?php
// namespace App\Jobs;

// use App\Models\User;
// use App\Models\Client;
// use App\Services\MailService;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Bus\Queueable;
// use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Queue\SerializesModels;
// use Illuminate\Support\Facades\Log;


// class SendLoyaltyCardJob  implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
//     protected $user;
//     protected $client;

//     public function __construct(User $user, Client $client)
//     {
//         $this->user = $user;
//         $this->client = $client;
//     }

//     public function handle(MailService $mailService)
//     {
//         Log::info("Job SendLoyaltyCardJob pour l'utilisateur : " . $this->user->id);
//         $mailService->sendLoyaltyCard($this->user, $this->client);
//     }
// }






// namespace App\Jobs;

// use App\Models\User;
// use App\Models\Client;
// use App\Services\MailService;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Bus\Queueable;
// use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Queue\SerializesModels;
// use Illuminate\Support\Facades\Log;

// class SendLoyaltyCardJob implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//     protected $user;
//     protected $client;

//     public function __construct(User $user, Client $client)
//     {
//         $this->user = $user;
//         $this->client = $client;
//     }

//     public function handle(MailService $mailService)
//     {
//         Log::info("Job SendLoyaltyCardJob pour l'utilisateur : " . $this->user->id);
//         $mailService->sendLoyaltyCard($this->user, $this->client);
//     }
// }






namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoyaltyCardMail;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;

class SendLoyaltyCardJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle()
{

        Log::info("Envoi de la carte de fidélité pour l'utilisateur : {$this->user->id}");

        // Générer le QR code
        $qrData = json_encode([
            'nom' => $this->user->nom,
            'prenom' => $this->user->prenom,
            'login' => $this->user->login,
            'telephone' => $this->user->client->telephone,
            'adresse' => $this->user->client->adresse,
        ]);

        // Génération du QR code en SVG
        $renderer = new ImageRenderer(new RendererStyle(400), new SvgImageBackEnd());
        $writer = new Writer($renderer);
        $qrCodeContent = $writer->writeString($qrData);
        $monQrcode = 'data:image/svg+xml;base64,' . base64_encode($qrCodeContent);

        // Génération du PDF
        $html = view('pdf.loyalty_card', ['user' => $this->user, 'monQrcode' => $monQrcode])->render();
        $pdfPath = 'loyalty_cards/' . $this->user->login . '.pdf';
        $mpdf = new Mpdf();
        $pdfContent = $mpdf->Output($pdfPath, 'S');

        // Envoi de l'email avec la carte de fidélité
        Mail::to($this->user->login)->send(new LoyaltyCardMail($this->user, $pdfPath, $pdfContent));

        Log::info("Email de carte de fidélité envoyé à {$this->user->login}");

}

}


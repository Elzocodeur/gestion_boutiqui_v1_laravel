<?php

namespace App\Observers;

use App\Models\User;
use App\Mail\LoyaltyCardMail;
use Illuminate\Support\Facades\Mail;
use Mpdf\Mpdf;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Log;
use App\Events\UserCreatedEvent;

class UserObserver
{
    // public function created(User $user)
    // {
    //     // Récupérer le client associé à l'utilisateur
    //     $client = $user->client;

    //     // Vérifier si le client existe
    //     if (!$client) {
    //         Log::error('Client non trouvé pour l\'utilisateur ID : ' . $user->id);
    //         return;
    //     }

    //     try {
    //         // Générer les données pour le QR code
    //         $qrData = json_encode([
    //             'nom' => $user->nom,
    //             'prenom' => $user->prenom,
    //             'login' => $user->login,
    //             'telephone' => $client->telephone,
    //             'adresse' => $client->adresse,
    //         ]);

    //         // Générer le QR code
    //         $renderer = new ImageRenderer(
    //             new RendererStyle(400),
    //             new SvgImageBackEnd()
    //         );
    //         $writer = new Writer($renderer);
    //         $qrCodeContent = $writer->writeString($qrData);
    //         $monQrcode = 'data:image/svg+xml;base64,' . base64_encode($qrCodeContent);

    //         // Générer le contenu du PDF
    //         $html = view('pdf.loyalty_card', compact('user', 'monQrcode'))->render();

    //         // Générer le PDF
    //         $pdfPath = 'loyalty_cards/' . $user->login . '.pdf';
    //         $mpdf = new Mpdf();
    //         $mpdf->WriteHTML($html);

    //         // Sauvegarder le PDF
    //         $pdfContent = $mpdf->Output($pdfPath, 'S');

    //         // Envoyer l'email avec la carte de fidélité en pièce jointe
    //         Mail::to($user->login)->send(new LoyaltyCardMail($user, $pdfPath, $pdfContent));

    //     } catch (\Exception $e) {
    //         Log::error('Erreur lors de la génération du QR code, PDF ou envoi de l\'email pour l\'utilisateur ID : ' . $user->id . '. Message: ' . $e->getMessage());
    //     }
    // }


    /**
     * Handle the User "updated" event.
     */


    public function created(User $user)
    {
        // Déclencher l'événement qui lance le job pour générer le QR code, le PDF et envoyer l'email
        event(new UserCreatedEvent($user->id));
    }

    
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}

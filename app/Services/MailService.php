<?php
namespace App\Services;

use App\Mail\LoyaltyCardMail;
use App\Models\Client;
use App\Models\User;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Mail;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;


class MailService
{
    public function sendLoyaltyCard(User $user, Client $client)
    {
        // Générer le QR code
        $qrData = json_encode([
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'login' => $user->login,
            'telephone' => $client->telephone,
            'adresse' => $client->adresse,
        ]);

        $renderer = new ImageRenderer(new RendererStyle(400), new SvgImageBackEnd());
        $writer = new Writer($renderer);
        $qrCodeContent = $writer->writeString($qrData);
        $monQrcode = 'data:image/svg+xml;base64,' . base64_encode($qrCodeContent);

        // Génération du PDF
        $html = view('pdf.loyalty_card', compact('user', 'monQrcode'))->render();
        $pdfPath = 'loyalty_cards/' . $user->login . '.pdf';
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $pdfContent = $mpdf->Output($pdfPath, 'S');

        // Envoi de l'email avec la carte de fidélité
        Mail::to($user->login)->send(new LoyaltyCardMail($user, $pdfPath, $pdfContent));
    }
}


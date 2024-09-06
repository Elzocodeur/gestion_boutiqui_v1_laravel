<?php

// app/Jobs/UploadPhotoToCloudinary.php
// namespace App\Jobs;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable; // Import du trait Dispatchable
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UploadPhotoToCloudinary implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels; // Utilisation du trait Dispatchable

    public $userData;
    public $userId;

    public function __construct($userData, $userId)
    {
        $this->userData = $userData;
        $this->userId = $userId;
    }

    public function handle()
    {
        try {
            // Upload vers Cloudinary
            $uploadedFileUrl = Cloudinary::upload($this->userData['photo']->getRealPath())->getSecurePath();
            // Mettre à jour l'utilisateur avec l'URL de la photo Cloudinary
            $user = User::find($this->userId);
            $user->photo = $uploadedFileUrl;
            $user->save();
        } catch (\Exception $e) {
            // Si l'upload Cloudinary échoue, sauvegarder en local
            $localPath = $this->userData['photo']->store('user_images', 'public');
            $user = User::find($this->userId);
            $user->photo = Storage::url($localPath);
            $user->save();
        }
    }
}








// app/Jobs/GenerateQrCodeAndSendEmail.php
// namespace App\Jobs;

// use App\Models\User;
// use App\Mail\LoyaltyCardMail;
// use Illuminate\Support\Facades\Mail;
// use Mpdf\Mpdf;
// use BaconQrCode\Renderer\ImageRenderer;
// use BaconQrCode\Renderer\RendererStyle\RendererStyle;
// use BaconQrCode\Renderer\Image\SvgImageBackEnd;
// use BaconQrCode\Writer;
// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Queue\SerializesModels;
// use Illuminate\Support\Facades\Log;
// use App\Events\UserCreatedEvent;
// use App\Exceptions\ExceptionService;
// use App\Services\ClientServiceImplement;



// use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
// use Illuminate\Support\Facades\Storage;

// class UploadPhotoToCloudinary implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//     protected $userId;
//     // public $userData;
//     protected $photoPath;


//     public function __construct($userId,  $photoPath)
//     {
//         $this->userId = $userId;
//         // $this->userData = $userData;
//         $this->$photoPath= $photoPath;
//         dd($this->$photoPath );
//     }

//     public function handle()
//     {
//         dd($this->photoPath );
//         Log::info('Client non trouvé pour l\'utilisateur ID : ');
//         $user = User::find($this->userId);
//         $client = $user->client;

//         if (!$client) {
//             return;
//         }

//         //     // Récupérer le client associé à l'utilisateur
//         //     $client = $user->client;

//         //     // Vérifier si le client existe
//         if (!$client) {
//             Log::error('Client non trouvé pour l\'utilisateur ID : ' . $user->id);
//             return;
//         }

//         try {
//             // Générer les données pour le QR code
//             $qrData = json_encode([
//                 'nom' => $user->nom,
//                 'prenom' => $user->prenom,
//                 'login' => $user->login,
//                 'telephone' => $client->telephone,
//                 'adresse' => $client->adresse,
//             ]);

//             // Générer le QR code
//             $renderer = new ImageRenderer(
//                 new RendererStyle(400),
//                 new SvgImageBackEnd()
//             );
//             $writer = new Writer($renderer);
//             $qrCodeContent = $writer->writeString($qrData);
//             $monQrcode = 'data:image/svg+xml;base64,' . base64_encode($qrCodeContent);

//             // Générer le contenu du PDF
//             $html = view('pdf.loyalty_card', compact('user', 'monQrcode'))->render();

//             // Générer le PDF
//             $pdfPath = 'loyalty_cards/' . $user->login . '.pdf';
//             $mpdf = new Mpdf();
//             $mpdf->WriteHTML($html);

//             // Sauvegarder le PDF
//             $pdfContent = $mpdf->Output($pdfPath, 'S');

//             // Envoyer l'email avec la carte de fidélité en pièce jointe
//             Mail::to($user->login)->send(new LoyaltyCardMail($user, $pdfPath, $pdfContent));
//             // Si une photo est présente, tenter l'upload sur Cloudinary
//             if ($user->photo ) {
//                 // dd( file_exists($user->photo) );

//                 try {
//                     dd( $this->photoPath);
//                     $uploadedFileUrl = Cloudinary::upload(storage_path('app/public/' . $this->photoPath))->getSecurePath();

//                     // Mettre à jour l'utilisateur avec l'URL de la photo Cloudinary
//                     $user->photo = $uploadedFileUrl;
//                     $user->save();
//                 } catch (\Exception $e) {
//                     $user->save();
//                 }
//             }
//         } catch (\Exception $e) {
//             Log::error('Erreur lors de la génération du QR code, PDF ou envoi de l\'email pour l\'utilisateur ID : ' . $user->id . '. Message: ' . $e->getMessage());
//         }
//     }
// }

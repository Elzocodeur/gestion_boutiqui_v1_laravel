<?php

// app/Services/ClientServiceImplement.php
namespace App\Services;

use App\Repositories\ClientRepository;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;
use App\Services\UploadService;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;
use App\Mail\LoyaltyCardMail;
use Illuminate\Support\Facades\Mail;
use TCPDF;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;
use Mpdf\Mpdf;
use App\Events\UserCreatedEvent;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Event;
use App\Exceptions\ExceptionService;
use App\Jobs\UploadPhotoToCloudinary;



class ClientServiceImplement implements ClientService
{
    protected $clientRepository;
    protected $uploadService;

    public function __construct(ClientRepository $clientRepository, UploadService $uploadService)
    {
        $this->clientRepository = $clientRepository;
        $this->uploadService = $uploadService;
    }

    public function createClient(array $data)
    {
        return $this->clientRepository->create($data);
    }

    public function updateClient($id, array $data)
    {
        return $this->clientRepository->update($id, $data);
    }

    public function deleteClient($id)
    {
        return $this->clientRepository->delete($id);
    }

    public function getClientById($id)
    {
        return $this->clientRepository->findById($id);
    }



    public function addUserToClient(array $userData, $clientId)
    {
        try {
            $client = $this->getClientById($clientId);
            if (!$client) {
                throw new ExceptionService('Client non trouvé');
            }

            if ($client->user_id) {
                throw new ExceptionService('Le client a déjà un compte utilisateur');
            }

            // Initialiser la variable pour le chemin de la photo
            $photoPath = true;
            $user = User::create([
                'nom' => $userData['nom'],
                'prenom' => $userData['prenom'],
                'login' => $userData['login'],
                'password' => Hash::make($userData['password']),
                'photo' => $photoPath, // sera mis à jour plus tard
                'role_id' => $userData['role_id'],
            ]);

            // Associer l'utilisateur au client
            $client->user_id = $user->id;
            $client->save();

            // Si une photo est présente, tenter l'upload sur Cloudinary
            if (isset($userData['photo']) && $userData['photo'] instanceof \Illuminate\Http\UploadedFile) {
                try {
                    // Upload de la photo sur Cloudinary
                    $uploadedFileUrl = Cloudinary::upload($userData['photo']->getRealPath())->getSecurePath();

                    // Mettre à jour le chemin de la photo dans la base de données (lien Cloudinary)
                    $user->photo = $uploadedFileUrl;

                } catch (ExceptionService $e) {
                    // En cas d'échec de Cloudinary, enregistrer la photo en local
                    $localPath = $userData['photo']->store('user_images', 'public');

                    // Mettre à jour le chemin local dans la base de données
                    // $user->photo = Storage::disk('public')->url($localPath); // URL locale
                }

                // Sauvegarder l'utilisateur avec le chemin de la photo mis à jour
                $user->save();
            }

            Event::dispatch('eloquent.created: ' . User::class, $user);



            // logui deplacer dans le l'observateur

            // Générer le QR code et la carte de fidélité comme avant
            $qrData = json_encode([
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'login' => $user->login,
                'telephone' => $client->telephone,
                'adresse' => $client->adresse,
            ]);

            $renderer = new ImageRenderer(
                new RendererStyle(400),
                new SvgImageBackEnd()
            );

            $writer = new Writer($renderer);
            $qrCodeContent = $writer->writeString($qrData);
            $monQrcode = 'data:image/svg+xml;base64,' . base64_encode($qrCodeContent);

            $html = view('pdf.loyalty_card', compact('user', 'monQrcode'))->render();

            $pdfPath = 'loyalty_cards/' . $user->login . '.pdf';
            $mpdf = new Mpdf();
            $mpdf->WriteHTML($html);

            $pdfContent = $mpdf->Output($pdfPath, 'S');

            // Envoyer l'email avec la carte de fidélité en pièce jointe
            Mail::to($user->login)->send(new LoyaltyCardMail($user, $pdfPath, $pdfContent));

            return $client;

        } catch (ExceptionService $e) {
            throw $e;
        }
    }







//     public function addUserToClient(array $userData, $clientId)
// {
//     try {

//         $client = $this->getClientById($clientId);
//         if (!$client) {
//             throw new ExceptionService('Client non trouvé');
//         }

//         if ($client->user_id) {
//             throw new ExceptionService('Le client a déjà un compte utilisateur');
//         }

//         $photoPath = true;
//         // Création du compte utilisateur
//         $user = User::create([
//             'nom' => $userData['nom'],
//             'prenom' => $userData['prenom'],
//             'login' => $userData['login'],
//             'password' => Hash::make($userData['password']),
//             'photo' => $photoPath, // La photo sera mise à jour plus tard
//             'role_id' => $userData['role_id'],
//         ]);

//         // Association de l'utilisateur au client
//         $client->user_id = $user->id;
//         $client->save();
//         // methode 2
//         if (isset($userData['photo']) && $userData['photo'] instanceof \Illuminate\Http\UploadedFile) {
//             // Stocker la photo localement
//             $storedPhotoPath = $userData['photo']->store('user_images_temp', 'public');

//             // Dispatch the job for uploading photo
//             UploadPhotoToCloudinary::dispatch($user->id,$storedPhotoPath);
//             dd($storedPhotoPath);

//         }

//         // Déclencher un événement pour générer le QR code, PDF, et envoyer l'email
//         event(new UserCreatedEvent($user->id));

//         return $client;

//     } catch (ExceptionService $e) {
//         throw $e;
//     }
// }



    public function getClientWithUser($id)
    {
        return $this->clientRepository->findById($id)->load('user:id,nom,prenom,login,photo');
    }


    public function getClientByTelephone(string $telephone): ?Client
    {
        return $this->clientRepository->findByTelephone($telephone);
    }

    public function createUserForClient(int $clientId, array $userData): Client
    {
        $client = $this->clientRepository->findById($clientId);
        if ($client && !$client->user_id) {
            return $this->clientRepository->addUserToClient($client, $userData);
        }
        throw new \Exception('Client already has a user or not found.');
    }

    public function getClientDettes(int $clientId): Collection
    {
        return $this->clientRepository->listDettesClient($clientId);
    }

    public function getAllClients(): Collection
    {
        return $this->clientRepository->getAll();
    }

}

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
use App\Events\PhotoUploadEvent;
use App\Enums\StatusResponseEnum;







class ClientServiceImplement implements ClientService
{
    protected $clientRepository;
    protected $uploadService;
    protected $photoService;
    protected $mailService;

    public function __construct(ClientRepository $clientRepository, UploadService $uploadService, PhotoService $photoService, MailService $mailService)
    {
        $this->clientRepository = $clientRepository;
        $this->uploadService = $uploadService;
        $this->photoService = $photoService;
        $this->mailService = $mailService;
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








    // debut-------------
    public function addUserToClient(array $userData, $clientId)



    {
        try {
            $client = $this->getClientById($clientId);
            if (!$client) {
                throw new Exception('Client non trouvé');
            }

            if ($client->user_id) {
                throw new Exception('Le client a déjà un compte utilisateur');
            }

            // Création du compte utilisateur
            // $photopaph = true;
            $user = User::create([
                'nom' => $userData['nom'],
                'prenom' => $userData['prenom'],
                'login' => $userData['login'],
                'password' => Hash::make($userData['password']),
                'photo' => '',
                'role_id' => $userData['role_id'],
            ]);



            // Association de l'utilisateur au client
            $client->user_id = $user->id;
            $client->save();

            // Gérer l'upload de la photo
            if (isset($userData['photo']) && $userData['photo'] instanceof \Illuminate\Http\UploadedFile) {
                $this->photoService->uploadPhoto($user, $userData['photo']);
            }

            // Génération du QR code et de la carte de fidélité
            $this->mailService->sendLoyaltyCard($user, $client);


            return $client;
        } catch (Exception $e) {
            throw new ExceptionService('Erreur lors de l\'ajout de l\'utilisateur au client: ' . $e->getMessage());
        }
    }

    // private function handlePhotoUpload(User $user, $photo)
    // {
    //     try {
    //         $uploadedFileUrl = Cloudinary::upload($photo->getRealPath())->getSecurePath();
    //         $user->photo = $uploadedFileUrl;
    //         $user->is_photo_on_cloudinary = true;
    //     } catch (Exception $e) {
    //         // Sauvegarde en local en cas d'échec
    //         $localPath = $photo->store('user_images', 'public');
    //         $user->photo = $localPath;
    //         $user->is_photo_on_cloudinary = false;
    //     }

    //     $user->save();
    // }




    // private function generateLoyaltyCard(User $user, Client $client)
    // {
    //     // Générer le QR code
    //     $qrData = json_encode([
    //         'nom' => $user->nom,
    //         'prenom' => $user->prenom,
    //         'login' => $user->login,
    //         'telephone' => $client->telephone,
    //         'adresse' => $client->adresse,
    //     ]);

    //     $renderer = new ImageRenderer(new RendererStyle(400), new SvgImageBackEnd());
    //     $writer = new Writer($renderer);
    //     $qrCodeContent = $writer->writeString($qrData);
    //     $monQrcode = 'data:image/svg+xml;base64,' . base64_encode($qrCodeContent);

    //     // Génération du PDF
    //     $html = view('pdf.loyalty_card', compact('user', 'monQrcode'))->render();
    //     $pdfPath = 'loyalty_cards/' . $user->login . '.pdf';
    //     $mpdf = new Mpdf();
    //     $mpdf->WriteHTML($html);
    //     $pdfContent = $mpdf->Output($pdfPath, 'S');

    //     // Envoi de l'email avec la carte de fidélité
    //     Mail::to($user->login)->send(new LoyaltyCardMail($user, $pdfPath, $pdfContent));
    // }
    // fin-------------




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




    public function storeClientWithUser(array $data)
    {
        DB::beginTransaction();
        try {
            // Création du client
            $client = $this->createClient($data);

            // Ajout de l'utilisateur associé, si les données de l'utilisateur sont présentes
            if (isset($data['user'])) {
                $client = $this->addUserToClient($data['user'], $client->id);
            }

            DB::commit();
            return [
                'status' => StatusResponseEnum::SUCCESS,
                'client' => $client,
                'message' => 'Client et utilisateur créés avec succès'
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'status' => StatusResponseEnum::ECHEC,
                'message' => $e->getMessage()
            ];
        }
    }
}

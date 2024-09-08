<?php
namespace App\Services;

use App\Repositories\ClientRepository;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;
use App\Services\UploadService;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;
use App\Exceptions\ExceptionService;
use App\Enums\StatusResponseEnum;


class ClientServiceImplement implements ClientService
{
    protected $clientRepository;
    protected $uploadService;
    // protected $photoService;
    // protected $mailService;

    public function __construct(ClientRepository $clientRepository, UploadService $uploadService) //, PhotoService $photoService, MailService $mailService
    {
        $this->clientRepository = $clientRepository;
        $this->uploadService = $uploadService;
        // $this->photoService = $photoService;
        // $this->mailService = $mailService;
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
                    // public function addUserToClient(array $userData, $clientId)

                    // {
                    //     try {
                    //         $client = $this->getClientById($clientId);
                    //         if (!$client) {
                    //             throw new Exception('Client non trouvé');
                    //         }

                    //         if ($client->user_id) {
                    //             throw new Exception('Le client a déjà un compte utilisateur');
                    //         }

                    //         // Création du compte utilisateur
                    //         $photopaph = true;
                    //         $user = User::create([
                    //             'nom' => $userData['nom'],
                    //             'prenom' => $userData['prenom'],
                    //             'login' => $userData['login'],
                    //             'password' => Hash::make($userData['password']),
                    //             'photo' => $userData,
                    //             'role_id' => $userData['role_id'],
                    //         ]);

                    //         // Association de l'utilisateur au client
                    //         $client->user_id = $user->id;
                    //         $client->save();

                    //         // Gérer l'upload de la photo
                    //         // if (isset($userData['photo']) && $userData['photo'] instanceof \Illuminate\Http\UploadedFile) {
                    //         //     $this->photoService->uploadPhoto($user, $userData['photo']);
                    //         // }

                    //         // // Génération du QR code et de la carte de fidélité
                    //         // $this->mailService->sendLoyaltyCard($user, $client);


                    //         return $client;
                    //     } catch (Exception $e) {
                    //         throw new ExceptionService('Erreur lors de l\'ajout de l\'utilisateur au client: ' . $e->getMessage());
                    //     }
                    // }


                //     public function addUserToClient(array $userData, $clientId)
                // {
                //     try {
                //         $client = $this->getClientById($clientId);
                //         if (!$client) {
                //             throw new Exception('Client non trouvé');
                //         }

                //         if ($client->user_id) {
                //             throw new Exception('Le client a déjà un compte utilisateur');
                //         }

                //         // Création du compte utilisateur
                //         $photoPath = is_string($userData['photo']) ? $userData['photo'] : $userData['photo']->getRealPath(); // Conversion de la photo en chemin

                //         $user = User::create([
                //             'nom' => $userData['nom'],
                //             'prenom' => $userData['prenom'],
                //             'login' => $userData['login'],
                //             'password' => Hash::make($userData['password']),
                //             'photo' => $photoPath, // Assurez-vous que la photo est bien une chaîne de caractères
                //             'role_id' => $userData['role_id'],
                //         ]);

                //         // Association de l'utilisateur au client
                //         $client->user_id = $user->id;
                //         $client->save();

                //         return $client;
                //     } catch (Exception $e) {
                //         throw new ExceptionService('Erreur lors de l\'ajout de l\'utilisateur au client: ' . $e->getMessage());
                //     }
                // }

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

                    // Sauvegarder la photo localement si elle est un fichier
                    if ($userData['photo'] instanceof \Illuminate\Http\UploadedFile) {
                        $photoPath = $userData['photo']->store('temp_photos');
                    } else {
                        $photoPath = $userData['photo']; // Si c'est déjà une chaîne de caractères
                    }

                    // Création du compte utilisateur
                    $user = User::create([
                        'nom' => $userData['nom'],
                        'prenom' => $userData['prenom'],
                        'login' => $userData['login'],
                        'password' => Hash::make($userData['password']),
                        'photo' => $photoPath, // Stockage du chemin
                        // dd($photoPath),
                        'role_id' => $userData['role_id'],
                    ]);

                    // Association de l'utilisateur au client
                    $client->user_id = $user->id;
                    $client->save();

                    // Retourner la photo pour le job
                    return ['client' => $client, 'photo' => $userData['photo']];
                } catch (Exception $e) {
                    throw new ExceptionService('Erreur lors de l\'ajout de l\'utilisateur au client: ' . $e->getMessage());
                }
            }

            

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
        throw new ExceptionService('Client already has a user or not found.');
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

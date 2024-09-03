<?php


// app/Services/ClientServiceImplement.php
namespace App\Services;

use App\Repositories\ClientRepository;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

class ClientServiceImplement implements ClientService
{
    protected $clientRepository;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
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
                throw new Exception('Client not found');
            }

            if ($client->user_id) {
                throw new Exception('Client already has a user account');
            }

            $user = User::create([
                'nom' => $userData['nom'],
                'prenom' => $userData['prenom'],
                'login' => $userData['login'],
                'password' => Hash::make($userData['password']),
                'photo' => $userData['photo'],
                'role_id' => $userData['role_id'],
            ]);

            $client->user_id = $user->id;
            $client->save();

            return $client;

        } catch (Exception $e) {
            throw $e;
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


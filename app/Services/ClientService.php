<?php

namespace App\Services;
use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

interface ClientService
{
    public function createClient(array $data);
    public function updateClient($id, array $data);
    public function deleteClient($id);
    public function getClientById($id);
    public function addUserToClient(array $userData, $clientId);

    public function getClientByTelephone(string $telephone): ?Client;
    public function createUserForClient(int $clientId, array $userData): Client;
    public function getClientDettes(int $clientId): Collection;
    public function getClientWithUser(int $clientId);
    public function getAllClients(): Collection;
}

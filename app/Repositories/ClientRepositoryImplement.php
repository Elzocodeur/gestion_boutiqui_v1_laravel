<?php

// app/Repositories/ClientRepositoryImplement.php
namespace App\Repositories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

class ClientRepositoryImplement implements ClientRepository
{
    public function create(array $data)
    {
        return Client::create($data);
    }

    public function find($id)
    {
        return Client::find($id);
    }

    public function update($id, array $data)
    {
        $client = $this->find($id);
        if ($client) {
            $client->update($data);
            return $client;
        }
        return null;
    }

    public function delete($id)
    {
        $client = $this->find($id);
        if ($client) {
            return $client->delete();
        }
        return false;
    }

    public function findById($id)
    {
        return Client::find($id);
    }

    public function findByTelephone(string $telephone): ?Client
    {
        return Client::where('telephone', $telephone)->with('user:id,nom,prenom,login,photo')->first();
    }

    public function addUserToClient(Client $client, array $userData): Client
    {
        $user = $client->user()->create($userData);
        $client->user_id = $user->id;
        $client->save();

        return $client;
    }

    public function listDettesClient(int $id): Collection
    {
        $client = Client::with('dettes')->find($id);
        return $client ? $client->dettes : collect([]);
    }

    public function getAll(): Collection
    {
        return Client::all();
    }
}

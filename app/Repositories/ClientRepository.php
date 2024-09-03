<?php
// app/Repositories/ClientRepository.php
namespace App\Repositories;
use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;
interface ClientRepository
{
    public function create(array $data);
    public function find($id);
    public function update($id, array $data);
    public function delete($id);
    public function findById($id);

    public function findByTelephone(string $telephone): ?Client;
    public function addUserToClient(Client $client, array $userData): Client;
    public function listDettesClient(int $id): Collection;
    public function getAll(): Collection;

}

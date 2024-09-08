<?php
namespace App\Repositories;

use App\Models\Dette;
use Illuminate\Support\Facades\DB;


class DetteRepositoryImplement implements DetteRepository
{
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Création de la dette avec les informations validées
            $dette = Dette::create([
                'client_id' => $data['clientId'],
                'montant' => 0, // Initialisé à 0, sera calculé par l'observer
                'montantRestant' => 0
            ]);
            return $dette;
        });
    }

    public function find(int $id)
    {
        return Dette::findOrFail($id);
    }

    public function update(int $id, array $data)
    {
        $dette = $this->find($id);
        $dette->update($data);

        return $dette;
    }

    public function delete(int $id)
    {
        $dette = $this->find($id);
        $dette->delete();
    }

    public function findById(int $id)
    {
        return Dette::findOrFail($id);
    }
}

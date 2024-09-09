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

    public function getAllDettes(array $filters)
    {
        $query = Dette::query();

        // Utiliser les scopes pour filtrer selon le statut
        if (isset($filters['statut'])) {
            if ($filters['statut'] === 'Solde') {
                $query->solde(); // Scope pour les dettes soldées
            } elseif ($filters['statut'] === 'NonSolde') {
                $query->nonSolde(); // Scope pour les dettes non soldées
            }
        }

        return $query->with('client')->get(); // Retourne les dettes avec les clients associés
    }


    // Lister les articles d'une dette
    public function getDetteArticles(int $detteId)
    {
        $dette = $this->find($detteId);
        return $dette->articles;
    }

    // Lister les paiements  d'une dette
    public function getPaiements(int $detteId)
    {
        $dette = $this->find($detteId);
        return $dette->paiements;
    }


    // Ajouter un paiementn Mettre a jour les montant due et restant de la dette,Le montant entree est  numerique ,positive et inferieur ou egal au montant restant
    public function addPaiement(int $detteId, float $montant)
{
    $dette = $this->find($detteId);

    // Vérifie que le montant est positif et inférieur ou égal au montant restant
    if ($montant <= 0 || $montant > $dette->montantRestant) {
        throw new \Exception('Le montant doit être positif et inférieur ou égal au montant restant');
    }

    // Mettre à jour les montants dans la dette
    $dette->update([
        'montantRestant' => $dette->montantRestant - $montant,
        // 'montant' => $dette->montant + $montant
    ]);

    // Ajouter le paiement
    $dette->paiements()->create([
        'montant' => $montant,
        // Ajoute d'autres champs si nécessaire
    ]);
    return $dette;
}



}

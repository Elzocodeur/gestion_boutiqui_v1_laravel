<?php

namespace App\Observers;

use App\Models\Article;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Dette;

class DetteObserver
{
    public function creating(Dette $dette)
    {
        $request = request();
        $montantTotal = 0;  // Initialiser le montant total de la dette

        // Calcul du montant total de la dette
        foreach ($request->input('articles') as $articleData) {
            // Vérification et validation des stocks
            $article = Article::findOrFail($articleData['articleId']);
            if ($article->quantite < $articleData['qteVente']) {
                throw new \Exception('Quantité en stock insuffisante pour l\'article : ' . $article->libelle);
            }

            // Calcul du montant total
            $montantTotal += $articleData['qteVente'] * $articleData['prixVente'];
        }

        // Mise à jour du montant dans la dette
        $dette->montant = $montantTotal;
        $dette->montantRestant = $montantTotal;
    }

    public function created(Dette $dette)
    {
        DB::transaction(function () use ($dette) {
            $request = request();

            // Enregistrement des articles dans la table article_dette
            foreach ($request->input('articles') as $articleData) {
                $article = Article::findOrFail($articleData['articleId']);

                // Mise à jour des stocks
                $article->decrement('quantite', $articleData['qteVente']);

                // Insertion dans la table article_dette
                DB::table('article_dette')->insert([
                    'article_id' => $articleData['articleId'],
                    'dette_id' => $dette->id,
                    'qteVente' => $articleData['qteVente'],
                    'prixVente' => $articleData['prixVente'],
                ]);
            }

            // Enregistrement du paiement s'il existe
            if ($request->filled('paiement.montant')) {
                $paiement = Paiement::create([
                    'montant' => $request->input('paiement.montant'),
                    'dette_id' => $dette->id,
                ]);

                // Mise à jour du montant restant après paiement
                $dette->montantRestant = $dette->montant - $paiement->montant;
                $dette->save();  // Sauvegarder la mise à jour
            }
        });

        Log::info('Dette créée avec succès : ' . $dette->id);
    }
}





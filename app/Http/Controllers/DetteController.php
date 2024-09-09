<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDetteRequest;
use App\Http\Resources\DetteResource;
use App\Repositories\DetteRepository; // Utiliser le repository pour créer la dette
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DetteController extends Controller
{
    protected $detteRepository;

    public function __construct(DetteRepository $detteRepository)
    {
        $this->detteRepository = $detteRepository;
    }

    public function store(StoreDetteRequest $request): JsonResponse
    {
        // Appel au repository pour la création de la dette
        $dette = $this->detteRepository->create($request->validated());

        return response()->json([
            'data' => new DetteResource($dette),
            'message' => 'Dette enregistrée avec succès',
            'status' => 'SUCCESS',
        ], 201);
    }



        // Liste des dettes avec filtres
        public function index(Request $request): JsonResponse
        {
            $filters = $request->only('statut'); // Filtrer par statut

            $dettes = $this->detteRepository->getAllDettes($filters);

            if ($dettes->isEmpty()) {
                return response()->json([
                    'data' => null,
                    'message' => 'Pas de dettes',
                    'status' => 'ERROR',
                ], 200);
            }

            return response()->json([
                'data' => DetteResource::collection($dettes),
                'message' => 'Liste des dettes',
                'status' => 'SUCCESS',
            ], 200);
        }




        public function show(int $id): JsonResponse
        {
            $dette = $this->detteRepository->findById($id);
            return response()->json([
                'data' => new DetteResource($dette),
                'message' => 'Dette',
                'status' => 'SUCCESS',
            ], 200);
        }


        public function destroy(int $id): JsonResponse
        {
            $this->detteRepository->delete($id);
            return response()->json([
                'data' => null,
                'message' => 'Dette supprimée',
                'status' => 'SUCCESS',
            ], 200);
        }


        public function showArticlesDettes(int $detteId): JsonResponse
        {
            $dette = $this->detteRepository->getDetteArticles($detteId);
            return response()->json([
                'data' => $dette,
                'message' => 'Articles de la dette',
                'status' => 'SUCCESS',
            ], 200);
        }

        // lister les paiements d'une dette
        public function showPaiements(int $detteId): JsonResponse
        {
            $dette = $this->detteRepository->getPaiements($detteId);
            return response()->json([
                'data' => $dette,
                'message' => 'Paiements de la dette',
                'status' => 'SUCCESS',
            ], 200);
        }

    // Ajouter un paiementn Mettre a jour les montant due et restant de la dette,Le montant entree est  numerique ,positive et inferieur ou egal au montant restant
    public function storePaiement(int $detteId, Request $request): JsonResponse
    {
        // Valide que le montant est présent et qu'il est numérique et positif
        $validatedData = $request->validate([
            'montant' => 'required|numeric|min:0.01',  // Le montant doit être positif
        ]);

        // Appelle le repository avec le montant
        $dette = $this->detteRepository->addPaiement($detteId, $validatedData['montant']);

        return response()->json([
            'data' => $dette,
            'message' => 'Paiement ajouté avec succès',
            'status' => 'SUCCESS',
        ], 200);
    }



}

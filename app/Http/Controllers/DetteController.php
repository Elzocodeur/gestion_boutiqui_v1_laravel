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
}

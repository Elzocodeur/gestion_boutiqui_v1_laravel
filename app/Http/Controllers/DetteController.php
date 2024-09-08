<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDetteRequest;
use App\Http\Resources\DetteResource;
use App\Repositories\DetteRepository; // Utiliser le repository pour créer la dette
use Illuminate\Http\JsonResponse;

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
}
    
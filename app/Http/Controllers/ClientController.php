<?php

namespace App\Http\Controllers;

use App\Facades\ClientServiceFacade;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientCompteRequest;
use App\Http\Resources\ClientCollection;
use App\Http\Resources\ClientResource;
use App\Traits\RestResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Enums\StatusResponseEnum;
use App\Models\Client;
use Exception;

class ClientController extends Controller
{
    use RestResponseTrait;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Client::class);
        $clients = ClientServiceFacade::getAllClients(); // Assume a method in ClientServiceFacade
        return new ClientCollection($clients);
    }

    public function store(StoreClientRequest $request)
    {
        $this->authorize('create', Client::class);

        try {
            DB::beginTransaction();

            $client = ClientServiceFacade::createClient($request->only('surname', 'adresse', 'telephone'));

            if ($request->has('nom') && $request->has('prenom') && $request->has('login') && $request->has('password')) {
                ClientServiceFacade::addUserToClient($request->only('nom', 'prenom', 'login', 'password', 'photo', 'role_id'), $client->id);
            }

            DB::commit();
            return $this->sendResponse(new ClientResource($client), StatusResponseEnum::SUCCESS, 'Client créé avec succès', 201);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendResponse(['error' => $e->getMessage()], StatusResponseEnum::ECHEC, 'Erreur lors de la création du client', 500);
        }
    }

    public function show(string $id)
    {
        $client = ClientServiceFacade::getClientWithUser($id);
        $this->authorize('view', $client);

        return new ClientResource($client);
    }

    public function showClientByTelephone(Request $request)
    {
        $this->authorize('viewAny', Client::class);

        $validated = $request->validate([
            'telephone' => 'required|string|size:9',
        ]);

        $client = ClientServiceFacade::getClientByTelephone($validated['telephone']);

        return $client
            ? new ClientResource($client)
            : $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Client non trouvé', 404);
    }

    public function addUserToClient(UpdateClientCompteRequest $request, $id)
    {
        $this->authorize('create', Client::class);

        $client = ClientServiceFacade::createUserForClient($id, $request->validated());

        return $this->sendResponse(new ClientResource($client), StatusResponseEnum::SUCCESS, 'Compte utilisateur ajouté avec succès au client.');
    }

    public function listDettesClient($id)
    {
        $this->authorize('view', Client::class);

        $dettes = ClientServiceFacade::getClientDettes($id);

        return $this->sendResponse($dettes, StatusResponseEnum::SUCCESS, 'Liste des dettes récupérée avec succès');
    }

    public function showClientWithUser($id)
    {
        $this->authorize('view', Client::class);

        $client = ClientServiceFacade::getClientWithUser($id);

        return $client
            ? $this->sendResponse(new ClientResource($client), StatusResponseEnum::SUCCESS, 'Informations du client récupérées avec succès')
            : $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Client non trouvé', 404);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Resources\ClientCollection;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\User;
use App\Traits\RestResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//  use mysql_xdevapi\Exception;
use Spatie\QueryBuilder\QueryBuilder;
use App\Enums\StatusResponseEnum;
use App\Http\Requests\UpdateClientCompteRequest;
use Illuminate\Support\Facades\Hash;


use Exception;

class ClientController extends Controller
{
    use RestResponseTrait;


    public function __construct()
    {
        $this->authorizeResource(Client::class, 'client');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Client::class);
        $query = Client::query();

        // Filtrer les clients avec ou sans compte
        if ($request->has('comptes')) {
            if ($request->input('comptes') === 'oui') {
                $query->whereNotNull('user_id');
            } elseif ($request->input('comptes') === 'non') {
                $query->whereNull('user_id');
            }
        }

        // Filtrer les clients par état d'activation du compte
        if ($request->has('active')) {
            if ($request->input('active') === 'oui') {
                $query->whereHas('user', function ($q) {
                    $q->where('active', 'OUI');
                });
            } elseif ($request->input('active') === 'non') {
                $query->whereHas('user', function ($q) {
                    $q->where('active', 'NON');
                });
            }
        }

        // Toujours inclure les informations de l'utilisateur si disponibles
        $query->with('user:id,nom,prenom,login,photo,active');

        // Appliquer les autres filtres
        $clients = QueryBuilder::for($query)
            ->allowedFilters(['surname'])
            ->get();

        // Retourner les clients sous forme de collection de ressources
        return new ClientCollection($clients);
    }


    public function store(StoreClientRequest $request)
    {
        $this->authorize('create', Client::class);

        try {
            DB::beginTransaction();

            // Créer le client avec les informations de base
            $clientData = $request->only('surname', 'adresse', 'telephone');
            $client = Client::create($clientData);

            // Vérifier si des informations utilisateur sont fournies pour créer un compte utilisateur
            if ($request->has('nom') && $request->has('prenom') && $request->has('login') && $request->has('password')) {
                $user = User::create([
                    'nom' => $request->input('nom'),
                    'prenom' => $request->input('prenom'),
                    'login' => $request->input('login'),
                    'password' => bcrypt($request->input('password')),  // Hachage du mot de passe
                    'role_id' => $request->input('role_id'),
                    'photo' => $request->input('photo'),
                ]);

                // Associer l'utilisateur au client
                $client->user_id = $user->id;
                $client->save();
            }

            DB::commit();

            // Appeler la méthode sendResponse avec les bons types d'arguments
            return $this->sendResponse(new ClientResource($client), StatusResponseEnum::SUCCESS, 'Client créé avec succès', 201);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendResponse(['error' => $e->getMessage()], StatusResponseEnum::ECHEC, 'Erreur lors de la création du client', 500);
        }
    }
    // }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $client = Client::find($id);
        $this->authorize('view', $client);

        return new ClientResource($client);
    }

    public function showClientByTelephone(Request $request)
    {
        $this->authorize('viewAny', Client::class);


        $validated = $request->validate([
            'telephone' => 'required|string|size:9',
        ]);

        $client = Client::where('telephone', $validated['telephone'])->with('user:id,nom,prenom,login,photo')->first();

        if ($client) {
            return new ClientResource($client);
        } else {
            return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Client non trouvé', 404);
        }
    }



    public function addUserToClient(UpdateClientCompteRequest $request, $id)
    {
        $this->authorize('create', Client::class);
        try {
            // Retrieve the client by ID
            $client = Client::findOrFail($id);

            // Check if the client already has a user account
            if ($client->user_id) {
                return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Ce client a déjà un compte utilisateur.', 400);
            }

            // Create the user account based on the validated data
            $user = User::create([
                'nom' => $request->input('nom'),
                'prenom' => $request->input('prenom'),
                'login' => $request->input('login'),
                'password' => Hash::make($request->input('password')),
                'photo' => $request->input('photo'),
                'role_id' => $request->input('role_id'),
            ]);

            // Assign the user to the client
            $client->user_id = $user->id;
            $client->save();

            // Return the updated client data
            return $this->sendResponse(new ClientResource($client), StatusResponseEnum::SUCCESS, 'Compte utilisateur ajouté avec succès au client.');
        } catch (Exception $e) {
            // Handle any errors
            return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Une erreur est survenue lors de l\'ajout du compte utilisateur au client.', 500);
        }
    }



    public function listDettesClient($id)
    {

        // Retrieve the client by ID
        $client = Client::with('dettes')->find($id);
        $this->authorize('view', $client);


        if (!$client) {
            return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Client non trouvé', 404);
        }

        // Get the client's debts (dettes)
        $dettes = $client->dettes;

        return $this->sendResponse($dettes, StatusResponseEnum::SUCCESS, 'Liste des dettes récupérée avec succès');
    }




    public function showClientWithUser($id)
    {
        // Récupérer le client avec ses informations utilisateur
        $client = Client::with('user:id,nom,prenom,login,photo')->find($id);
        $this->authorize('view', $client);

        if (!$client) {
            return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Client non trouvé', 404);
        }

        // Retourner le client et ses informations utilisateur
        return $this->sendResponse(new ClientResource($client), StatusResponseEnum::SUCCESS, 'Informations du client récupérées avec succès');
    }

}

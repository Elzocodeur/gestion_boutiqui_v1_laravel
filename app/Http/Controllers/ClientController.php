<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Resources\ClientCollection;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\User;
use App\Traits\RestResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        /**
     * @OA\Get(
     *     path="/api/v1/clients",
     *     summary="Liste des clients",
     *     description="Récupère la liste des clients, avec possibilité de filtrer par la présence d'un compte utilisateur et par l'état d'activation du compte.",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="comptes",
     *         in="query",
     *         description="Filtrer par présence d'un compte utilisateur (oui/non)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"oui", "non"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="active",
     *         in="query",
     *         description="Filtrer par état d'activation du compte (oui/non)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"oui", "non"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des clients récupérée avec succès",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé"
     *     )
     * )
     */
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


        /**
     * @OA\Post(
     *     path="/api/v1/clients",
     *     summary="Créer un client",
     *     description="Crée un nouveau client avec la possibilité d'associer un compte utilisateur.",
     *     tags={"Clients"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Client créé avec succès",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur lors de la création du client"
     *     )
     * )
     */
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

            return $this->sendResponse(new ClientResource($client), StatusResponseEnum::SUCCESS, 'Client créé avec succès', 201);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendResponse(['error' => $e->getMessage()], StatusResponseEnum::ECHEC, 'Erreur lors de la création du client', 500);
        }
    }


        /**
     * @OA\Get(
     *     path="/api/v1/clients/{id}",
     *     summary="Afficher un client",
     *     description="Récupère les informations d'un client par son ID.",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du client",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Informations du client récupérées avec succès",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client non trouvé"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé"
     *     )
     * )
     */
    public function show(string $id)
    {
        $client = Client::find($id);
        $this->authorize('view', $client);

        return new ClientResource($client);
    }


            /**
     * @OA\Post(
     *     path="/api/v1/clients/telephone/{telephone}",
     *     summary="Afficher un client par téléphone",
     *     description="Récupère les informations d'un client en fonction de son numéro de téléphone.",
     *     tags={"Clients"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Informations du client récupérées avec succès",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client non trouvé"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé"
     *     )
     * )
     */
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


            /**
     * @OA\Patch(
     *     path="/api/v1/clients/{id}/add-user",
     *     summary="Ajouter un compte utilisateur à un client",
     *     description="Associe un compte utilisateur à un client existant.",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du client",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Compte utilisateur ajouté avec succès au client",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Le client a déjà un compte utilisateur"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur lors de l'ajout du compte utilisateur"
     *     )
     * )
     */

    public function addUserToClient(UpdateClientCompteRequest $request, $id)
    {
        $this->authorize('create', Client::class);
        try {
            $client = Client::findOrFail($id);

            if ($client->user_id) {
                return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Ce client a déjà un compte utilisateur.', 400);
            }

            $user = User::create([
                'nom' => $request->input('nom'),
                'prenom' => $request->input('prenom'),
                'login' => $request->input('login'),
                'password' => Hash::make($request->input('password')),
                'photo' => $request->input('photo'),
                'role_id' => $request->input('role_id'),
            ]);

            $client->user_id = $user->id;
            $client->save();

            return $this->sendResponse(new ClientResource($client), StatusResponseEnum::SUCCESS, 'Compte utilisateur ajouté avec succès au client.');
        } catch (Exception $e) {
            return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Une erreur est survenue lors de l\'ajout du compte utilisateur au client.', 500);
        }
    }


        /**
     * @OA\Get(
     *     path="/api/v1/clients/{id}/dettes",
     *     summary="Lister les dettes d'un client",
     *     description="Récupère la liste des dettes d'un client par son ID.",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du client",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des dettes récupérée avec succès",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client non trouvé"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé"
     *     )
     * )
     */

    public function listDettesClient($id)
    {
        $client = Client::with('dettes')->find($id);
        $this->authorize('view', $client);

        if (!$client) {
            return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Client non trouvé', 404);
        }

        $dettes = $client->dettes;

        return $this->sendResponse($dettes, StatusResponseEnum::SUCCESS, 'Liste des dettes récupérée avec succès');
    }

        /**
     * @OA\Get(
     *     path="/api/v1/clients/{id}/withUser",
     *     summary="Afficher un client avec son compte utilisateur",
     *     description="Récupère les informations d'un client et les détails de son compte utilisateur associé.",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du client",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Informations du client récupérées avec succès",
     *         @OA\JsonContent(ref="ClientResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client non trouvé"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé"
     *     )
     * )
     */
    public function showClientWithUser($id)
    {
        $client = Client::with('user:id,nom,prenom,login,photo')->find($id);
        $this->authorize('view', $client);

        if (!$client) {
            return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Client non trouvé', 404);
        }

        return $this->sendResponse(new ClientResource($client), StatusResponseEnum::SUCCESS, 'Informations du client récupérées avec succès');
    }
}

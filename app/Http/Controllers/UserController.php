<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Traits\RestResponseTrait;
use App\Enums\StatusResponseEnum;
use Illuminate\Http\Request;





class UserController extends Controller
{
    use RestResponseTrait;

    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }



    /**
     * @OA\Get(
     *     path="/api/v1/users",
     *     operationId="GetAllUsersWithOptionalFilters",
     *     tags={"AllUsersWithOptionalFilters"},
     *     summary="Get all users with optional filters",
     *     description="Récupération des utilisateurs avec possibilité de filtrer par role et/ou par active",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="role",
     *         in="query",
     *         description="Filterer les utilisateurs par role (admin ou boutiquier)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"admin", "boutiquier"})
     *     ),
     *     @OA\Parameter(
     *         name="active",
     *         in="query",
     *         description="Filterer les utilisateurs par status (oui ou non)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"oui", "non"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Compte utilisateur créé avec succès",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);
        // Initialisation de la requête utilisateur
        $query = User::query();

        // Filtrer par rôle si le paramètre 'role' est présent dans la requête
        if ($request->has('role')) {
            $role = $request->input('role');
            $query->whereHas('role', function ($q) use ($role) {
                $q->where('nomRole', $role);
            });
        }

        // Filter by activation status
        if ($request->has('active')) {
            $active = strtolower($request->input('active')) === 'oui' ? 'OUI' : 'NON';
            $query->where('active', $active);
        }

        // Récupérer les utilisateurs avec leurs rôles
        $users = $query->with('role')->get();

        return $this->sendResponse(UserResource::collection($users), StatusResponseEnum::SUCCESS, 'Liste des utilisateurs récupérée avec succès');
    }

        /**
     * @OA\Post(
     *     path="/api/v1/users",
     *     tags={"Utilisateurs"},
     *     summary="Créer un nouvel utilisateur",
     *     description="Crée un nouvel utilisateur avec les données fournies dans la requête.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="StoreUserRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Utilisateur créé avec succès",
     *            @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès interdit"
     *     ),
     *     security={{"Bearer":{}}}
     * )
     */
    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);

        $validatedData = $request->validated();

        $user = User::create([
            'nom' => $validatedData['nom'],
            'prenom' => $validatedData['prenom'],
            'login' => $validatedData['login'],
            'photo' => $validatedData['photo'],
            'password' => bcrypt($validatedData['password']),
            'role_id' => $validatedData['role_id'],
        ]);

        return $this->sendResponse(new UserResource($user), StatusResponseEnum::SUCCESS, 'Utilisateur créé avec succès', 201);
    }


        /**
     * @OA\Get(
     *     path="/api/v1/users/{id}",
     *     tags={"Utilisateurs"},
     *     summary="Afficher un utilisateur spécifique",
     *     description="Récupère les informations d'un utilisateur spécifique par son ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'utilisateur",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur récupéré avec succès",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès interdit"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur non trouvé"
     *     ),
     *     security={{"Bearer":{}}}
     * )
     */
    public function show($id)
    {
        $user = User::with('role')->find($id);
        $this->authorize('view', $user);

        if (!$user) {
            return $this->sendResponse(null, StatusResponseEnum::ECHEC, 'Utilisateur non trouvé', 404);
        }

        return $this->sendResponse(new UserResource($user), StatusResponseEnum::SUCCESS, 'Utilisateur récupéré avec succès');
    }
}

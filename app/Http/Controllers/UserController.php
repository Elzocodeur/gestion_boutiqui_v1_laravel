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
                // $q->whereRaw('LOWER(nomRole) = ?', [$role]);
            });
        }

            // Filter by activation status
        if ($request->has('active')) {
            $active = strtolower($request->input('active')) === 'oui' ? 'OUI' : 'NON';
            $query->where('active', $active);
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
     * Store a newly created user in storage.
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
     * Display the specified user.
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

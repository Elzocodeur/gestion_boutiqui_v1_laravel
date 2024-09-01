<?php

namespace App\Http\Controllers;

use App\Enums\StatusResponseEnum;
use App\Http\Requests\RegistreRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Role;
use App\Traits\RestResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; //

class AuthController extends Controller
{

    use RestResponseTrait;

    // public function register(RegistreRequest $request)
    // {
    //     try {
    //         // Vérifier si le rôle est celui de "boutiquier"
    //         $role = Role::find($request->role_id);

    //         if (!$role || $role->nomRole !== 'boutiquier') {
    //             return $this->sendResponse(['error' => 'Seuls les utilisateurs de rôle "boutiquier" peuvent s\'enregistrer.'], StatusResponseEnum::ECHEC, 403);
    //         }

    //         // Créer l'utilisateur
    //         $user = User::create([
    //             'nom' => $request->nom,
    //             'prenom' => $request->prenom,
    //             'photo' => $request->photo,
    //             'login' => $request->login,
    //             'password' => Hash::make($request->password),
    //             'role_id' => $request->role_id,
    //             'active' => $request->active,
    //         ]);

    //         // Créer un jeton d'accès
    //         $token = $user->createToken('authToken')->accessToken;

    //         return $this->sendResponse(['user' => new UserResource($user), 'token' => $token], StatusResponseEnum::SUCCESS);
    //     } catch (Exception $e) {
    //         return $this->sendResponse(['error' => $e->getMessage()], StatusResponseEnum::ECHEC, 500);
    //     }
    // }


    public function register(RegistreRequest $request)
{
    try {
        if (!auth()->check()) {
            return $this->sendResponse(['error' => 'Utilisateur non authentifié.'], StatusResponseEnum::ECHEC, 401);
        }
        // Récupérer l'utilisateur connecté
        $currentUser = auth()->user();


        // Vérifier si l'utilisateur connecté a le rôle de "boutiquier"
        if (!$currentUser || $currentUser->role->nomRole !== 'BOUTIQUIER') {
            return $this->sendResponse(['error' => 'Seuls les utilisateurs de rôle "boutiquier" peuvent s\'enregistrer.'], StatusResponseEnum::ECHEC, 403);
        }


        // Créer l'utilisateur
        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'photo' => $request->photo,
            'login' => $request->login,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'active' => $request->active,
        ]);

        // Créer un jeton d'accès
        $token = $user->createToken('authToken')->accessToken;

        return $this->sendResponse(['user' => new UserResource($user), 'token' => $token], StatusResponseEnum::SUCCESS);
    } catch (Exception $e) {
        return $this->sendResponse(['error' => $e->getMessage()], StatusResponseEnum::ECHEC, 500);
    }
}



    public function login(Request $request)
    {
        $credentials = $request->only('login', 'password');

        if (Auth::attempt($credentials)) {
            // $user = Auth::user(); // Récupérer directement l'utilisateur authentifié

            $user = User::find(Auth::user()->id);

            // Créer un jeton d'accès
            $token = $user->createToken('appToken')->accessToken;

            // Créer un jeton de rafraîchissement
            $refreshToken = $user->createToken('refreshToken')->accessToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'refresh_token' => $refreshToken,
                'user' => new UserResource($user),
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Échec de l\'authentification.',
            ], 401);
        }
    }



    public function refreshToken(Request $request)
    {
        // Valider les données d'entrée
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // Authentifier l'utilisateur
        if (!Auth::attempt($request->only('login', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Identifiants invalides.'
            ], 401);
        }

        // Récupérer l'utilisateur authentifié
        //user = Auth::user();
        $user = User::find(Auth::user()->id);

        // Créer un nouveau jeton d'accès et un nouveau jeton de rafraîchissement
        $newAccessToken = $user->createToken('appToken')->accessToken;
        $newRefreshToken = $user->createToken('refreshToken')->accessToken;

        return response()->json([
            'success' => true,
            'token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
            'user' => new UserResource($user),
        ], 200);
    }




}

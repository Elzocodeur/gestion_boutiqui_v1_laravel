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


        /**
     * @OA\Post(
     *     path="/api/v1/auth/register",
     *     tags={"Authentification"},
     *     summary="Inscrire un nouvel utilisateur",
     *     description="Cette méthode permet d'inscrire un nouvel utilisateur avec les rôles appropriés.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="RegistreRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Utilisateur enregistré avec succès",
     *         @OA\JsonContent(ref="UserResource")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Utilisateur non authentifié"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès interdit, seul le rôle 'BOUTIQUIER' est autorisé"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur interne du serveur"
     *     ),
     *     security={{"Bearer":{}}}
     * )
     */
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


 /**
    * @OA\Post(
    *     path="/api/v1/auth/login",
    *     operationId="Login",
    *     tags={"Login"},
    *     summary="User Login",
    *     description="Connexion de l'utilisateur...",
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\MediaType(
    *            mediaType="multipart/form-data",
    *            @OA\Schema(
    *               type="object",
    *               required={"login", "password"},
    *               @OA\Property(property="login", type="string", example="khalilThree"),
    *               @OA\Property(property="password", type="string", example="Passer@123"),
    *            ),
    *        ),
    *        @OA\MediaType(
    *            mediaType="application/json",
    *            @OA\Schema(
    *               type="object",
    *               required={"login", "password"},
    *               @OA\Property(property="login", type="string", example="khalilThree"),
    *               @OA\Property(property="password", type="string", example="Passer@123"),
    *            ),
    *        ),
    *    ),
    *    @OA\Response(
    *        response=200,
    *        description="Connexion réussie",
    *        @OA\JsonContent()
    *    ),
    *    @OA\Response(
    *        response=422,
    *        description="Unprocessable Entity",
    *        @OA\JsonContent()
    *    ),
    *    @OA\Response(response=400, description="Bad request"),
    *    @OA\Response(response=404, description="Resource Not Found"),
    *    @OA\Response(response=500, description="Internal server error"),
    * )
    */
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



    // public function refreshToken(Request $request)
    // {
    //     // Valider les données d'entrée
    //     $request->validate([
    //         'login' => 'required|string',
    //         'password' => 'required|string',
    //     ]);

    //     // Authentifier l'utilisateur
    //     if (!Auth::attempt($request->only('login', 'password'))) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Identifiants invalides.'
    //         ], 401);
    //     }

    //     // Récupérer l'utilisateur authentifié
    //     //user = Auth::user();
    //     $user = User::find(Auth::user()->id);

    //     // Créer un nouveau jeton d'accès et un nouveau jeton de rafraîchissement
    //     $newAccessToken = $user->createToken('appToken')->accessToken;
    //     $newRefreshToken = $user->createToken('refreshToken')->accessToken;

    //     return response()->json([
    //         'success' => true,
    //         'token' => $newAccessToken,
    //         'refresh_token' => $newRefreshToken,
    //         'user' => new UserResource($user),
    //     ], 200);
    // }




            /**
     * @OA\Post(
     *     path="/api/v1/auth/logout",
     *     tags={"Authentification"},
     *     summary="Déconnexion de l'utilisateur",
     *     description="Cette méthode permet de déconnecter l'utilisateur et de supprimer tous les tokens associés.",
     *     @OA\Response(
     *         response=200,
     *         description="Déconnexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Déconnexion réussie. Vous devez vous reconnecter pour accéder à l'application.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Utilisateur non authentifié"
     *     ),
     *     security={{"Bearer":{}}}
     * )
     */
    public function logout(Request $request)
    {
        // Vérifier si l'utilisateur est authentifié
        if (Auth::check()) {
            // Récupérer l'utilisateur connecté
            $user = Auth::user();

            // Supprimer tous les tokens de l'utilisateur
            $user->tokens()->delete(); // Supprime tous les tokens

            return response()->json(['message' => 'Déconnexion réussie. Vous devez vous reconnecter pour accéder à l\'application.']);
        }

        return response()->json(['message' => 'Utilisateur non authentifié.'], 401);
    }

}

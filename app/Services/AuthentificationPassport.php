<?php

namespace App\Services;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthentificationPassport implements AuthentificationServiceInterface
{
    public function authentificate(Request $request)
    {
        $credentials = $request->only('login', 'password');

        if (Auth::attempt($credentials)) {
            $user = User::find(Auth::user()->id);

            // Create access and refresh tokens
            $token = $user->createToken('appToken')->accessToken;
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
        // Validate input
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // Authenticate user
        if (!Auth::attempt($request->only('login', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Identifiants invalides.'
            ], 401);
        }

        // Retrieve authenticated user
        $user = User::find(Auth::user()->id);

        // Create new access and refresh tokens
        $newAccessToken = $user->createToken('appToken')->accessToken;
        $newRefreshToken = $user->createToken('refreshToken')->accessToken;

        return response()->json([
            'success' => true,
            'token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
            'user' => new UserResource($user),
        ], 200);
    }

    public function logout()
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            // Retrieve the authenticated user
            $user = Auth::user();

            // Revoke all tokens for the user
            $user->tokens()->delete();

            return response()->json([
                'message' => 'Déconnexion réussie. Vous devez vous reconnecter pour accéder à l\'application.'
            ], 200);
        }

        return response()->json([
            'message' => 'Utilisateur non authentifié.'
        ], 401);
    }
}

<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthentificationSanctum implements AuthentificationServiceInterface
{
    public function authentificate(Request $request)
    {
        $credentials = $request->only('login', 'password');

        if (Auth::attempt($credentials)) {
            // $user = Auth::user();
            $user = User::find(Auth::user()->id);


            $token = $user->createToken('Personal Access Token')->accessToken;

            return response()->json([
                'token' => $token,
                'user' => $user
            ], 200);
        }

        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    public function logout()
    {
        // Assurez-vous d'utiliser la même signature que l'interface
        if (Auth::check()) {
            $user = Auth::user();
            $user->tokens()->delete();

            return response()->json(['message' => 'Logged out successfully'], 200);
        }

        return response()->json(['error' => 'Unauthenticated'], 401);
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\StatusResponseEnum;
use App\Http\Requests\RegistreRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\RestResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    use RestResponseTrait; 

    public function register(RegistreRequest $request)
    {
        try {
            $user = User::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'photo' => $request->photo,
                'login' => $request->login,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'active' => $request->active,
            ]);

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
           $user = User::find(Auth::user()->id);
        $token = $user->createToken('appToken')->accessToken;

            // Generate a refresh token
            //$refreshToken = $this->createRefreshToken($user);

            return response()->json([
                'success' => true,
                'token' => $token,
                //'refresh_token' => $refreshToken,
                'user' => $user,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to authenticate.',
            ], 401);
        }
    }

    public function index()
    {
        $users = User::all();
        return $this->sendResponse(UserResource::collection($users), StatusResponseEnum::SUCCESS);
    }

    public function store(RegistreRequest $request)
    {
        try {
            $user = User::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'photo' => $request->photo,
                'login' => $request->login,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'active' => $request->active,
            ]);

            return $this->sendResponse(new UserResource($user), StatusResponseEnum::SUCCESS);
        } catch (Exception $e) {
            return $this->sendResponse(['error' => $e->getMessage()], StatusResponseEnum::ECHEC, 500);
        }
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->sendResponse(['error' => 'User not found'], StatusResponseEnum::ECHEC, 404);
        }

        return $this->sendResponse(new UserResource($user), StatusResponseEnum::SUCCESS);
    }

    /*public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update($request->all());

            return $this->sendResponse(new UserResource($user), \App\Enums\StateEnum::SUCCESS);
        } catch (Exception $e) {
            return $this->sendResponse(['error' => $e->getMessage()], \App\Enums\StateEnum::ECHEC, 500);
        }
    }*/

    /*public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->sendResponse(['error' => 'User not found'], \App\Enums\StateEnum::ECHEC, 404);
        }

        $user->delete();
        return $this->sendResponse(null, \App\Enums\StateEnum::SUCCESS);
    }*/
}

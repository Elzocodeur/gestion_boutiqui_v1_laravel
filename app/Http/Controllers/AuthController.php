<?php

namespace App\Http\Controllers;

use App\Services\AuthentificationServiceInterface;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthentificationServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        return $this->authService->authentificate($request);
    }

    // public function refreshToken(Request $request)
    // {
    //     return $this->authService->refreshToken($request);
    // }

    public function logout()
    {
        return $this->authService->logout();
    }
}

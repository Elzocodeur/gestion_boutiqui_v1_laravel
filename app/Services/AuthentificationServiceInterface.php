<?php

namespace App\Services;
use Illuminate\Http\Request;

interface AuthentificationServiceInterface
{
    public function authentificate(Request $request);
    public function logout();
}

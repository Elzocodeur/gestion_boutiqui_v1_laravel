<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AuthentificationServiceInterface;
use App\Services\AuthentificationPassport;
use App\Services\AuthentificationSanctum;

class AuthCustomServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Binding the interface to the Passport implementation by default
        $this->app->bind(AuthentificationServiceInterface::class, AuthentificationPassport::class);
    }

    public function boot()
    {
        //
    }
}

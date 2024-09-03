<?php

namespace App\Providers;


use App\Repositories\ArticleRepository;
use App\Repositories\ArticleRepositoryImplemente;
use App\Services\ArticleService;
use App\Services\ArticleServicesImplemente;

use App\Repositories\ClientRepository;
use App\Repositories\ClientRepositoryImplement;
use App\Services\ClientService;
use App\Services\ClientServiceImplement;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ArticleRepository::class, ArticleRepositoryImplemente::class);
        $this->app->singleton(ArticleService::class, ArticleServicesImplemente::class);

        // $this->app->bind(ClientRepository::class, ClientRepositoryImplement::class);
        // $this->app->bind(ClientService::class, ClientServiceImplement::class);

        $this->app->singleton('client-repository', function () {
            return new ClientRepositoryImplement();
        });

        $this->app->singleton('client-service', function ($app) {
            return new ClientServiceImplement($app->make('client-repository'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

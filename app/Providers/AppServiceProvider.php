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
use App\Services\UploadService;
use App\Services\PhotoService;
use App\Services\MailService;

use Illuminate\Support\ServiceProvider;
use App\Observers\UserObserver;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ArticleRepository::class, ArticleRepositoryImplemente::class);
        $this->app->singleton(ArticleService::class, ArticleServicesImplemente::class);

        // methode 1
        // $this->app->bind(ClientRepository::class, ClientRepositoryImplement::class);
        // $this->app->bind(ClientService::class, ClientServiceImplement::class);


        // methode 2
        // $this->app->singleton('client-repository', function () {
        //     return new ClientRepositoryImplement();
        // });

        // $this->app->singleton('client-service', function ($app) {
        //     return new ClientServiceImplement($app->make('client-repository'));
        // });


            // methode 3

                    // Enregistrement du dépôt client
        $this->app->singleton(ClientRepository::class, ClientRepositoryImplement::class);

        // Enregistrement du service d'upload
        $this->app->singleton(UploadService::class, function ($app) {
            return new UploadService();
        });

        // Enregistrement du service client en liant l'interface à l'implémentation
        $this->app->singleton('client-service', function ($app) {
            return new ClientServiceImplement(
                $app->make(ClientRepository::class),
                $app->make(UploadService::class)
                // $app->make(PhotoService::class),
                // $app->make(MailService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // User::observe(UserObserver::class);
    }
}

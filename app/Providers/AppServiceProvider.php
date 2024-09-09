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
use App\Observers\DetteObserver;
use App\Models\Dette;
use App\Repositories\DetteRepository;
use App\Repositories\DetteRepositoryImplement;
use App\Facades\UploadServiceImgurFacade as UploadWithImgur;
use App\Services\UploadServiceImgur;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // methode injection de dependance
        $this->app->singleton(ArticleRepository::class, ArticleRepositoryImplemente::class);
        $this->app->singleton(ArticleService::class, ArticleServicesImplemente::class);


        // for DetteRepository,DetteRepositoryImplemente
        $this->app->singleton(DetteRepository::class, DetteRepositoryImplement::class);




        // methode 1
        // $this->app->bind(ClientRepository::class, ClientRepositoryImplement::class);
        // $this->app->bind(ClientService::class, ClientServiceImplement::class);


                    // Enregistrement du dépôt client
        $this->app->singleton(ClientRepository::class, ClientRepositoryImplement::class);

        // Enregistrement du service d'upload
        $this->app->singleton(UploadService::class, function ($app) {
            return new UploadService();
        });

        // methode par facades
        $this->app->singleton('client-service', function ($app) {
            return new ClientServiceImplement(
                $app->make(ClientRepository::class),
                $app->make(UploadService::class),
                $app->make(PhotoService::class),
                $app->make(MailService::class)
            );
        });


        $this->app->singleton('uploadserviceimgur', function ($app) {
            return new UploadServiceImgur();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // User::observe(UserObserver::class);
        // Dette::observe(DetteObserver::class);
    }
}

<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\PhotoUploadEvent;
// use App\Listeners\HandlePhotoUpload;
use App\Events\UserCreated;
use App\Listeners\SendLoyaltyCardListener;
use App\Listeners\UploadUserPhotoListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
            // HandlePhotoUpload::class,
        ],
        UserCreated::class => [
            SendLoyaltyCardListener::class,
            UploadUserPhotoListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
        public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

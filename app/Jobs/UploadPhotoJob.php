<?php
namespace App\Jobs;

use App\Models\User;
use App\Services\PhotoService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\File;

class UploadPhotoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $photo;

    public function __construct(User $user,   $photo)
    {
        $this->user = $user;
        $this->photo = $photo;
    }

    public function handle(PhotoService $photoService)
    {
        dd($photoService);
        Log::info("Job UploadPhotoJob pour l'utilisateur : " );
        $photoService->uploadPhoto($this->user, $this->photo);
    }


}


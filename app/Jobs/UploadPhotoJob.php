<?php
namespace App\Jobs;

use App\Models\User;
use App\Services\PhotoService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class UploadPhotoJob   implements   ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    // protected $user;
    // protected $photo;

    // public function __construct(User $user, $photo)
    // {
    //     $this->user = $user;
    //     $this->photo = $photo;
    // }

    // public function handle(PhotoService $photoService)
    // {
    //     $photoService->uploadPhoto($this->user, $this->photo);
    // }


    public $user;
    public $photo;

    public function __construct(User $user, $photo)
    {
        $this->user = $user;
        $this->photo = $photo;
    }

    public function handle()
    {
        try {
            // Upload vers Cloudinary
            $uploadedFileUrl = Cloudinary::upload($this->photo->getRealPath())->getSecurePath();

            // Mise à jour du chemin de la photo (Cloudinary)
            $this->user->photo = $uploadedFileUrl;
            $this->user->is_photo_on_cloudinary = true;
            $this->user->save();
        } catch (\Exception $e) {
            // En cas d'échec, sauvegarder la photo localement
            $path = $this->photo->store('public/photos');
            $this->user->photo = $path;
            $this->user->is_photo_on_cloudinary = false;
            $this->user->save();
        }
    }
}

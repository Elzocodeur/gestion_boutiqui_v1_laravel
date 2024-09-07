<?php


namespace App\Jobs;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UploadPhotoToCloudinary implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    public $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function handle()
    {
        $user = User::find($this->userId);
        if (!$user || $user->is_photo_on_cloudinary) {
            return;
        }

        try {
            // Tentative d'upload vers Cloudinary avec le chemin de la photo locale
            $uploadedFileUrl = Cloudinary::upload(public_path('storage/' . $user->photo))->getSecurePath();

            // Mise à jour du chemin Cloudinary
            $user->photo = $uploadedFileUrl;
            $user->is_photo_on_cloudinary = true;
            $user->save();
        } catch (\Exception $e) {
            // En cas d'échec, laisser la photo en local
        }




    }
}

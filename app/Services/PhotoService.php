<?php
namespace App\Services;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

use App\Models\User;
// use Cloudinary;
use Exception;

class PhotoService
{
    public function uploadPhoto(User $user, $photo)
    {
        try {
            // Upload de la photo sur Cloudinary
            $uploadedFileUrl = Cloudinary::upload($photo->getRealPath())->getSecurePath();
            $user->photo = $uploadedFileUrl;
            $user->is_photo_on_cloudinary = true;
        } catch (Exception $e) {
            // En cas d'échec, sauvegarde en local
            $localPath = $photo->store('user_images', 'public');
            $user->photo = $localPath;
            $user->is_photo_on_cloudinary = false;
        }

        // Sauvegarde de l'utilisateur
        $user->save();
    }
}

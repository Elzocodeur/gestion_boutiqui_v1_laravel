<?php

// app/Jobs/UploadPhotoToCloudinary.php
namespace App\Jobs;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable; // Import du trait Dispatchable
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UploadPhotoToCloudinary implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels; // Utilisation du trait Dispatchable

    public $userData;
    public $userId;

    public function __construct($userData, $userId)
    {
        $this->userData = $userData;
        $this->userId = $userId;
    }

    public function handle()
    {
        try {
            // Upload vers Cloudinary
            $uploadedFileUrl = Cloudinary::upload($this->userData['photo']->getRealPath())->getSecurePath();

            // Mettre à jour l'utilisateur avec l'URL de la photo Cloudinary
            $user = User::find($this->userId);
            $user->photo = $uploadedFileUrl;
            $user->save();
        } catch (\Exception $e) {
            // Si l'upload Cloudinary échoue, sauvegarder en local
            $localPath = $this->userData['photo']->store('user_images', 'public');
            $user = User::find($this->userId);
            $user->photo = Storage::url($localPath);
            $user->save();
        }
    }
}


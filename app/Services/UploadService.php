<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UploadService
{
    public function uploadFile($file, $path)
    {
        $fullPath = Storage::put($path, $file);
        return $fullPath;
    }

    public function uploadImage($image, $path, $maxSize = 2048)
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'svg'];
        $extension = $image->getClientOriginalExtension();

        if (!in_array($extension, $allowedExtensions)) {
            throw new \Exception('Le type de fichier doit être jpg, jpeg, png, ou svg.');
        }

        if ($image->getSize() / 1024 > $maxSize) {
            throw new \Exception('La taille de l\'image ne doit pas dépasser ' . $maxSize . ' Ko.');
        }

        return $this->uploadFile($image, $path);
    }

    public function encryptImagePath($path)
    {
        return base64_encode($path);
    }
}

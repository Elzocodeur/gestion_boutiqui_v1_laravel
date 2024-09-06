<?php
// commande pour lancer la relance: php artisan photo:relance-upload

namespace App\Console\Commands;

use App\Models\User;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RelanceUploadPhotoCloudinary extends Command
{
    protected $signature = 'photo:relance-upload';
    protected $description = 'Relancer l\'upload des photos vers Cloudinary pour les utilisateurs sans photo sur Cloudinary';

    public function handle()
    {
        // Lister les utilisateurs dont la photo n'est pas encore sur Cloudinary
        $users = User::where('is_photo_on_cloudinary', false)
            ->whereNotNull('photo') // Assurez-vous que le chemin de la photo existe
            ->get();

        foreach ($users as $user) {
            try {
                // Tenter l'upload de la photo sur Cloudinary
                $uploadedFileUrl = Cloudinary::upload($user->photo)->getSecurePath();

                // Si upload réussi, mettre à jour la colonne et le chemin de la photo
                $user->photo = $uploadedFileUrl;
                $user->is_photo_on_cloudinary = true;
                $user->save();

                $this->info("Photo de l'utilisateur {$user->nom} uploadée avec succès.");

            } catch (\Exception $e) {
                // En cas d'erreur, journaliser le problème
                Log::error("Erreur lors de l'upload de la photo pour l'utilisateur {$user->id} : " . $e->getMessage());
                $this->error("Échec de l'upload de la photo pour {$user->nom}");
            }
        }

        $this->info("Processus de relance terminé.");
    }
}

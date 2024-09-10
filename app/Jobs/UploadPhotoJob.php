<?php
// namespace App\Jobs;

// use App\Models\User;
// use App\Services\PhotoService;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Queue\SerializesModels;
// use Illuminate\Bus\Queueable;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Http\File;

// class UploadPhotoJob implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//     public $user;
//     public $photo;

//     public function __construct(User $user,   $photo)
//     {
//         $this->user = $user;
//         $this->photo = $photo;
//     }

//     public function handle(PhotoService $photoService)
//     {
//         dd($photoService);
//         Log::info("Job UploadPhotoJob pour l'utilisateur : " );
//         $photoService->uploadPhoto($this->user, $this->photo);
//     }


// }







// namespace App\Jobs;

// use App\Models\Role;
// use Exception;
// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Http\UploadedFile;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Queue\SerializesModels;
// use App\Facades\UploadServiceImgurFacade as UploadWithImgur;
// use App\Facades\UploadServiceFacade as UploadWithLocal;
// use App\Models\User;
// use Illuminate\Support\Facades\Log;

// class UploadPhotoJob implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//     public $user;

//     /**
//      * Create a new job instance.
//      */
//     public function __construct(User $user)
//     {
//         $this->user = $user;
//     }

//     /**
//      * Execute the job.
//      */
//     public function handle(): void
//     {
//         try{

//             if(isset($this->user)){
//                 try{
//                     $fullImagePath = $this->user->photo;
//                     $imgurUrl = UploadWithImgur::uploadImageWithImgur($fullImagePath);
//                     $photoUrl = $imgurUrl ?: UploadWithLocal::uploadImage($this->user->photo);

//                     $this->user->photo = $photoUrl;
//                     $this->user->save();
//                 }catch(Exception $e){
//                     throw new Exception('Erreur lors de l\'upload de l\'image : ' . $e->getMessage());
//                 }
//             }

//         }catch(Exception $e){
//             Log::error('Erreur dans le job UploadPhotoJob: ' . $e->getMessage(), ['exception' => $e]);
//             throw new Exception('Erreur dans le job UploadPhotoJob: ' . $e->getMessage());
//         }
//     }
// }











namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\File;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class UploadPhotoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        Log::info("Tentative d'upload de la photo sur Cloudinary.");

        $uploadedFileUrl = Cloudinary::upload($this->photo)->getSecurePath();
        $this->user->photo = $uploadedFileUrl;
        $this->user->is_photo_on_cloudinary = true;
    } catch (\Exception $e) {
        Log::error("Échec de l'upload sur Cloudinary, erreur : " . $e->getMessage());

        // Sauvegarde de la photo en local en cas d'échec
        try {
            $localPath = $this->photo->store('user_images', 'public');
            $this->user->photo = $localPath;
            $this->user->is_photo_on_cloudinary = false;
        } catch (\Exception $e) {
            Log::error("Échec de la sauvegarde de la photo en local, erreur : " . $e->getMessage());
            $this->fail($e); // Échoue explicitement le job en cas d'erreur
        }
    }

    $this->user->save();
    Log::info("Photo sauvegardée avec succès.");
}

}


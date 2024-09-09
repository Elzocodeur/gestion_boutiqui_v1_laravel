<?php
    namespace App\Observers;

    use App\Models\User;
    use App\Events\UserCreated;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Http\File;

    class UserObserver
    {
        public function created(User $user)
        {
            Log::info('User créé : ');

            // Passer la photo correctement au job
            $photoPath = $user->photo;
            // $photo = file_exists($photoPath) ? new File($photoPath) : null;

            Log::info('User créé et Event déclenché.');
            // dd($photoPath);
            event(new UserCreated($user, $photoPath));

        }


        // release
        // public function created(User $user): void
        // {
        //     event(new UserCreated($user));
        // }
    }


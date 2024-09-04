<?php
// app/Mail/LoyaltyCardMail.php

// namespace App\Mail;

// use App\Models\User;
// use Illuminate\Bus\Queueable;
// use Illuminate\Mail\Mailable;
// use Illuminate\Queue\SerializesModels;

// class LoyaltyCardMail extends Mailable
// {
//     use Queueable, SerializesModels;

//     public $user;
//     public $filePath;

//     public function __construct(User $user, $filePath)
//     {
//         $this->user = $user;
//         $this->filePath = $filePath;
//     }

//     public function build()
//     {
//         $subject = 'Votre carte de fidélité';
//         $body = "Bonjour, {$this->user->prenom} {$this->user->nom},\n\n";
//         $body .= "Merci d'avoir créé un compte avec nous. Vous trouverez ci-joint votre carte de fidélité.\n\n";
//         $body .= "Cordialement,\nL'équipe de " . config('app.name');

//         return $this->subject($subject)
//                     ->attach($this->filePath, [
//                         'as' => 'loyalty_card.pdf',
//                         'mime' => 'application/pdf',
//                     ])
//                     ->text('') // Si vous ne voulez pas utiliser de texte HTML, laissez le corps vide
//                     ->with('message', $body);
//     }
// }




namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoyaltyCardMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $pdfPath;

    public function __construct($user, $pdfPath)
    {
        $this->user = $user;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        return $this->view('pdf.loyalty_card')
                    ->attach(storage_path('app/public/' . $this->pdfPath), [
                        'as' => 'carte_fidelite.pdf',
                        'mime' => 'application/pdf',
                    ])
                    ->subject('Votre carte de fidélité'); // Assurez-vous que le sujet est défini
    }
}


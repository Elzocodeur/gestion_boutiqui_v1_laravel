<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QRCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $qrCodeBase64;

    /**
     * Crée une nouvelle instance de la classe.
     *
     * @param string $qrCodeBase64
     */
    public function __construct(string $qrCodeBase64)
    {
        $this->qrCodeBase64 = $qrCodeBase64;
    }

    /**
     * Construire l'email.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('pdfs.qr_code')
                    ->subject('Votre QR Code')
                    ->with('qrCodeBase64', $this->qrCodeBase64);
    }
}

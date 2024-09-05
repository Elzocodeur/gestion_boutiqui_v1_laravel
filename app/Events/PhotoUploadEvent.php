<?php

// app/Events/PhotoUploadEvent.php
namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PhotoUploadEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userData;

    public function __construct($userData)
    {
        $this->userData = $userData;
    }
}



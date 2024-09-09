<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class UploadServiceImgur{

    protected $clientId;
    public function __construct(){
        $this->clientId = env('IMGUR_CLIENT_ID');
    }

    public function uploadImageWithImgur($imagePath)
    {

        $client = new Client();
        $response = $client->request('POST', 'https://api.imgur.com/3/image', [
            'headers' => [
                'Authorization' => 'Client-ID ' . $this->clientId,
            ],
            'multipart' => [
                [
                    'name' => 'image',
                    'contents' => fopen($imagePath, 'r'),
                ]
            ],
        ]);
        $body = json_decode((string)$response->getBody());

        if ($body && $body->success) {
            return $body->data->link ?? null;
        } else {
            return null;
        }
    }
}

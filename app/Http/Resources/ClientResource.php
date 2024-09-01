<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'surname' => $this->surname,
            'adresse' => $this->adresse,
            'telephone' => $this->telephone,
            'user' => $this->when($this->user_id, function () {
                return new UserResource($this->user);
            }),
        ];
    }
}



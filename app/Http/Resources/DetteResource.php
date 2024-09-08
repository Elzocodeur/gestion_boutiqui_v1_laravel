<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DetteResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'montant' => $this->montant,
            'montantRestant' => $this->montantRestant,
            'client' => $this->client,
            'articles' => $this->articles,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnderecoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'logradouro'     => $this->logradouro,
            'cep'            => $this->cep,
            'usuarios_count' => $this->whenNotNull($this->usuarios_count),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'nome'       => $this->nome,
            'email'      => $this->email,
            'cpf'        => $this->cpf,
            'perfil_id'  => $this->perfil_id,
            'perfil'     => new PerfilResource($this->whenLoaded('perfil')),
            'enderecos'  => EnderecoResource::collection($this->whenLoaded('enderecos')),
            'created_at' => $this->created_at?->format('d/m/Y'),
        ];
    }
}

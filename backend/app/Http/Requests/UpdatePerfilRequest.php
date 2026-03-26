<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePerfilRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('perfil');

        return [
            'nome' => "required|string|max:100|unique:perfis,nome,{$id}",
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome do perfil é obrigatório.',
            'nome.unique'   => 'Já existe um perfil com este nome.',
        ];
    }
}

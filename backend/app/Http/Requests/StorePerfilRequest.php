<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePerfilRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:100|unique:perfis,nome',
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

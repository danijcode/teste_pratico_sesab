<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome'       => 'required|string|max:150',
            'email'      => 'required|email|max:150|unique:usuarios,email',
            'cpf'        => 'required|string|max:14|unique:usuarios,cpf',
            'perfil_id'  => 'required|exists:perfis,id',
            'endereco_ids'   => 'nullable|array',
            'endereco_ids.*' => 'integer|exists:enderecos,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required'      => 'O nome é obrigatório.',
            'email.required'     => 'O e-mail é obrigatório.',
            'email.unique'       => 'Este e-mail já está em uso.',
            'cpf.required'       => 'O CPF é obrigatório.',
            'cpf.unique'         => 'Este CPF já está cadastrado.',
            'perfil_id.required' => 'O perfil é obrigatório.',
            'perfil_id.exists'   => 'Perfil inválido.',
        ];
    }
}

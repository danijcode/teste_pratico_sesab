<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEnderecoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('endereco');

        return [
            'logradouro' => [
                'required', 'string', 'max:200',
                Rule::unique('enderecos')->where('cep', $this->cep)->ignore($id),
            ],
            'cep' => 'required|string|max:9',
        ];
    }

    public function messages(): array
    {
        return [
            'logradouro.required' => 'O logradouro é obrigatório.',
            'logradouro.unique'   => 'Já existe um endereço com este logradouro e CEP.',
            'cep.required'        => 'O CEP é obrigatório.',
        ];
    }
}

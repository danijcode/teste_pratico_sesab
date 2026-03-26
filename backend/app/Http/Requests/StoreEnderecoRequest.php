<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnderecoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'logradouro' => [
                'required', 'string', 'max:200',
                Rule::unique('enderecos')->where('cep', $this->cep),
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Endereco extends Model
{
    protected $table = 'enderecos';

    protected $fillable = ['logradouro', 'cep'];

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'usuario_endereco', 'endereco_id', 'usuario_id');
    }
}

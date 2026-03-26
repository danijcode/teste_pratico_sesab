<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Usuario extends Model
{
    protected $table = 'usuarios';

    protected $fillable = ['nome', 'email', 'cpf', 'perfil_id'];

    public function perfil(): BelongsTo
    {
        return $this->belongsTo(Perfil::class, 'perfil_id');
    }

    public function enderecos(): BelongsToMany
    {
        return $this->belongsToMany(Endereco::class, 'usuario_endereco', 'usuario_id', 'endereco_id');
    }
}

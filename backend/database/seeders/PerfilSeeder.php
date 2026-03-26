<?php

namespace Database\Seeders;

use App\Models\Perfil;
use Illuminate\Database\Seeder;

class PerfilSeeder extends Seeder
{
    public function run(): void
    {
        $perfis = ['ADMIN', 'USER', 'GESTOR', 'OPERADOR'];

        foreach ($perfis as $nome) {
            Perfil::firstOrCreate(['nome' => $nome]);
        }
    }
}

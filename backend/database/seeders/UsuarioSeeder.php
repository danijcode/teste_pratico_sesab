<?php

namespace Database\Seeders;

use App\Models\Endereco;
use App\Models\Perfil;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $admin  = Perfil::where('nome', 'ADMIN')->first();
        $user   = Perfil::where('nome', 'USER')->first();

        $endRua    = Endereco::firstOrCreate(['logradouro' => 'Rua tal', 'cep' => '41950-035']);
        $endTravel = Endereco::firstOrCreate(['logradouro' => 'Travessa tal', 'cep' => '41200-050']);

        $u1 = Usuario::firstOrCreate(
            ['cpf' => '111.022.354-4'],
            ['nome' => 'Fulano de Tal', 'email' => 'fulano@gmail.com', 'perfil_id' => $admin->id]
        );
        $u1->enderecos()->syncWithoutDetaching([$endRua->id, $endTravel->id]);

        $u2 = Usuario::firstOrCreate(
            ['cpf' => '111.022.234-5'],
            ['nome' => 'Ciclano de Silva', 'email' => 'ciclano@gmail.com', 'perfil_id' => $user->id]
        );
        $u2->enderecos()->syncWithoutDetaching([$endRua->id]);

        $u3 = Usuario::firstOrCreate(
            ['cpf' => '130.046.464-6'],
            ['nome' => 'Maria Silva', 'email' => 'maria@gmail.com', 'perfil_id' => $user->id]
        );
        $u3->enderecos()->syncWithoutDetaching([$endTravel->id]);
    }
}

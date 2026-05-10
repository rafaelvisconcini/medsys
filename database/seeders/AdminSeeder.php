<?php

namespace Database\Seeders;

use App\Enums\Especialidade;
use App\Enums\PerfilUsuario;
use App\Models\Profissional;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'lucylady@clinica.local'],
            [
                'name'                  => 'Lucylady Visconcini',
                'password'              => bcrypt('Trocar@Primeiro1'),
                'perfil'                => PerfilUsuario::Admin,
                'ativo'                 => true,
                'force_password_change' => true, // deve trocar na primeira entrada
            ]
        );

        // Cadastra como profissional fonoaudióloga
        Profissional::firstOrCreate(
            ['user_id' => $user->id],
            [
                'especialidade'         => Especialidade::Fonoaudiologia,
                'registro_profissional' => '',
                'duracao_sessao_min'    => 50,
                'ativo'                 => true,
            ]
        );
    }
}

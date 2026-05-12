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
        // Proprietário do sistema — acesso total, imune a exclusão
        User::updateOrCreate(
            ['email' => 'rafael@theraflow.local'],
            [
                'name'                  => 'Rafael Visconcini',
                'password'              => bcrypt('Theraflow2025'),
                'perfil'                => PerfilUsuario::Proprietario,
                'ativo'                 => true,
                'force_password_change' => false,
            ]
        );

        // Administradora / Fonoaudióloga
        $lucylady = User::firstOrCreate(
            ['email' => 'lucylady@clinica.local'],
            [
                'name'                  => 'Lucylady Visconcini',
                'password'              => bcrypt('Trocar@Primeiro1'),
                'perfil'                => PerfilUsuario::Admin,
                'ativo'                 => true,
                'force_password_change' => true,
            ]
        );

        Profissional::firstOrCreate(
            ['user_id' => $lucylady->id],
            [
                'especialidade'         => Especialidade::Fonoaudiologia,
                'registro_profissional' => '',
                'duracao_sessao_min'    => 50,
                'ativo'                 => true,
            ]
        );
    }
}

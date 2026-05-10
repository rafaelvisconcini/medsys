<?php

namespace Database\Factories;

use App\Enums\Especialidade;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfissionalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'               => User::factory(),
            'especialidade'         => fake()->randomElement(Especialidade::cases())->value,
            'registro_profissional' => 'CRFa ' . fake()->numerify('####-##'),
            'duracao_sessao_min'    => 50,
            'ativo'                 => true,
        ];
    }
}

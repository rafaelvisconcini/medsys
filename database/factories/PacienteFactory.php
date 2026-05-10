<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PacienteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome'                   => fake()->name(),
            'data_nascimento'        => fake()->dateTimeBetween('-12 years', '-3 years')->format('Y-m-d'),
            'sexo'                   => fake()->randomElement(['M', 'F']),
            'responsavel_nome'       => fake()->name(),
            'responsavel_parentesco' => 'Mãe',
            'responsavel_celular'    => '51999999999',
            'responsavel_email'      => fake()->safeEmail(),
            'ativo'                  => true,
        ];
    }
}

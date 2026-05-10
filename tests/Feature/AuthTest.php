<?php

namespace Tests\Feature;

use App\Enums\PerfilUsuario;
use App\Http\Middleware\ForcarTrocaSenha;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_pagina_login_carrega(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_login_com_credenciais_validas(): void
    {
        $user = User::factory()->create([
            'password'              => bcrypt('senha123'),
            'ativo'                 => true,
            'force_password_change' => false,
            'perfil'                => PerfilUsuario::Recepcionista,
        ]);

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'senha123',
        ])->assertRedirect('/');
    }

    public function test_login_com_credenciais_invalidas(): void
    {
        $user = User::factory()->create(['password' => bcrypt('correta')]);

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'errada',
        ])->assertSessionHasErrors('email');
    }

    public function test_usuario_inativo_e_deslogado(): void
    {
        $user = User::factory()->create([
            'ativo'                 => false,
            'force_password_change' => false,
            'perfil'                => PerfilUsuario::Recepcionista,
        ]);

        $this->actingAs($user)
            ->get('/')
            ->assertRedirect('/login');
    }

    public function test_force_password_change_redireciona(): void
    {
        $user = User::factory()->create([
            'ativo'                 => true,
            'force_password_change' => true,
            'perfil'                => PerfilUsuario::Recepcionista,
        ]);

        $this->actingAs($user)
            ->withoutMiddleware(\App\Http\Middleware\VerificarUsuarioAtivo::class)
            ->get('/')
            ->assertRedirect('/senha/trocar');
    }

    public function test_senha_fraca_rejeitada_na_troca(): void
    {
        $user = User::factory()->create([
            'ativo'                 => true,
            'force_password_change' => true,
            'perfil'                => PerfilUsuario::Recepcionista,
        ]);

        $this->actingAs($user)
            ->withoutMiddleware(\App\Http\Middleware\VerificarUsuarioAtivo::class)
            ->post('/senha/trocar', [
                'password'              => 'fraca',
                'password_confirmation' => 'fraca',
            ])
            ->assertSessionHasErrors('password');
    }

    public function test_troca_senha_limpa_flag(): void
    {
        $user = User::factory()->create([
            'ativo'                 => true,
            'force_password_change' => true,
            'perfil'                => PerfilUsuario::Recepcionista,
        ]);

        $this->actingAs($user)
            ->withoutMiddleware(\App\Http\Middleware\VerificarUsuarioAtivo::class)
            ->post('/senha/trocar', [
                'password'              => 'Nova@Senha1',
                'password_confirmation' => 'Nova@Senha1',
            ])
            ->assertRedirect('/');

        $this->assertFalse($user->fresh()->force_password_change);
    }

    public function test_logout(): void
    {
        $user = User::factory()->create([
            'ativo'                 => true,
            'force_password_change' => false,
            'perfil'                => PerfilUsuario::Recepcionista,
        ]);

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    }
}

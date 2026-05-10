<?php

namespace Tests\Feature;

use App\Enums\PerfilUsuario;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PacienteTest extends TestCase
{
    use RefreshDatabase;

    private function usuario(PerfilUsuario $perfil): User
    {
        return User::factory()->create([
            'perfil'                => $perfil,
            'ativo'                 => true,
            'force_password_change' => false,
        ]);
    }

    private function dadosPaciente(array $extra = []): array
    {
        return array_merge([
            'nome'                   => 'Criança Teste',
            'data_nascimento'        => '2018-05-10',
            'sexo'                   => 'M',
            'responsavel_nome'       => 'Responsável Teste',
            'responsavel_parentesco' => 'Mãe',
            'responsavel_celular'    => '51999999999',
        ], $extra);
    }

    public function test_admin_pode_criar_paciente(): void
    {
        $admin = $this->usuario(PerfilUsuario::Admin);

        $this->actingAs($admin)
            ->post('/pacientes', $this->dadosPaciente())
            ->assertRedirect();

        $this->assertDatabaseHas('pacientes', ['nome' => 'Criança Teste']);
    }

    public function test_recepcionista_pode_criar_paciente(): void
    {
        $recep = $this->usuario(PerfilUsuario::Recepcionista);

        $this->actingAs($recep)
            ->post('/pacientes', $this->dadosPaciente(['nome' => 'Paciente Recep']))
            ->assertRedirect();

        $this->assertDatabaseHas('pacientes', ['nome' => 'Paciente Recep']);
    }

    public function test_profissional_nao_pode_criar_paciente(): void
    {
        $prof = $this->usuario(PerfilUsuario::Profissional);

        $this->actingAs($prof)
            ->post('/pacientes', $this->dadosPaciente())
            ->assertForbidden();
    }

    public function test_financeiro_nao_pode_criar_paciente(): void
    {
        $fin = $this->usuario(PerfilUsuario::Financeiro);

        $this->actingAs($fin)
            ->post('/pacientes', $this->dadosPaciente())
            ->assertForbidden();
    }

    public function test_listagem_exige_autenticacao(): void
    {
        $this->get('/pacientes')->assertRedirect('/login');
    }

    public function test_admin_pode_deletar_paciente(): void
    {
        $admin   = $this->usuario(PerfilUsuario::Admin);
        $paciente = Paciente::factory()->create();

        $this->actingAs($admin)
            ->delete("/pacientes/{$paciente->id}")
            ->assertRedirect();

        $this->assertSoftDeleted('pacientes', ['id' => $paciente->id]);
    }

    public function test_recepcionista_nao_pode_deletar_paciente(): void
    {
        $recep   = $this->usuario(PerfilUsuario::Recepcionista);
        $paciente = Paciente::factory()->create();

        $this->actingAs($recep)
            ->delete("/pacientes/{$paciente->id}")
            ->assertForbidden();
    }

    public function test_paciente_inativo_nao_aparece_na_busca_de_ativos(): void
    {
        Paciente::factory()->create(['nome' => 'Ativo', 'ativo' => true]);
        Paciente::factory()->create(['nome' => 'Inativo', 'ativo' => false]);

        $this->assertEquals(1, Paciente::where('ativo', true)->count());
    }
}

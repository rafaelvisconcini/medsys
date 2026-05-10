<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Especialidade;
use App\Enums\PerfilUsuario;
use App\Http\Controllers\Controller;
use App\Models\Profissional;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class ProfissionalController extends Controller
{
    public function index()
    {
        $profissionais = Profissional::with('user')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.profissionais.index', compact('profissionais'));
    }

    public function create()
    {
        $especialidades = Especialidade::cases();
        return view('admin.profissionais.create', compact('especialidades'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'name'                   => 'required|string|max:255',
            'email'                  => 'required|email|unique:users,email',
            'especialidade'          => 'required|string',
            'registro_profissional'  => 'required|string|max:50',
            'duracao_sessao_min'     => 'required|integer|min:15|max:240',
        ], [
            'email.unique' => 'Este e-mail já está em uso.',
        ]);

        DB::transaction(function () use ($dados) {
            $user = User::create([
                'name'                  => $dados['name'],
                'email'                 => $dados['email'],
                'password'              => bcrypt('Trocar@Primeiro1'),
                'perfil'                => PerfilUsuario::Profissional,
                'ativo'                 => true,
                'force_password_change' => true,
            ]);

            Profissional::create([
                'user_id'               => $user->id,
                'especialidade'         => $dados['especialidade'],
                'registro_profissional' => $dados['registro_profissional'],
                'duracao_sessao_min'    => $dados['duracao_sessao_min'],
                'ativo'                 => true,
            ]);
        });

        return redirect()->route('profissionais.index')
            ->with('success', 'Profissional cadastrado. Senha temporária: Trocar@Primeiro1');
    }

    public function show(Profissional $profissional)
    {
        $profissional->load('user');
        return view('admin.profissionais.show', compact('profissional'));
    }

    public function edit(Profissional $profissional)
    {
        $profissional->load('user');
        $especialidades = Especialidade::cases();
        return view('admin.profissionais.edit', compact('profissional', 'especialidades'));
    }

    public function update(Request $request, Profissional $profissional)
    {
        $dados = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email,' . $profissional->user_id,
            'especialidade'         => 'required|string',
            'registro_profissional' => 'required|string|max:50',
            'duracao_sessao_min'    => 'required|integer|min:15|max:240',
            'ativo'                 => 'boolean',
        ]);

        DB::transaction(function () use ($dados, $profissional) {
            $profissional->user->update([
                'name'  => $dados['name'],
                'email' => $dados['email'],
                'ativo' => isset($dados['ativo']),
            ]);

            $profissional->update([
                'especialidade'         => $dados['especialidade'],
                'registro_profissional' => $dados['registro_profissional'],
                'duracao_sessao_min'    => $dados['duracao_sessao_min'],
                'ativo'                 => isset($dados['ativo']),
            ]);
        });

        return redirect()->route('profissionais.index')
            ->with('success', 'Profissional atualizado.');
    }

    public function destroy(Profissional $profissional)
    {
        $profissional->update(['ativo' => false]);
        $profissional->user->update(['ativo' => false]);

        return redirect()->route('profissionais.index')
            ->with('success', 'Profissional desativado.');
    }
}

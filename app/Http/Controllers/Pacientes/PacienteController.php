<?php

namespace App\Http\Controllers\Pacientes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pacientes\StorePacienteRequest;
use App\Http\Requests\Pacientes\UpdatePacienteRequest;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;

class PacienteController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Paciente::class);

        $pacientes = Paciente::query()
            ->when($request->busca, fn($q, $b) =>
                $q->where('nome', 'like', "%{$b}%")
                  ->orWhere('responsavel_nome', 'like', "%{$b}%")
            )
            ->when($request->filled('status'), fn($q) =>
                $q->where('ativo', $request->status)
            )
            ->orderBy('nome')
            ->paginate(15)
            ->withQueryString();

        return view('pacientes.index', compact('pacientes'));
    }

    public function create()
    {
        $this->authorize('create', Paciente::class);
        return view('pacientes.create');
    }

    public function store(StorePacienteRequest $request)
    {
        $dados = $request->except('foto');
        $dados['diagnosticos'] = $request->diagnosticos ?? [];

        if ($request->hasFile('foto')) {
            $dados['foto_path'] = $this->salvarFoto($request->file('foto'));
        }

        $paciente = Paciente::create($dados);

        $paciente->prontuario()->create([
            'numero' => $this->gerarNumeroProntuario($paciente->id),
        ]);

        return redirect()->route('pacientes.show', $paciente)
            ->with('success', "Paciente {$paciente->nome} cadastrado com sucesso.");
    }

    public function show(Paciente $paciente)
    {
        $this->authorize('view', $paciente);

        if (auth()->user()->perfil->value === 'financeiro') {
            return view('pacientes.show-financeiro', compact('paciente'));
        }

        $sessoes = $paciente->sessoes()
            ->with('profissional.user')
            ->latest('data_hora')
            ->take(10)
            ->get();

        $contratos = $paciente->contratos()
            ->with('profissional.user')
            ->where('status', 'ativo')
            ->get();

        return view('pacientes.show', compact('paciente', 'sessoes', 'contratos'));
    }

    public function edit(Paciente $paciente)
    {
        $this->authorize('update', $paciente);
        return view('pacientes.edit', compact('paciente'));
    }

    public function update(UpdatePacienteRequest $request, Paciente $paciente)
    {
        $dados = $request->except('foto');
        $dados['diagnosticos'] = $request->diagnosticos ?? [];

        if ($request->hasFile('foto')) {
            $this->excluirFotoAnterior($paciente->foto_path);
            $dados['foto_path'] = $this->salvarFoto($request->file('foto'));
        }

        $paciente->update($dados);

        return redirect()->route('pacientes.show', $paciente)
            ->with('success', 'Cadastro atualizado com sucesso.');
    }

    public function destroy(Paciente $paciente)
    {
        $this->authorize('delete', $paciente);

        $paciente->update(['ativo' => false]);
        $paciente->delete();

        return redirect()->route('pacientes.index')
            ->with('success', "Paciente {$paciente->nome} desativado.");
    }

    private function salvarFoto($arquivo): string
    {
        $nome = 'pacientes/' . uniqid() . '.webp';
        $destino = storage_path('app/public/' . $nome);

        if (! is_dir(dirname($destino))) {
            mkdir(dirname($destino), 0755, true);
        }

        Image::read($arquivo)
            ->cover(300, 300)
            ->toWebp(80)
            ->save($destino);

        return $nome;
    }

    private function excluirFotoAnterior(?string $path): void
    {
        if ($path && file_exists(storage_path('app/public/' . $path))) {
            unlink(storage_path('app/public/' . $path));
        }
    }

    private function gerarNumeroProntuario(int $id): string
    {
        return date('Y') . str_pad($id, 6, '0', STR_PAD_LEFT);
    }
}

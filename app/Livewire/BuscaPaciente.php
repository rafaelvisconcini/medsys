<?php

namespace App\Livewire;

use App\Models\Paciente;
use Livewire\Component;

class BuscaPaciente extends Component
{
    public string $termo = '';
    public bool $aberto = false;

    public function updatedTermo(): void
    {
        $this->aberto = strlen($this->termo) >= 2;
    }

    public function selecionar(int $id): void
    {
        $this->redirect(route('pacientes.show', $id));
    }

    public function render()
    {
        $resultados = collect();

        if (strlen($this->termo) >= 2) {
            $resultados = Paciente::where('ativo', true)
                ->where(fn($q) =>
                    $q->where('nome', 'like', "%{$this->termo}%")
                      ->orWhere('responsavel_nome', 'like', "%{$this->termo}%")
                )
                ->orderBy('nome')
                ->limit(8)
                ->get(['id', 'nome', 'data_nascimento', 'responsavel_nome']);
        }

        return view('livewire.busca-paciente', compact('resultados'));
    }
}

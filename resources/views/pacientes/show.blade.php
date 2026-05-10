@extends('layouts.app')

@section('title', $paciente->nome)
@section('page-title', $paciente->nome)

@section('header-actions')
<div class="d-flex gap-2">
    @can('update', $paciente)
    <a href="{{ route('pacientes.edit', $paciente) }}" class="btn btn-outline-secondary btn-sm">Editar</a>
    @endcan
    @can('agendar')
    <a href="{{ route('sessoes.create', ['paciente_id' => $paciente->id]) }}" class="btn btn-primary btn-sm">
        + Agendar Sessão
    </a>
    @endcan
    @can('admin')
    <a href="{{ route('lgpd.audit.paciente', $paciente) }}" class="btn btn-outline-secondary btn-sm">
        Auditoria
    </a>
    @endcan
</div>
@endsection

@section('content')
<div class="row g-4">

    {{-- Coluna esquerda: dados do paciente --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center p-4 mb-4">
            @if($paciente->foto_path)
                <img src="{{ asset('storage/' . $paciente->foto_path) }}"
                     class="rounded-circle mx-auto mb-3" width="100" height="100"
                     style="object-fit:cover;">
            @else
                <div class="rounded-circle bg-primary bg-opacity-10 mx-auto mb-3 d-flex align-items-center justify-content-center"
                     style="width:100px;height:100px;font-size:2rem;font-weight:700;color:#0d6efd;">
                    {{ strtoupper(substr($paciente->nome, 0, 1)) }}
                </div>
            @endif
            <h5 class="fw-bold mb-0">{{ $paciente->nome }}</h5>
            <div class="text-muted small mb-2">
                {{ $paciente->data_nascimento->format('d/m/Y') }} · {{ $paciente->data_nascimento->age }} anos
                @if($paciente->sexo) · {{ $paciente->sexo === 'M' ? 'Masculino' : 'Feminino' }} @endif
            </div>
            <span class="badge {{ $paciente->ativo ? 'bg-success' : 'bg-secondary' }}">
                {{ $paciente->ativo ? 'Ativo' : 'Inativo' }}
            </span>

            @if(!empty($paciente->diagnosticos))
            <div class="mt-3 d-flex flex-wrap gap-1 justify-content-center">
                @foreach($paciente->diagnosticos as $cid)
                    <span class="badge bg-info text-dark">{{ $cid }}</span>
                @endforeach
            </div>
            @endif

            @if($paciente->prontuario)
            <div class="mt-3">
                @can('ver-prontuario')
                <a href="{{ route('prontuarios.show', $paciente) }}"
                   class="btn btn-outline-primary btn-sm w-100">
                    Ver Prontuário #{{ $paciente->prontuario->numero }}
                </a>
                @endcan
            </div>
            @endif
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold small">Responsável</div>
            <div class="card-body small">
                <div class="fw-semibold">{{ $paciente->responsavel_nome }}</div>
                <div class="text-muted">{{ $paciente->responsavel_parentesco }}</div>
                <div class="mt-2">📱 {{ $paciente->responsavel_celular }}</div>
                @if($paciente->responsavel_telefone)
                    <div>📞 {{ $paciente->responsavel_telefone }}</div>
                @endif
                @if($paciente->responsavel_email)
                    <div>✉️ {{ $paciente->responsavel_email }}</div>
                @endif
            </div>
        </div>

        @if($paciente->contato2_nome)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold small">Contato Secundário</div>
            <div class="card-body small">
                <div class="fw-semibold">{{ $paciente->contato2_nome }}</div>
                <div class="text-muted">{{ $paciente->contato2_parentesco }}</div>
                <div class="mt-1">📱 {{ $paciente->contato2_telefone }}</div>
            </div>
        </div>
        @endif

        @if($paciente->logradouro)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold small">Endereço</div>
            <div class="card-body small text-muted">
                {{ $paciente->logradouro }}, {{ $paciente->numero }}
                @if($paciente->complemento) — {{ $paciente->complemento }} @endif<br>
                {{ $paciente->bairro }} · {{ $paciente->cidade }}/{{ $paciente->uf }}
                @if($paciente->cep) · CEP {{ substr($paciente->cep, 0, 5) }}-{{ substr($paciente->cep, 5) }} @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Coluna direita: sessões e contratos --}}
    <div class="col-lg-8">
        @if($paciente->escola || $paciente->serie_escolar)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body small">
                🏫 <strong>{{ $paciente->escola }}</strong>
                @if($paciente->serie_escolar) · {{ $paciente->serie_escolar }} @endif
            </div>
        </div>
        @endif

        @if($paciente->observacoes)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold small">Observações</div>
            <div class="card-body small text-muted">{{ $paciente->observacoes }}</div>
        </div>
        @endif

        {{-- Contratos ativos --}}
        @if($contratos->isNotEmpty())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold small">Contratos Ativos</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Especialidade</th>
                            <th>Profissional</th>
                            <th>Sessões/semana</th>
                            <th>Valor mensal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contratos as $c)
                        <tr>
                            <td>{{ $c->especialidade->label() }}</td>
                            <td>{{ $c->profissional->user->name }}</td>
                            <td>{{ $c->sessoes_por_semana }}x</td>
                            <td>R$ {{ number_format($c->valor_mensal, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Últimas sessões --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold small d-flex justify-content-between align-items-center">
                Últimas Sessões
                @can('agendar')
                <a href="{{ route('agenda.index') }}" class="btn btn-link btn-sm p-0">Ver agenda →</a>
                @endcan
            </div>
            @if($sessoes->isEmpty())
                <p class="text-muted text-center py-4 mb-0 small">Nenhuma sessão registrada.</p>
            @else
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr><th>Data</th><th>Especialidade</th><th>Profissional</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach($sessoes as $s)
                        <tr>
                            <td class="small">{{ $s->data_hora->format('d/m/Y H:i') }}</td>
                            <td class="small">{{ $s->especialidade->label() }}</td>
                            <td class="small">{{ $s->profissional->user->name }}</td>
                            <td>
                                <span class="badge bg-{{ match($s->status) {
                                    'realizada' => 'success',
                                    'cancelada', 'faltou' => 'danger',
                                    default => 'warning text-dark'
                                } }}">{{ ucfirst($s->status) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

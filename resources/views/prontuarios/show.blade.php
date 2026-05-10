@extends('layouts.app')

@section('title', 'Prontuário — ' . $paciente->nome)
@section('page-title', 'Prontuário — ' . $paciente->nome)

@section('header-actions')
    <a href="{{ route('pacientes.show', $paciente) }}" class="btn btn-outline-secondary btn-sm">&larr; Dados do Paciente</a>
@endsection

@section('content')

{{-- Cabeçalho do prontuário --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex align-items-center gap-4 py-3">
        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
             style="width:56px;height:56px;font-size:1.4rem;font-weight:700;color:#0d6efd;">
            {{ strtoupper(substr($paciente->nome, 0, 1)) }}
        </div>
        <div class="flex-grow-1">
            <h5 class="fw-bold mb-0">{{ $paciente->nome }}</h5>
            <span class="text-muted small">Prontuário Nº {{ $prontuario->numero }}</span>
            @foreach(($paciente->diagnosticos ?? []) as $cid)
                <span class="badge bg-secondary bg-opacity-75 ms-1">{{ $cid }}</span>
            @endforeach
        </div>
        <div class="text-end text-muted small">
            <div>{{ $paciente->idade }}</div>
            <div>{{ $paciente->data_nascimento->format('d/m/Y') }}</div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<ul class="nav nav-tabs mb-4" id="prontTab">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#tab-evolucoes">
            Evoluções <span class="badge bg-primary rounded-pill">{{ $evolucoes->total() }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-planos">
            Plano Terapêutico <span class="badge bg-secondary rounded-pill">{{ $planos->count() }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-encaminhamentos">
            Encaminhamentos <span class="badge bg-secondary rounded-pill">{{ $encaminhamentos->count() }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-anexos">
            Anexos <span class="badge bg-secondary rounded-pill">{{ $anexos->total() }}</span>
        </a>
    </li>
</ul>

<div class="tab-content">

    {{-- TAB: Evoluções --}}
    <div class="tab-pane fade show active" id="tab-evolucoes">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-semibold mb-0 text-secondary">Registro de Evoluções</h6>
            <a href="{{ route('prontuarios.evolucoes.create', $prontuario) }}" class="btn btn-primary btn-sm">
                + Nova Evolução
            </a>
        </div>

        @forelse ($evolucoes as $ev)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge" style="background:#1d4ed8;">{{ $ev->especialidade->label() }}</span>
                    <span class="fw-semibold">{{ $ev->profissional->user->name }}</span>
                    <span class="text-muted small">{{ $ev->data_hora->format('d/m/Y H:i') }}</span>
                    @if($ev->sessao)
                        <span class="badge bg-light text-dark border">Sessão #{{ $ev->sessao_id }}</span>
                    @endif
                </div>
                <div class="d-flex gap-1">
                    <a href="{{ route('evolucoes.show', $ev) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                    @can('update', $ev)
                    <a href="{{ route('evolucoes.edit', $ev) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                    @endcan
                </div>
            </div>
            <div class="card-body py-3">
                <p class="mb-1">{{ Str::limit($ev->descricao, 300) }}</p>
                @if($ev->cids)
                    <div class="mt-2">
                        @foreach($ev->cids as $cid)
                            <span class="badge bg-light text-dark border me-1">{{ $cid }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center text-muted py-5">Nenhuma evolução registrada.</div>
        @endforelse

        {{ $evolucoes->appends(['anexos_page' => $anexos->currentPage()])->links() }}
    </div>

    {{-- TAB: Plano Terapêutico --}}
    <div class="tab-pane fade" id="tab-planos">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-semibold mb-0 text-secondary">Planos Terapêuticos</h6>
            @can('ver-prontuario')
            <a href="{{ route('prontuarios.planos.create', $prontuario) }}" class="btn btn-primary btn-sm">
                + Novo Plano
            </a>
            @endcan
        </div>

        @forelse ($planos as $plano)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <span class="fw-semibold">{{ $plano->titulo }}</span>
                    <span class="badge ms-2 {{ $plano->status === 'ativo' ? 'bg-success' : ($plano->status === 'suspenso' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                        {{ ucfirst($plano->status) }}
                    </span>
                    <span class="text-muted small ms-2">
                        {{ $plano->periodo_inicio->format('d/m/Y') }}
                        @if($plano->periodo_fim) — {{ $plano->periodo_fim->format('d/m/Y') }} @endif
                    </span>
                </div>
                @can('ver-prontuario')
                @if(auth()->user()->isAdmin() || $plano->criado_por === auth()->id())
                <div class="d-flex gap-1">
                    <a href="{{ route('prontuarios.planos.edit', $plano) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                    <form action="{{ route('prontuarios.planos.destroy', $plano) }}" method="POST"
                          onsubmit="return confirm('Remover este plano terapêutico?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Remover</button>
                    </form>
                </div>
                @endif
                @endcan
            </div>
            <div class="card-body">
                @forelse ($plano->especialidades as $esp)
                <div class="mb-3 border-bottom pb-3">
                    <h6 class="fw-semibold text-primary mb-2">
                        {{ $esp->especialidade->label() }}
                        <small class="text-muted fw-normal">— {{ $esp->profissional->user->name }}</small>
                    </h6>
                    @if($esp->objetivos_gerais)
                    <p class="mb-1"><strong>Objetivos gerais:</strong> {{ $esp->objetivos_gerais }}</p>
                    @endif
                    @if($esp->objetivos_especificos)
                    <p class="mb-1"><strong>Objetivos específicos:</strong> {{ $esp->objetivos_especificos }}</p>
                    @endif
                    @if($esp->estrategias)
                    <p class="mb-0"><strong>Estratégias:</strong> {{ $esp->estrategias }}</p>
                    @endif
                </div>
                @empty
                <p class="text-muted small mb-0">Nenhuma especialidade cadastrada neste plano.</p>
                @endforelse
            </div>
            <div class="card-footer text-muted small bg-white">
                Criado por {{ $plano->criador->name }} em {{ $plano->created_at->format('d/m/Y') }}
            </div>
        </div>
        @empty
        <div class="text-center text-muted py-5">Nenhum plano terapêutico cadastrado.</div>
        @endforelse
    </div>

    {{-- TAB: Encaminhamentos --}}
    <div class="tab-pane fade" id="tab-encaminhamentos">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-semibold mb-0 text-secondary">Encaminhamentos</h6>
            <a href="{{ route('prontuarios.encaminhamentos.create', $prontuario) }}" class="btn btn-primary btn-sm">
                + Novo Encaminhamento
            </a>
        </div>

        @forelse ($encaminhamentos as $enc)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="fw-semibold">{{ $enc->para_especialidade }}</span>
                            <span class="badge {{ $enc->status === 'realizado' ? 'bg-success' : ($enc->status === 'cancelado' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                {{ ucfirst($enc->status) }}
                            </span>
                            <span class="text-muted small">{{ $enc->data->format('d/m/Y') }}</span>
                        </div>
                        <p class="mb-1 text-secondary">{{ $enc->motivo }}</p>
                        <small class="text-muted">Por {{ $enc->profissional->user->name }}</small>
                    </div>
                    <div class="d-flex gap-1 ms-3 flex-shrink-0">
                        <a href="{{ route('prontuarios.encaminhamentos.pdf', $enc) }}"
                           class="btn btn-sm btn-outline-secondary" target="_blank">PDF</a>
                        @if($enc->status === 'pendente')
                        <form action="{{ route('prontuarios.encaminhamentos.update', $enc) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="realizado">
                            <button class="btn btn-sm btn-outline-success">Marcar realizado</button>
                        </form>
                        @endif
                        <form action="{{ route('prontuarios.encaminhamentos.destroy', $enc) }}" method="POST"
                              onsubmit="return confirm('Remover encaminhamento?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Remover</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center text-muted py-5">Nenhum encaminhamento registrado.</div>
        @endforelse
    </div>

    {{-- TAB: Anexos --}}
    <div class="tab-pane fade" id="tab-anexos">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-semibold mb-0 text-secondary">Arquivos Anexados</h6>
            @can('ver-prontuario')
            <a href="{{ route('prontuarios.anexos.create', $prontuario) }}" class="btn btn-primary btn-sm">
                + Novo Anexo
            </a>
            @endcan
        </div>

        @forelse ($anexos as $anx)
        @php
            $tipoLabels = [
                'avaliacao' => ['label' => 'Avaliação',  'color' => 'bg-info text-dark'],
                'laudo'     => ['label' => 'Laudo',      'color' => 'bg-primary'],
                'relatorio' => ['label' => 'Relatório',  'color' => 'bg-secondary'],
                'imagem'    => ['label' => 'Imagem',     'color' => 'bg-success'],
                'outro'     => ['label' => 'Outro',      'color' => 'bg-light text-dark border'],
            ];
            $tc = $tipoLabels[$anx->tipo] ?? ['label' => $anx->tipo, 'color' => 'bg-secondary'];
            $kb = round($anx->tamanho_bytes / 1024);
            $tamanho = $anx->tamanho_bytes >= 1024 * 1024
                ? number_format($anx->tamanho_bytes / (1024 * 1024), 2) . ' MB'
                : $kb . ' KB';
        @endphp
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-3">
                <div class="d-flex align-items-start justify-content-between gap-3">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge {{ $tc['color'] }}">{{ $tc['label'] }}</span>
                            <span class="fw-semibold">{{ $anx->nome_original }}</span>
                            <span class="text-muted small">{{ $tamanho }}</span>
                        </div>
                        @if($anx->descricao)
                            <p class="mb-1 text-secondary small">{{ $anx->descricao }}</p>
                        @endif
                        <div class="text-muted small">
                            Enviado por {{ $anx->uploader->name }}
                            @if($anx->data_documento)
                                · Documento de {{ \Carbon\Carbon::parse($anx->data_documento)->format('d/m/Y') }}
                            @endif
                            · {{ $anx->created_at->format('d/m/Y H:i') }}
                            @if($anx->evolucao_id)
                                · <span class="badge bg-light text-dark border">Vinculado a evolução</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex gap-1 flex-shrink-0">
                        <a href="{{ route('prontuarios.anexos.download', $anx) }}"
                           class="btn btn-sm btn-outline-secondary">Baixar</a>
                        @if(auth()->user()->isAdmin() || $anx->uploaded_por === auth()->id())
                        <form action="{{ route('prontuarios.anexos.destroy', $anx) }}" method="POST"
                              onsubmit="return confirm('Remover este anexo permanentemente?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Remover</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center text-muted py-5">Nenhum arquivo anexado.</div>
        @endforelse

        {{ $anexos->appends(['evolucoes_page' => $evolucoes->currentPage()])->links() }}
    </div>

</div>
@endsection

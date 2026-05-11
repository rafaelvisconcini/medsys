@extends('layouts.app')
@section('title', 'Prontuário — ' . $paciente->nome)
@section('page-title', 'Prontuário — ' . $paciente->nome)

@section('header-actions')
    <a href="{{ route('pacientes.show', $paciente) }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Dados do Paciente
    </a>
@endsection

@section('content')

{{-- Cabeçalho --}}
<div class="card mb-4">
    <div class="card-body d-flex align-items-center gap-4" style="padding:1.25rem 1.5rem;">
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
             style="width:56px;height:56px;background:#eff6ff;font-size:1.375rem;font-weight:700;color:#2563eb;border:2px solid #dbeafe;">
            {{ strtoupper(substr($paciente->nome, 0, 1)) }}
        </div>
        <div style="flex:1;">
            <h5 style="font-weight:700;color:#111827;margin-bottom:.25rem;">{{ $paciente->nome }}</h5>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span style="font-size:.8125rem;color:#6b7280;">Prontuário Nº {{ $prontuario->numero }}</span>
                @foreach(($paciente->diagnosticos ?? []) as $cid)
                    <span class="badge bg-secondary">{{ $cid }}</span>
                @endforeach
            </div>
        </div>
        <div class="text-end" style="font-size:.8125rem;color:#6b7280;">
            <div>{{ $paciente->data_nascimento->age }} anos</div>
            <div>{{ $paciente->data_nascimento->format('d/m/Y') }}</div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<ul class="nav nav-tabs mb-4" id="prontTab">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#tab-evolucoes">
            <i class="bi bi-clipboard2-pulse me-1"></i>Evoluções
            <span class="badge bg-primary ms-1" style="border-radius:999px;">{{ $evolucoes->total() }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-planos">
            <i class="bi bi-list-check me-1"></i>Plano Terapêutico
            <span class="badge bg-secondary ms-1" style="border-radius:999px;">{{ $planos->count() }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-encaminhamentos">
            <i class="bi bi-send me-1"></i>Encaminhamentos
            <span class="badge bg-secondary ms-1" style="border-radius:999px;">{{ $encaminhamentos->count() }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-anexos">
            <i class="bi bi-paperclip me-1"></i>Anexos
            <span class="badge bg-secondary ms-1" style="border-radius:999px;">{{ $anexos->total() }}</span>
        </a>
    </li>
</ul>

<div class="tab-content">

    {{-- TAB Evoluções --}}
    <div class="tab-pane fade show active" id="tab-evolucoes">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span style="font-size:.8125rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Registro de Evoluções</span>
            <a href="{{ route('prontuarios.evolucoes.create', $prontuario) }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg"></i> Nova Evolução
            </a>
        </div>

        @forelse ($evolucoes as $ev)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center" style="padding:.875rem 1.25rem;">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-primary">{{ $ev->especialidade->label() }}</span>
                    <span style="font-weight:600;font-size:.875rem;color:#111827;">{{ $ev->profissional->user->name }}</span>
                    <span style="font-size:.8125rem;color:#9ca3af;">{{ $ev->data_hora->format('d/m/Y H:i') }}</span>
                    @if($ev->sessao)
                        <span class="badge bg-secondary">Sessão #{{ $ev->sessao_id }}</span>
                    @endif
                </div>
                <div class="d-flex gap-1">
                    <a href="{{ route('prontuarios.evolucoes.show', [$prontuario, $ev]) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-eye"></i>
                    </a>
                    @can('update', $ev)
                    <a href="{{ route('prontuarios.evolucoes.edit', [$prontuario, $ev]) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil"></i>
                    </a>
                    @endcan
                </div>
            </div>
            <div class="card-body" style="line-height:1.6;color:#374151;">
                <p class="mb-0">{{ Str::limit($ev->descricao, 300) }}</p>
                @if($ev->cids)
                <div class="mt-2 d-flex flex-wrap gap-1">
                    @foreach($ev->cids as $cid)
                        <span class="badge bg-secondary">{{ $cid }}</span>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="d-flex flex-column align-items-center justify-content-center py-5" style="color:#9ca3af;">
            <i class="bi bi-clipboard2" style="font-size:2.5rem;opacity:.3;"></i>
            <p class="mt-3 mb-0" style="font-size:.875rem;">Nenhuma evolução registrada.</p>
        </div>
        @endforelse

        {{ $evolucoes->appends(['anexos_page' => $anexos->currentPage()])->links() }}
    </div>

    {{-- TAB Plano Terapêutico --}}
    <div class="tab-pane fade" id="tab-planos">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span style="font-size:.8125rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Planos Terapêuticos</span>
            @can('ver-prontuario')
            <a href="{{ route('prontuarios.planos.create', $prontuario) }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg"></i> Novo Plano
            </a>
            @endcan
        </div>

        @forelse ($planos as $plano)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center" style="padding:.875rem 1.25rem;">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span style="font-weight:600;color:#111827;">{{ $plano->titulo }}</span>
                    <span class="badge {{ $plano->status === 'ativo' ? 'bg-success' : ($plano->status === 'suspenso' ? 'bg-warning' : 'bg-secondary') }}">
                        {{ ucfirst($plano->status) }}
                    </span>
                    <span style="font-size:.8125rem;color:#9ca3af;">
                        {{ $plano->periodo_inicio->format('d/m/Y') }}
                        @if($plano->periodo_fim) — {{ $plano->periodo_fim->format('d/m/Y') }} @endif
                    </span>
                </div>
                @can('ver-prontuario')
                @if(auth()->user()->isAdmin() || $plano->criado_por === auth()->id())
                <div class="d-flex gap-1">
                    <a href="{{ route('prontuarios.planos.edit', $plano) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('prontuarios.planos.destroy', $plano) }}" method="POST"
                          onsubmit="return confirm('Remover este plano terapêutico?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
                @endif
                @endcan
            </div>
            <div class="card-body">
                @forelse ($plano->especialidades as $esp)
                <div class="mb-4 pb-4 border-bottom">
                    <div style="font-weight:600;color:#2563eb;font-size:.875rem;margin-bottom:.75rem;">
                        {{ $esp->especialidade->label() }}
                        <span style="color:#6b7280;font-weight:400;"> &mdash; {{ $esp->profissional->user->name }}</span>
                    </div>
                    @if($esp->objetivos_gerais)
                    <div class="mb-2">
                        <div style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#9ca3af;margin-bottom:.25rem;">Objetivos gerais</div>
                        <p style="font-size:.875rem;color:#374151;margin:0;">{{ $esp->objetivos_gerais }}</p>
                    </div>
                    @endif
                    @if($esp->objetivos_especificos)
                    <div class="mb-2">
                        <div style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#9ca3af;margin-bottom:.25rem;">Objetivos específicos</div>
                        <p style="font-size:.875rem;color:#374151;margin:0;">{{ $esp->objetivos_especificos }}</p>
                    </div>
                    @endif
                    @if($esp->estrategias)
                    <div>
                        <div style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#9ca3af;margin-bottom:.25rem;">Estratégias</div>
                        <p style="font-size:.875rem;color:#374151;margin:0;">{{ $esp->estrategias }}</p>
                    </div>
                    @endif
                </div>
                @empty
                <p style="font-size:.875rem;color:#9ca3af;margin:0;">Nenhuma especialidade cadastrada neste plano.</p>
                @endforelse
            </div>
            <div class="card-footer" style="font-size:.8125rem;color:#6b7280;">
                Criado por {{ $plano->criador->name }} em {{ $plano->created_at->format('d/m/Y') }}
            </div>
        </div>
        @empty
        <div class="d-flex flex-column align-items-center justify-content-center py-5" style="color:#9ca3af;">
            <i class="bi bi-list-check" style="font-size:2.5rem;opacity:.3;"></i>
            <p class="mt-3 mb-0" style="font-size:.875rem;">Nenhum plano terapêutico cadastrado.</p>
        </div>
        @endforelse
    </div>

    {{-- TAB Encaminhamentos --}}
    <div class="tab-pane fade" id="tab-encaminhamentos">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span style="font-size:.8125rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Encaminhamentos</span>
            <a href="{{ route('prontuarios.encaminhamentos.create', $prontuario) }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg"></i> Novo Encaminhamento
            </a>
        </div>

        @forelse ($encaminhamentos as $enc)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div style="flex:1;">
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            <span style="font-weight:600;font-size:.875rem;color:#111827;">{{ $enc->para_especialidade }}</span>
                            <span class="badge {{ $enc->status === 'realizado' ? 'bg-success' : ($enc->status === 'cancelado' ? 'bg-danger' : 'bg-warning') }}">
                                {{ ucfirst($enc->status) }}
                            </span>
                            <span style="font-size:.8125rem;color:#9ca3af;">{{ $enc->data->format('d/m/Y') }}</span>
                        </div>
                        <p style="font-size:.875rem;color:#374151;margin-bottom:.5rem;">{{ $enc->motivo }}</p>
                        <span style="font-size:.8125rem;color:#9ca3af;">Por {{ $enc->profissional->user->name }}</span>
                    </div>
                    <div class="d-flex gap-1 ms-3 flex-shrink-0">
                        <a href="{{ route('prontuarios.encaminhamentos.pdf', $enc) }}"
                           class="btn btn-sm btn-outline-secondary" target="_blank">
                            <i class="bi bi-file-pdf"></i> PDF
                        </a>
                        @if($enc->status === 'pendente')
                        <form action="{{ route('prontuarios.encaminhamentos.update', $enc) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="realizado">
                            <button class="btn btn-sm btn-outline-success">
                                <i class="bi bi-check-lg"></i> Realizado
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('prontuarios.encaminhamentos.destroy', $enc) }}" method="POST"
                              onsubmit="return confirm('Remover encaminhamento?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="d-flex flex-column align-items-center justify-content-center py-5" style="color:#9ca3af;">
            <i class="bi bi-send" style="font-size:2.5rem;opacity:.3;"></i>
            <p class="mt-3 mb-0" style="font-size:.875rem;">Nenhum encaminhamento registrado.</p>
        </div>
        @endforelse
    </div>

    {{-- TAB Anexos --}}
    <div class="tab-pane fade" id="tab-anexos">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span style="font-size:.8125rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Arquivos Anexados</span>
            @can('ver-prontuario')
            <a href="{{ route('prontuarios.anexos.create', $prontuario) }}" class="btn btn-sm btn-primary">
                <i class="bi bi-paperclip"></i> Novo Anexo
            </a>
            @endcan
        </div>

        @forelse ($anexos as $anx)
        @php
            $tipoMap = [
                'avaliacao' => ['Avaliação','bg-info'],
                'laudo'     => ['Laudo','bg-primary'],
                'relatorio' => ['Relatório','bg-secondary'],
                'imagem'    => ['Imagem','bg-success'],
                'outro'     => ['Outro','bg-secondary'],
            ];
            [$tipoLabel, $tipoCor] = $tipoMap[$anx->tipo] ?? [$anx->tipo,'bg-secondary'];
            $tamanho = $anx->tamanho_bytes >= 1024*1024
                ? number_format($anx->tamanho_bytes/(1024*1024),2).' MB'
                : round($anx->tamanho_bytes/1024).' KB';
        @endphp
        <div class="card mb-3">
            <div class="card-body d-flex align-items-start justify-content-between gap-3" style="padding:.875rem 1.25rem;">
                <div style="flex:1;">
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <span class="badge {{ $tipoCor }}">{{ $tipoLabel }}</span>
                        <span style="font-weight:600;font-size:.875rem;color:#111827;">{{ $anx->nome_original }}</span>
                        <span style="font-size:.75rem;color:#9ca3af;">{{ $tamanho }}</span>
                    </div>
                    @if($anx->descricao)
                        <p style="font-size:.8125rem;color:#6b7280;margin-bottom:.375rem;">{{ $anx->descricao }}</p>
                    @endif
                    <div style="font-size:.75rem;color:#9ca3af;">
                        Enviado por {{ $anx->uploader->name }}
                        @if($anx->data_documento) &middot; Doc. {{ \Carbon\Carbon::parse($anx->data_documento)->format('d/m/Y') }} @endif
                        &middot; {{ $anx->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                <div class="d-flex gap-1 flex-shrink-0">
                    <a href="{{ route('prontuarios.anexos.download', $anx) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-download"></i>
                    </a>
                    @if(auth()->user()->isAdmin() || $anx->uploaded_por === auth()->id())
                    <form action="{{ route('prontuarios.anexos.destroy', $anx) }}" method="POST"
                          onsubmit="return confirm('Remover este anexo permanentemente?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="d-flex flex-column align-items-center justify-content-center py-5" style="color:#9ca3af;">
            <i class="bi bi-paperclip" style="font-size:2.5rem;opacity:.3;"></i>
            <p class="mt-3 mb-0" style="font-size:.875rem;">Nenhum arquivo anexado.</p>
        </div>
        @endforelse

        {{ $anexos->appends(['evolucoes_page' => $evolucoes->currentPage()])->links() }}
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const hash = window.location.hash;
    if (hash) {
        const trigger = document.querySelector('#prontTab a[href="' + hash + '"]');
        if (trigger) bootstrap.Tab.getOrCreateInstance(trigger).show();
    }
    document.querySelectorAll('#prontTab a[data-bs-toggle="tab"]').forEach(function (el) {
        el.addEventListener('shown.bs.tab', function (e) {
            history.replaceState(null, null, e.target.getAttribute('href'));
        });
    });
});
</script>
@endpush
@endsection

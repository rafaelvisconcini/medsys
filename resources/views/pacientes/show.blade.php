@extends('layouts.app')
@section('title', $paciente->nome)
@section('page-title', $paciente->nome)

@section('header-actions')
<div class="d-flex gap-2">
    <a href="{{ route('pacientes.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
    @can('update', $paciente)
    <a href="{{ route('pacientes.edit', $paciente) }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-pencil"></i> Editar
    </a>
    @endcan
    @can('agendar')
    <a href="{{ route('sessoes.create', ['paciente_id' => $paciente->id]) }}" class="btn btn-sm btn-primary">
        <i class="bi bi-calendar-plus"></i> Agendar Sessão
    </a>
    @endcan
    @can('admin')
    <a href="{{ route('lgpd.audit.paciente', $paciente) }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-shield-check"></i> Auditoria
    </a>
    @endcan
</div>
@endsection

@section('content')
<div class="row g-4">

    {{-- Coluna esquerda --}}
    <div class="col-lg-4">

        {{-- Perfil do paciente --}}
        <div class="card mb-4">
            <div class="card-body text-center" style="padding:2rem 1.5rem;">
                @if($paciente->foto_path)
                    <img src="{{ asset('storage/' . $paciente->foto_path) }}"
                         class="rounded-circle mb-3" width="96" height="96"
                         style="object-fit:cover;border:3px solid #e5e7eb;">
                @else
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                         style="width:96px;height:96px;background:#eff6ff;font-size:2rem;font-weight:700;color:#2563eb;border:3px solid #e5e7eb;">
                        {{ strtoupper(substr($paciente->nome, 0, 1)) }}
                    </div>
                @endif

                <h5 style="font-weight:700;color:#111827;margin-bottom:.25rem;">{{ $paciente->nome }}</h5>
                <div style="font-size:.8125rem;color:#6b7280;margin-bottom:.875rem;">
                    {{ $paciente->data_nascimento->format('d/m/Y') }} &middot; {{ $paciente->data_nascimento->age }} anos
                    @if($paciente->sexo) &middot; {{ $paciente->sexo === 'M' ? 'Masculino' : 'Feminino' }} @endif
                </div>
                <span class="badge {{ $paciente->ativo ? 'bg-success' : 'bg-secondary' }}">
                    {{ $paciente->ativo ? 'Ativo' : 'Inativo' }}
                </span>

                @if(!empty($paciente->diagnosticos))
                <div class="mt-3 d-flex flex-wrap gap-1 justify-content-center">
                    @foreach($paciente->diagnosticos as $cid)
                        <span class="badge bg-info">{{ $cid }}</span>
                    @endforeach
                </div>
                @endif

                @if($paciente->prontuario)
                @can('ver-prontuario')
                <div class="mt-4">
                    <a href="{{ route('prontuarios.show', $paciente) }}"
                       class="btn btn-outline-primary btn-sm w-100" style="justify-content:center;">
                        <i class="bi bi-journal-medical me-1"></i>
                        Prontuário #{{ $paciente->prontuario->numero }}
                    </a>
                </div>
                @endcan
                @endif
            </div>
        </div>

        {{-- Responsável --}}
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-person-fill me-2" style="color:#6b7280;"></i>Responsável
            </div>
            <div class="card-body" style="font-size:.875rem;">
                <div style="font-weight:600;color:#111827;">{{ $paciente->responsavel_nome }}</div>
                <div style="color:#6b7280;margin-bottom:.75rem;">{{ $paciente->responsavel_parentesco }}</div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-phone" style="color:#9ca3af;font-size:.8125rem;"></i>
                    <span>{{ $paciente->responsavel_celular }}</span>
                </div>
                @if($paciente->responsavel_telefone)
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-telephone" style="color:#9ca3af;font-size:.8125rem;"></i>
                    <span>{{ $paciente->responsavel_telefone }}</span>
                </div>
                @endif
                @if($paciente->responsavel_email)
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-envelope" style="color:#9ca3af;font-size:.8125rem;"></i>
                    <span>{{ $paciente->responsavel_email }}</span>
                </div>
                @endif
            </div>
        </div>

        @if($paciente->contato2_nome)
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-person me-2" style="color:#6b7280;"></i>Contato Secundário
            </div>
            <div class="card-body" style="font-size:.875rem;">
                <div style="font-weight:600;color:#111827;">{{ $paciente->contato2_nome }}</div>
                <div style="color:#6b7280;margin-bottom:.5rem;">{{ $paciente->contato2_parentesco }}</div>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-phone" style="color:#9ca3af;font-size:.8125rem;"></i>
                    <span>{{ $paciente->contato2_telefone }}</span>
                </div>
            </div>
        </div>
        @endif

        @if($paciente->logradouro)
        <div class="card">
            <div class="card-header">
                <i class="bi bi-geo-alt me-2" style="color:#6b7280;"></i>Endereço
            </div>
            <div class="card-body" style="font-size:.875rem;color:#6b7280;">
                {{ $paciente->logradouro }}, {{ $paciente->numero }}
                @if($paciente->complemento) — {{ $paciente->complemento }} @endif<br>
                {{ $paciente->bairro }} &middot; {{ $paciente->cidade }}/{{ $paciente->uf }}
                @if($paciente->cep) &middot; CEP {{ substr($paciente->cep, 0, 5) }}-{{ substr($paciente->cep, 5) }} @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Coluna direita --}}
    <div class="col-lg-8">
        @if($paciente->escola || $paciente->serie_escolar)
        <div class="card mb-4">
            <div class="card-body d-flex align-items-center gap-3" style="padding:.875rem 1.25rem;">
                <i class="bi bi-building" style="color:#6b7280;font-size:1.125rem;"></i>
                <div style="font-size:.875rem;">
                    <strong>{{ $paciente->escola }}</strong>
                    @if($paciente->serie_escolar)
                        <span style="color:#6b7280;"> &middot; {{ $paciente->serie_escolar }}</span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if($paciente->observacoes)
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-chat-text me-2" style="color:#6b7280;"></i>Observações Clínicas
            </div>
            <div class="card-body" style="font-size:.875rem;color:#374151;line-height:1.6;">
                {{ $paciente->observacoes }}
            </div>
        </div>
        @endif

        @if($contratos->isNotEmpty())
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-file-earmark-text me-2" style="color:#6b7280;"></i>Contratos Ativos
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Especialidade</th>
                            <th>Profissional</th>
                            <th>Sessões/sem.</th>
                            <th>Valor mensal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contratos as $c)
                        <tr>
                            <td>{{ $c->especialidade->label() }}</td>
                            <td>{{ $c->profissional->user->name }}</td>
                            <td>{{ $c->sessoes_por_semana }}×</td>
                            <td style="font-weight:600;">R$ {{ number_format($c->valor_mensal, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar3 me-2" style="color:#6b7280;"></i>Últimas Sessões</span>
                @can('agendar')
                <a href="{{ route('agenda.index') }}" class="btn btn-sm btn-link" style="font-size:.8125rem;">
                    Ver agenda <i class="bi bi-arrow-right ms-1"></i>
                </a>
                @endcan
            </div>
            @if($sessoes->isEmpty())
                <div class="d-flex flex-column align-items-center justify-content-center py-5" style="color:#9ca3af;">
                    <i class="bi bi-calendar3" style="font-size:2rem;opacity:.3;"></i>
                    <p class="mt-2 mb-0" style="font-size:.875rem;">Nenhuma sessão registrada.</p>
                </div>
            @else
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr><th>Data</th><th>Especialidade</th><th>Profissional</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach($sessoes as $s)
                        <tr>
                            <td style="color:#374151;">{{ $s->data_hora->format('d/m/Y H:i') }}</td>
                            <td>{{ $s->especialidade->label() }}</td>
                            <td style="color:#6b7280;">{{ $s->profissional->user->name }}</td>
                            <td>
                                @php
                                    $statusStyles = [
                                        'realizada'  => 'background:#dcfce7;color:#166534;',
                                        'cancelada'  => 'background:#fee2e2;color:#991b1b;',
                                        'faltou'     => 'background:#fee2e2;color:#991b1b;',
                                    ];
                                    $style = $statusStyles[$s->status] ?? 'background:#fef9c3;color:#854d0e;';
                                @endphp
                                <span class="badge" style="{{ $style }}">{{ ucfirst($s->status) }}</span>
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

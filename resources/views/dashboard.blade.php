@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-primary bg-opacity-10 p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#0d6efd" viewBox="0 0 16 16">
                        <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                    </svg>
                </div>
                <div>
                    <div class="text-muted small">Pacientes Ativos</div>
                    <div class="fs-4 fw-bold">{{ $totalPacientes }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-success bg-opacity-10 p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#198754" viewBox="0 0 16 16">
                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-muted small">Sessões Hoje</div>
                    <div class="fs-4 fw-bold">{{ $sessoesHoje }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-warning bg-opacity-10 p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#ffc107" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                    </svg>
                </div>
                <div>
                    <div class="text-muted small">Sessões Esta Semana</div>
                    <div class="fs-4 fw-bold">{{ $sessoesSemana }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-danger bg-opacity-10 p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#dc3545" viewBox="0 0 16 16">
                        <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.966c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.81 8.985.936 8 1.783"/>
                    </svg>
                </div>
                <div>
                    <div class="text-muted small">Contas em Aberto</div>
                    <div class="fs-4 fw-bold">{{ $contasAbertas }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold border-bottom">
                Próximas Sessões — Hoje
            </div>
            <div class="card-body p-0">
                @if($proximasSessoes->isEmpty())
                    <p class="text-muted text-center py-4 mb-0">Nenhuma sessão agendada para hoje.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Horário</th>
                                    <th>Paciente</th>
                                    <th>Especialidade</th>
                                    <th>Profissional</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proximasSessoes as $sessao)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($sessao->data_hora)->format('H:i') }}</td>
                                    <td>{{ $sessao->paciente->nome }}</td>
                                    <td>{{ $sessao->profissional->especialidade->label() }}</td>
                                    <td>{{ $sessao->profissional->user->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $sessao->status === 'realizada' ? 'success' : ($sessao->status === 'cancelada' ? 'danger' : 'warning text-dark') }}">
                                            {{ ucfirst($sessao->status) }}
                                        </span>
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

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold border-bottom">
                Especialidades Ativas
            </div>
            <div class="card-body">
                @foreach($especialidades as $esp)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>{{ $esp['label'] }}</span>
                    <span class="badge bg-primary rounded-pill">{{ $esp['total'] }} paciente{{ $esp['total'] !== 1 ? 's' : '' }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

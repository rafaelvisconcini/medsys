@extends('layouts.app')

@section('title', 'Configuração de Agenda')
@section('page-title', 'Configuração de Agenda')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Profissional</th>
                    <th>Especialidade</th>
                    <th>Dias configurados</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($profissionais as $prof)
                <tr>
                    <td class="fw-semibold">{{ $prof->user->name }}</td>
                    <td>{{ $prof->especialidade->label() }}</td>
                    <td>
                        @php
                            $dias = ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];
                            $ativos = $prof->agendaConfiguracoes->where('ativo', true)->pluck('dia_semana');
                        @endphp
                        @foreach ($dias as $i => $d)
                            <span class="badge {{ $ativos->contains($i) ? 'bg-success' : 'bg-secondary bg-opacity-25 text-secondary' }}">
                                {{ $d }}
                            </span>
                        @endforeach
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.agenda-config.edit', $prof) }}" class="btn btn-sm btn-outline-primary">
                            Configurar
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('admin.bloqueios.index') }}" class="btn btn-outline-secondary btn-sm">
        Gerenciar Bloqueios de Agenda
    </a>
</div>
@endsection

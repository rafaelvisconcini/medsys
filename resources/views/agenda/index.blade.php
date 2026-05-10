@extends('layouts.app')

@section('title', 'Agenda')
@section('page-title', 'Agenda')

@section('header-actions')
    @can('agendar')
    <a href="{{ route('sessoes.create') }}" class="btn btn-primary btn-sm">+ Nova Sessão</a>
    @endcan
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        {{-- Filtro por profissional --}}
        <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
            <label class="fw-semibold text-secondary mb-0 small">Profissional:</label>
            <select id="filtro-profissional" class="form-select form-select-sm" style="max-width:260px;">
                <option value="">Todos</option>
                @foreach ($profissionais as $prof)
                    <option value="{{ $prof->id }}">{{ $prof->user->name }} ({{ $prof->especialidade->label() }})</option>
                @endforeach
            </select>
        </div>

        <div id="calendario" style="min-height:600px;"></div>
    </div>
</div>

{{-- Modal de detalhe da sessão --}}
<div class="modal fade" id="modalSessao" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold" id="modalSessaoTitulo">Sessão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0" id="modalSessaoDetalhes"></dl>
            </div>
            <div class="modal-footer gap-2" id="modalSessaoAcoes"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendario');
    const statusLabels = {
        agendada: 'Agendada', confirmada: 'Confirmada', realizada: 'Realizada',
        cancelada: 'Cancelada', faltou: 'Faltou', reposicao: 'Reposição',
    };

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay',
        },
        slotMinTime: '07:00:00',
        slotMaxTime: '20:00:00',
        allDaySlot: false,
        height: 'auto',
        nowIndicator: true,
        businessHours: true,
        editable: false,
        selectable: @can('agendar') true @else false @endcan,
        select: function (info) {
            const url = new URL('{{ route('sessoes.create') }}', window.location.origin);
            url.searchParams.set('data', info.startStr.slice(0, 10));
            url.searchParams.set('hora', info.startStr.slice(11, 16));
            const profId = document.getElementById('filtro-profissional').value;
            if (profId) url.searchParams.set('profissional_id', profId);
            window.location.href = url.toString();
        },
        events: function (fetchInfo, successCallback, failureCallback) {
            const profId = document.getElementById('filtro-profissional').value;
            const url = new URL('{{ route('agenda.slots') }}', window.location.origin);
            url.searchParams.set('start', fetchInfo.startStr);
            url.searchParams.set('end', fetchInfo.endStr);
            if (profId) url.searchParams.set('profissional_id', profId);

            fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(successCallback)
            .catch(failureCallback);
        },
        eventClick: function (info) {
            const p = info.event.extendedProps;
            document.getElementById('modalSessaoTitulo').textContent = p.paciente;

            document.getElementById('modalSessaoDetalhes').innerHTML = `
                <dt class="col-5">Profissional</dt><dd class="col-7">${p.profissional}</dd>
                <dt class="col-5">Especialidade</dt><dd class="col-7">${p.especialidade}</dd>
                <dt class="col-5">Início</dt><dd class="col-7">${info.event.start.toLocaleTimeString('pt-BR', {hour:'2-digit',minute:'2-digit'})}</dd>
                <dt class="col-5">Fim</dt><dd class="col-7">${info.event.end ? info.event.end.toLocaleTimeString('pt-BR', {hour:'2-digit',minute:'2-digit'}) : '—'}</dd>
                <dt class="col-5">Status</dt><dd class="col-7"><span class="badge" style="background:${info.event.backgroundColor}">${statusLabels[p.status] ?? p.status}</span></dd>
            `;

            const acoes = document.getElementById('modalSessaoAcoes');
            acoes.innerHTML = `
                <a href="${p.edit_url}" class="btn btn-sm btn-outline-primary">Editar</a>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Fechar</button>
            `;

            new bootstrap.Modal(document.getElementById('modalSessao')).show();
        },
    });

    calendar.render();

    document.getElementById('filtro-profissional').addEventListener('change', function () {
        calendar.refetchEvents();
    });
});
</script>
@endpush

@extends('layouts.app')

@section('title', 'Agenda — ' . $profissional->user->name)
@section('page-title', 'Agenda de ' . $profissional->user->name)

@section('header-actions')
    <a href="{{ route('admin.agenda-config.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Voltar</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                <form action="{{ route('admin.agenda-config.update', $profissional) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:90px">Ativo</th>
                                    <th style="width:110px">Dia</th>
                                    <th>Início</th>
                                    <th>Fim</th>
                                    <th>Slot (min)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dias as $i => $nomeDia)
                                @php $c = $configs->get($i); @endphp
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input dia-toggle" type="checkbox"
                                                   name="dias[{{ $i }}][ativo]"
                                                   id="ativo_{{ $i }}"
                                                   data-dia="{{ $i }}"
                                                   @checked($c && $c->ativo)>
                                        </div>
                                    </td>
                                    <td><label for="ativo_{{ $i }}" class="fw-semibold mb-0">{{ $nomeDia }}</label></td>
                                    <td>
                                        <input type="time" name="dias[{{ $i }}][hora_inicio]"
                                               class="form-control form-control-sm dia-campo-{{ $i }}"
                                               value="{{ $c->hora_inicio ?? '08:00' }}"
                                               @if(!$c || !$c->ativo) disabled @endif>
                                    </td>
                                    <td>
                                        <input type="time" name="dias[{{ $i }}][hora_fim]"
                                               class="form-control form-control-sm dia-campo-{{ $i }}"
                                               value="{{ $c->hora_fim ?? '18:00' }}"
                                               @if(!$c || !$c->ativo) disabled @endif>
                                    </td>
                                    <td>
                                        <input type="number" name="dias[{{ $i }}][duracao_slot_min]"
                                               class="form-control form-control-sm dia-campo-{{ $i }}"
                                               value="{{ $c->duracao_slot_min ?? $profissional->duracao_sessao_min ?? 50 }}"
                                               min="15" max="120" step="5"
                                               @if(!$c || !$c->ativo) disabled @endif>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('admin.agenda-config.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Salvar Configuração</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.dia-toggle').forEach(cb => {
    cb.addEventListener('change', function () {
        document.querySelectorAll(`.dia-campo-${this.dataset.dia}`).forEach(el => {
            el.disabled = !this.checked;
        });
    });
});
</script>
@endpush

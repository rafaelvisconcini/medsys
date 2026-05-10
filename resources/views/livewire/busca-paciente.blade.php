<div class="position-relative" style="max-width: 380px;">
    <input
        type="text"
        class="form-control form-control-sm"
        placeholder="Buscar paciente..."
        wire:model.live.debounce.300ms="termo"
        wire:keydown.escape="$set('aberto', false)"
        autocomplete="off"
    >

    @if($aberto && $resultados->isNotEmpty())
    <div class="position-absolute top-100 start-0 w-100 bg-white border rounded-2 shadow mt-1 z-3">
        @foreach($resultados as $p)
        <button
            type="button"
            class="d-flex align-items-center gap-2 w-100 px-3 py-2 border-0 bg-transparent text-start hover-bg-light"
            wire:click="selecionar({{ $p->id }})"
        >
            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:32px;height:32px;font-size:.8rem;font-weight:600;color:#0d6efd;">
                {{ strtoupper(substr($p->nome, 0, 1)) }}
            </div>
            <div class="overflow-hidden">
                <div class="fw-semibold text-truncate" style="font-size:.875rem;">{{ $p->nome }}</div>
                <div class="text-muted" style="font-size:.75rem;">
                    {{ $p->data_nascimento->age }} anos · Resp: {{ $p->responsavel_nome }}
                </div>
            </div>
        </button>
        @endforeach
    </div>
    @endif

    @if($aberto && $resultados->isEmpty() && strlen($termo) >= 2)
    <div class="position-absolute top-100 start-0 w-100 bg-white border rounded-2 shadow mt-1 z-3">
        <p class="text-muted text-center py-3 mb-0 small">Nenhum paciente encontrado.</p>
    </div>
    @endif
</div>

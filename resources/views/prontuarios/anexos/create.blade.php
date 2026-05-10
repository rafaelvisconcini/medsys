@extends('layouts.app')
@section('title', 'Anexar Arquivo')
@section('page-title', 'Novo Anexo — ' . $prontuario->paciente->nome)
@section('header-actions')
    <a href="{{ route('prontuarios.show', $prontuario->paciente_id) }}" class="btn btn-outline-secondary btn-sm">&larr; Prontuário</a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                <form action="{{ route('prontuarios.anexos.store', $prontuario) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        {{-- Arquivo --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Arquivo <span class="text-danger">*</span>
                                <span class="text-muted fw-normal small">— PDF, imagem ou documento · máx. 20 MB</span>
                            </label>
                            <input type="file"
                                   name="arquivo"
                                   id="arquivo"
                                   accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,.doc,.docx"
                                   class="form-control @error('arquivo') is-invalid @enderror"
                                   required>
                            @error('arquivo') <div class="invalid-feedback">{{ $message }}</div> @enderror

                            {{-- Preview de imagem --}}
                            <div id="img-preview-wrap" class="mt-2 d-none">
                                <img id="img-preview" src="" alt="pré-visualização"
                                     class="img-thumbnail" style="max-height:200px;">
                            </div>
                            <div id="file-info" class="text-muted small mt-1"></div>
                        </div>

                        {{-- Tipo --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipo <span class="text-danger">*</span></label>
                            <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                                <option value="">Selecione...</option>
                                <option value="avaliacao"  @selected(old('tipo') === 'avaliacao')>Avaliação</option>
                                <option value="laudo"      @selected(old('tipo') === 'laudo')>Laudo</option>
                                <option value="relatorio"  @selected(old('tipo') === 'relatorio')>Relatório</option>
                                <option value="imagem"     @selected(old('tipo') === 'imagem')>Imagem</option>
                                <option value="outro"      @selected(old('tipo') === 'outro')>Outro</option>
                            </select>
                            @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Data do documento --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Data do documento
                                <span class="text-muted fw-normal">(opcional)</span>
                            </label>
                            <input type="date"
                                   name="data_documento"
                                   value="{{ old('data_documento') }}"
                                   class="form-control @error('data_documento') is-invalid @enderror">
                            @error('data_documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Descrição --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Descrição
                                <span class="text-muted fw-normal">(opcional)</span>
                            </label>
                            <input type="text"
                                   name="descricao"
                                   value="{{ old('descricao') }}"
                                   maxlength="200"
                                   placeholder="Ex: Laudo de avaliação neuropsicológica 2026"
                                   class="form-control @error('descricao') is-invalid @enderror">
                            @error('descricao') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Evolução vinculada (opcional) --}}
                        @if($evolucoes->isNotEmpty())
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Vincular à evolução
                                <span class="text-muted fw-normal">(opcional)</span>
                            </label>
                            <select name="evolucao_id"
                                    class="form-select @error('evolucao_id') is-invalid @enderror">
                                <option value="">Sem vínculo</option>
                                @foreach($evolucoes as $ev)
                                <option value="{{ $ev->id }}" @selected(old('evolucao_id') == $ev->id)>
                                    {{ $ev->data_hora->format('d/m/Y') }}
                                    — {{ $ev->especialidade->label() }}
                                    ({{ $ev->profissional->user->name }})
                                </option>
                                @endforeach
                            </select>
                            @error('evolucao_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        @endif

                        <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                            <a href="{{ route('prontuarios.show', $prontuario->paciente_id) }}"
                               class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Salvar Anexo</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('arquivo').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    const info = document.getElementById('file-info');
    const wrap = document.getElementById('img-preview-wrap');
    const prev = document.getElementById('img-preview');

    const kb = (file.size / 1024).toFixed(0);
    const mb = (file.size / (1024 * 1024)).toFixed(2);
    info.textContent = file.name + ' — ' + (file.size >= 1024 * 1024 ? mb + ' MB' : kb + ' KB');

    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = e => { prev.src = e.target.result; wrap.classList.remove('d-none'); };
        reader.readAsDataURL(file);
    } else {
        wrap.classList.add('d-none');
    }
});
</script>
@endpush
@endsection

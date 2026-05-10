<div class="esp-row border rounded p-3 mb-3 bg-light">
    <div class="d-flex justify-content-end mb-2">
        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-esp">Remover</button>
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Profissional <span class="text-danger">*</span></label>
            <select name="especialidades[{{ $index }}][profissional_id]"
                    class="form-select @error("especialidades.{$index}.profissional_id") is-invalid @enderror" required>
                <option value="">Selecione...</option>
                @foreach($profissionais as $prof)
                    <option value="{{ $prof->id }}"
                        @selected(($valores['profissional_id'] ?? null) == $prof->id)>
                        {{ $prof->user->name }} ({{ $prof->especialidade->label() }})
                    </option>
                @endforeach
            </select>
            @error("especialidades.{$index}.profissional_id")
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Especialidade <span class="text-danger">*</span></label>
            <select name="especialidades[{{ $index }}][especialidade]"
                    class="form-select @error("especialidades.{$index}.especialidade") is-invalid @enderror" required>
                <option value="">Selecione...</option>
                @foreach($especialidades as $esp)
                    <option value="{{ $esp->value }}"
                        @selected(($valores['especialidade'] ?? null) === $esp->value)>
                        {{ $esp->label() }}
                    </option>
                @endforeach
            </select>
            @error("especialidades.{$index}.especialidade")
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <label class="form-label fw-semibold">Objetivos gerais</label>
            <textarea name="especialidades[{{ $index }}][objetivos_gerais]"
                      rows="2"
                      class="form-control">{{ $valores['objetivos_gerais'] ?? '' }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Objetivos específicos</label>
            <textarea name="especialidades[{{ $index }}][objetivos_especificos]"
                      rows="2"
                      class="form-control">{{ $valores['objetivos_especificos'] ?? '' }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Estratégias</label>
            <textarea name="especialidades[{{ $index }}][estrategias]"
                      rows="2"
                      class="form-control">{{ $valores['estrategias'] ?? '' }}</textarea>
        </div>
    </div>
</div>

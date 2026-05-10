<?php

namespace App\Http\Controllers\Prontuario;

use App\Enums\PerfilUsuario;
use App\Http\Controllers\Controller;
use App\Models\ProntuarioAnexo;
use App\Models\Prontuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProntuarioAnexoController extends Controller
{
    private const TIPOS = ['avaliacao', 'laudo', 'relatorio', 'imagem', 'outro'];

    private const MIMES_PERMITIDOS = 'pdf,jpg,jpeg,png,gif,webp,doc,docx';

    private const MAX_KB = 20480; // 20 MB

    public function create(Prontuario $prontuario)
    {
        $this->authorize('view', $prontuario->paciente);

        $evolucoes = $prontuario->evolucoes()
            ->with('profissional.user')
            ->orderByDesc('data_hora')
            ->limit(30)
            ->get();

        return view('prontuarios.anexos.create', compact('prontuario', 'evolucoes'));
    }

    public function store(Request $request, Prontuario $prontuario)
    {
        $this->authorize('view', $prontuario->paciente);

        $dados = $request->validate([
            'arquivo'        => ['required', 'file', 'mimes:' . self::MIMES_PERMITIDOS, 'max:' . self::MAX_KB],
            'tipo'           => ['required', 'in:' . implode(',', self::TIPOS)],
            'descricao'      => ['nullable', 'string', 'max:200'],
            'data_documento' => ['nullable', 'date'],
            'evolucao_id'    => ['nullable', 'exists:evolucoes,id'],
        ]);

        $arquivo    = $request->file('arquivo');
        $extensao   = $arquivo->getClientOriginalExtension();
        $nomeUnico  = Str::uuid() . '.' . $extensao;
        $pasta      = 'prontuarios/' . $prontuario->id;

        $arquivo->storeAs($pasta, $nomeUnico, 'local');

        ProntuarioAnexo::create([
            'prontuario_id'  => $prontuario->id,
            'evolucao_id'    => $dados['evolucao_id'] ?? null,
            'tipo'           => $dados['tipo'],
            'nome_original'  => $arquivo->getClientOriginalName(),
            'path'           => $pasta . '/' . $nomeUnico,
            'mime_type'      => $arquivo->getMimeType(),
            'tamanho_bytes'  => $arquivo->getSize(),
            'descricao'      => $dados['descricao'] ?? null,
            'data_documento' => $dados['data_documento'] ?? null,
            'uploaded_por'   => auth()->id(),
        ]);

        return redirect()
            ->route('prontuarios.show', $prontuario->paciente_id)
            ->with('success', 'Arquivo anexado com sucesso.')
            ->withFragment('tab-anexos');
    }

    public function download(ProntuarioAnexo $anexo)
    {
        $this->authorize('view', $anexo->prontuario->paciente);

        if (! Storage::disk('local')->exists($anexo->path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return Storage::disk('local')->download($anexo->path, $anexo->nome_original);
    }

    public function destroy(ProntuarioAnexo $anexo)
    {
        $this->authorize('view', $anexo->prontuario->paciente);

        $user = auth()->user();
        if ($user->perfil !== PerfilUsuario::Admin && $anexo->uploaded_por !== $user->id) {
            abort(403, 'Apenas o responsável pelo upload ou um administrador pode remover este anexo.');
        }

        Storage::disk('local')->delete($anexo->path);

        $pacienteId = $anexo->prontuario->paciente_id;
        $anexo->delete();

        return redirect()
            ->route('prontuarios.show', $pacienteId)
            ->with('success', 'Anexo removido.')
            ->withFragment('tab-anexos');
    }
}

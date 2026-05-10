<?php

namespace App\Http\Controllers\Lgpd;

use App\Http\Controllers\Controller;
use App\Models\Paciente;

class ExportController extends Controller
{
    /**
     * Exporta todos os dados pessoais do paciente em JSON (LGPD Art. 18 — Portabilidade).
     */
    public function paciente(Paciente $paciente)
    {
        $this->authorize('view', $paciente);

        $paciente->load([
            'sessoes.profissional.user',
            'prontuario.evolucoes.profissional.user',
            'prontuario.encaminhamentos.profissional.user',
            'contratos.profissional.user',
        ]);

        $dados = [
            'exportado_em'    => now()->toIso8601String(),
            'sistema'         => config('app.name'),
            'dados_pessoais'  => [
                'nome'                   => $paciente->nome,
                'data_nascimento'        => $paciente->data_nascimento?->toDateString(),
                'sexo'                   => $paciente->sexo,
                'escola'                 => $paciente->escola,
                'serie_escolar'          => $paciente->serie_escolar,
                'diagnosticos'           => $paciente->diagnosticos,
                'cep'                    => $paciente->cep,
                'logradouro'             => $paciente->logradouro,
                'numero'                 => $paciente->numero,
                'complemento'            => $paciente->complemento,
                'bairro'                 => $paciente->bairro,
                'cidade'                 => $paciente->cidade,
                'uf'                     => $paciente->uf,
            ],
            'responsavel' => [
                'nome'          => $paciente->responsavel_nome,
                'cpf'           => $paciente->responsavel_cpf,
                'parentesco'    => $paciente->responsavel_parentesco,
                'telefone'      => $paciente->responsavel_telefone,
                'celular'       => $paciente->responsavel_celular,
                'email'         => $paciente->responsavel_email,
            ],
            'sessoes' => $paciente->sessoes->map(fn($s) => [
                'data_hora'     => $s->data_hora->toIso8601String(),
                'especialidade' => $s->especialidade->label(),
                'profissional'  => $s->profissional->user->name,
                'duracao_min'   => $s->duracao_min,
                'status'        => $s->status,
                'tipo'          => $s->tipo,
            ]),
            'evolucoes' => $paciente->prontuario?->evolucoes->map(fn($e) => [
                'data_hora'             => $e->data_hora->toIso8601String(),
                'especialidade'         => $e->especialidade->label(),
                'profissional'          => $e->profissional->user->name,
                'descricao'             => $e->descricao,
                'objetivos_trabalhados' => $e->objetivos_trabalhados,
                'resposta_paciente'     => $e->resposta_paciente,
                'proximos_objetivos'    => $e->proximos_objetivos,
                'cids'                  => $e->cids,
            ]),
        ];

        $nomeArquivo = 'dados_' . str()->slug($paciente->nome) . '_' . now()->format('Ymd') . '.json';

        return response()->json($dados, 200, [
            'Content-Disposition' => 'attachment; filename="' . $nomeArquivo . '"',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}

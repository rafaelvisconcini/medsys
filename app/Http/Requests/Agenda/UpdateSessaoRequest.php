<?php

namespace App\Http\Requests\Agenda;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSessaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('sessao'));
    }

    public function rules(): array
    {
        return [
            'paciente_id'     => 'required|exists:pacientes,id',
            'profissional_id' => 'required|exists:profissionais,id',
            'data_hora'       => 'required|date',
            'duracao_min'     => 'required|integer|min:15|max:240',
            'especialidade'   => 'required|string',
            'tipo'            => 'required|in:avulsa,plano,reposicao',
            'observacoes'     => 'nullable|string|max:1000',
            'contrato_id'     => 'nullable|exists:contratos,id',
        ];
    }

    public function messages(): array
    {
        return [
            'paciente_id.required'     => 'Selecione o paciente.',
            'profissional_id.required' => 'Selecione o profissional.',
            'data_hora.required'       => 'Informe a data e horário.',
            'duracao_min.min'          => 'Duração mínima de 15 minutos.',
            'duracao_min.max'          => 'Duração máxima de 240 minutos.',
            'especialidade.required'   => 'Informe a especialidade.',
            'tipo.required'            => 'Informe o tipo de sessão.',
        ];
    }
}

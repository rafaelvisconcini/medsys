<?php

namespace App\Http\Requests\Agenda;

use Illuminate\Foundation\Http\FormRequest;

class StoreSessaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('agendar');
    }

    public function rules(): array
    {
        return [
            'paciente_id'      => 'required|exists:pacientes,id',
            'profissional_id'  => 'required|exists:profissionais,id',
            'data_hora'        => 'required|date|after:now',
            'duracao_min'      => 'required|integer|min:15|max:240',
            'especialidade'    => 'required|string',
            'tipo'             => 'required|in:avulsa,plano,reposicao',
            'observacoes'      => 'nullable|string|max:1000',
            'contrato_id'      => 'nullable|exists:contratos,id',
        ];
    }

    public function messages(): array
    {
        return [
            'paciente_id.required'     => 'Selecione o paciente.',
            'paciente_id.exists'       => 'Paciente não encontrado.',
            'profissional_id.required' => 'Selecione o profissional.',
            'profissional_id.exists'   => 'Profissional não encontrado.',
            'data_hora.required'       => 'Informe a data e horário.',
            'data_hora.after'          => 'O agendamento deve ser em uma data futura.',
            'duracao_min.required'     => 'Informe a duração da sessão.',
            'duracao_min.min'          => 'Duração mínima de 15 minutos.',
            'duracao_min.max'          => 'Duração máxima de 240 minutos.',
            'especialidade.required'   => 'Informe a especialidade.',
            'tipo.required'            => 'Informe o tipo de sessão.',
            'tipo.in'                  => 'Tipo inválido.',
        ];
    }
}

<?php

namespace App\Http\Requests\Pacientes;

use Illuminate\Foundation\Http\FormRequest;

class StorePacienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Paciente::class);
    }

    public function rules(): array
    {
        return [
            'nome'                    => ['required', 'string', 'max:150'],
            'data_nascimento'         => ['required', 'date', 'before:today'],
            'sexo'                    => ['nullable', 'in:M,F'],
            'escola'                  => ['nullable', 'string', 'max:150'],
            'serie_escolar'           => ['nullable', 'string', 'max:40'],
            'diagnosticos'            => ['nullable', 'array'],
            'diagnosticos.*'          => ['string', 'max:20'],
            'observacoes'             => ['nullable', 'string'],
            'foto'                    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            // Responsável
            'responsavel_nome'        => ['required', 'string', 'max:150'],
            'responsavel_parentesco'  => ['required', 'string', 'max:40'],
            'responsavel_celular'     => ['required', 'string', 'max:15'],
            'responsavel_cpf'         => ['nullable', 'string', 'size:11'],
            'responsavel_telefone'    => ['nullable', 'string', 'max:15'],
            'responsavel_email'       => ['nullable', 'email', 'max:120'],
            // Contato 2
            'contato2_nome'           => ['nullable', 'string', 'max:150'],
            'contato2_telefone'       => ['nullable', 'string', 'max:15'],
            'contato2_parentesco'     => ['nullable', 'string', 'max:40'],
            // Endereço
            'cep'                     => ['nullable', 'string', 'size:8'],
            'logradouro'              => ['nullable', 'string', 'max:150'],
            'numero'                  => ['nullable', 'string', 'max:10'],
            'complemento'             => ['nullable', 'string', 'max:60'],
            'bairro'                  => ['nullable', 'string', 'max:80'],
            'cidade'                  => ['nullable', 'string', 'max:80'],
            'uf'                      => ['nullable', 'string', 'size:2'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required'                   => 'O nome do paciente é obrigatório.',
            'data_nascimento.required'        => 'A data de nascimento é obrigatória.',
            'data_nascimento.before'          => 'A data de nascimento deve ser no passado.',
            'responsavel_nome.required'       => 'O nome do responsável é obrigatório.',
            'responsavel_parentesco.required' => 'O parentesco do responsável é obrigatório.',
            'responsavel_celular.required'    => 'O celular do responsável é obrigatório.',
            'foto.image'                      => 'O arquivo enviado deve ser uma imagem.',
            'foto.max'                        => 'A foto não pode ter mais de 2 MB.',
        ];
    }
}

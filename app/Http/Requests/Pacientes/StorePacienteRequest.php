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
            // Paciente
            'nome.required'                      => 'O nome do paciente é obrigatório.',
            'nome.max'                           => 'O nome não pode ter mais de 150 caracteres.',
            'data_nascimento.required'           => 'A data de nascimento é obrigatória.',
            'data_nascimento.date'               => 'Data de nascimento inválida.',
            'data_nascimento.before'             => 'A data de nascimento deve ser no passado.',
            'foto.image'                         => 'O arquivo enviado deve ser uma imagem.',
            'foto.mimes'                         => 'A foto deve estar no formato JPG, PNG ou WebP.',
            'foto.max'                           => 'A foto não pode ter mais de 2 MB.',
            // Responsável
            'responsavel_nome.required'          => 'O nome do responsável é obrigatório.',
            'responsavel_nome.max'               => 'O nome do responsável não pode ter mais de 150 caracteres.',
            'responsavel_parentesco.required'    => 'O parentesco do responsável é obrigatório.',
            'responsavel_parentesco.max'         => 'O parentesco não pode ter mais de 40 caracteres.',
            'responsavel_celular.required'       => 'O celular do responsável é obrigatório.',
            'responsavel_celular.max'            => 'O número de celular deve ter no máximo 15 caracteres.',
            'responsavel_cpf.size'               => 'O CPF deve ter exatamente 11 dígitos (somente números).',
            'responsavel_telefone.max'           => 'O número de telefone fixo deve ter no máximo 15 caracteres.',
            'responsavel_email.email'            => 'Informe um endereço de e-mail válido.',
            'responsavel_email.max'              => 'O e-mail não pode ter mais de 120 caracteres.',
            // Contato 2
            'contato2_nome.max'                  => 'O nome do contato não pode ter mais de 150 caracteres.',
            'contato2_telefone.max'              => 'O telefone do contato deve ter no máximo 15 caracteres.',
            'contato2_parentesco.max'            => 'O parentesco não pode ter mais de 40 caracteres.',
            // Endereço
            'cep.size'                           => 'O CEP deve ter exatamente 8 dígitos (somente números).',
            'uf.size'                            => 'Informe a UF com 2 letras (ex: PR).',
        ];
    }
}

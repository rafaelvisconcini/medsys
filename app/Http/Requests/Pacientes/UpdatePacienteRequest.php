<?php

namespace App\Http\Requests\Pacientes;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePacienteRequest extends StorePacienteRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('paciente'));
    }
}

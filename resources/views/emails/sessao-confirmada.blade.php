@extends('emails.layout')

@section('content')
<h2>Sessão Agendada com Sucesso</h2>

<p>Olá, <strong>{{ $sessao->paciente->responsavel_nome }}</strong>.</p>
<p>
    Informamos que uma sessão foi agendada para
    <strong>{{ $sessao->paciente->nome }}</strong> em nosso centro terapêutico.
    Confira os detalhes abaixo:
</p>

<div class="info-box">
    <table>
        <tr>
            <td>Paciente:</td>
            <td>{{ $sessao->paciente->nome }}</td>
        </tr>
        <tr>
            <td>Profissional:</td>
            <td>{{ $sessao->profissional->user->name }}</td>
        </tr>
        <tr>
            <td>Especialidade:</td>
            <td>{{ $sessao->especialidade->label() }}</td>
        </tr>
        <tr>
            <td>Data:</td>
            <td>{{ $sessao->data_hora->translatedFormat('l, d \d\e F \d\e Y') }}</td>
        </tr>
        <tr>
            <td>Horário:</td>
            <td>{{ $sessao->data_hora->format('H:i') }} ({{ $sessao->duracao_min }} minutos)</td>
        </tr>
        <tr>
            <td>Status:</td>
            <td><span class="badge badge-blue">{{ ucfirst($sessao->status) }}</span></td>
        </tr>
    </table>
</div>

<p style="font-size:14px;color:#6b7280;">
    Caso precise cancelar ou reagendar, entre em contato com a clínica com pelo menos 24 horas de antecedência.
</p>
@endsection

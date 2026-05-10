@extends('emails.layout')

@section('content')
<h2>Lembrete: Sessão Amanhã</h2>

<p>Olá, <strong>{{ $sessao->paciente->responsavel_nome }}</strong>.</p>
<p>
    Este é um lembrete de que <strong>{{ $sessao->paciente->nome }}</strong>
    tem uma sessão agendada <strong>amanhã</strong>. Veja os detalhes:
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
            <td><strong>{{ $sessao->data_hora->format('H:i') }}</strong> ({{ $sessao->duracao_min }} minutos)</td>
        </tr>
        <tr>
            <td>Status:</td>
            <td><span class="badge badge-green">Confirmada</span></td>
        </tr>
    </table>
</div>

<p style="font-size:14px;color:#6b7280;">
    Por favor, chegue com <strong>10 minutos de antecedência</strong>.<br>
    Se não puder comparecer, contate a clínica o quanto antes.
</p>
@endsection

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11pt; color: #222; margin: 0; padding: 0; }
        .page { padding: 40px 50px; }

        .header { border-bottom: 2px solid #1d4ed8; padding-bottom: 16px; margin-bottom: 24px; display: table; width: 100%; }
        .header-left { display: table-cell; vertical-align: middle; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; font-size: 9pt; color: #555; }
        .clinic-name { font-size: 16pt; font-weight: bold; color: #1d4ed8; margin: 0 0 2px; }
        .clinic-sub  { font-size: 9pt; color: #555; margin: 0; }

        h2 { font-size: 14pt; font-weight: bold; text-align: center; margin: 0 0 24px; letter-spacing: 1px; text-transform: uppercase; color: #1d4ed8; }

        .section { margin-bottom: 20px; }
        .label { font-weight: bold; color: #444; font-size: 9.5pt; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
        .value { border-bottom: 1px solid #ddd; padding-bottom: 6px; min-height: 22px; }

        table.grid { width: 100%; border-collapse: collapse; }
        table.grid td { padding: 6px 8px; }
        table.grid td:first-child { width: 40%; }

        .box { border: 1px solid #ccc; border-radius: 4px; padding: 12px; min-height: 80px; background: #fafafa; }

        .signature { margin-top: 60px; text-align: center; }
        .sig-line { border-top: 1px solid #333; width: 280px; margin: 0 auto 6px; }
        .sig-label { font-size: 9.5pt; color: #444; }

        .footer { margin-top: 40px; border-top: 1px solid #ddd; padding-top: 10px; font-size: 8pt; color: #999; text-align: center; }
    </style>
</head>
<body>
<div class="page">

    <div class="header">
        <div class="header-left">
            <p class="clinic-name">{{ config('app.name') }}</p>
            <p class="clinic-sub">Centro Terapêutico</p>
        </div>
        <div class="header-right">
            Data: {{ $encaminhamento->data->format('d/m/Y') }}<br>
            Prontuário Nº {{ $encaminhamento->prontuario->numero }}
        </div>
    </div>

    <h2>Encaminhamento</h2>

    <table class="grid section">
        <tr>
            <td>
                <div class="label">Paciente</div>
                <div class="value">{{ $encaminhamento->prontuario->paciente->nome }}</div>
            </td>
            <td>
                <div class="label">Data de Nascimento</div>
                <div class="value">{{ $encaminhamento->prontuario->paciente->data_nascimento->format('d/m/Y') }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Profissional Solicitante</div>
                <div class="value">{{ $encaminhamento->profissional->user->name }}</div>
            </td>
            <td>
                <div class="label">Especialidade</div>
                <div class="value">{{ $encaminhamento->profissional->especialidade->label() }}</div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="label">Encaminhar Para</div>
                <div class="value">{{ $encaminhamento->para_especialidade }}</div>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="label">Motivo do Encaminhamento</div>
        <div class="box">{{ $encaminhamento->motivo }}</div>
    </div>

    @if($encaminhamento->observacoes)
    <div class="section">
        <div class="label">Observações</div>
        <div class="box">{{ $encaminhamento->observacoes }}</div>
    </div>
    @endif

    <div class="signature">
        <div class="sig-line"></div>
        <div class="sig-label">
            {{ $encaminhamento->profissional->user->name }}<br>
            {{ $encaminhamento->profissional->especialidade->label() }}
            — {{ $encaminhamento->profissional->especialidade->registro() }}
            {{ $encaminhamento->profissional->registro_profissional }}
        </div>
    </div>

    <div class="footer">
        Documento gerado em {{ now()->format('d/m/Y \à\s H:i') }} — {{ config('app.name') }}
    </div>
</div>
</body>
</html>

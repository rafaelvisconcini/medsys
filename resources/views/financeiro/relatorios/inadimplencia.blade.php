<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1d4ed8; padding-bottom: 10px; }
        .header h1 { font-size: 16px; color: #1d4ed8; margin-bottom: 4px; }
        .header p { color: #555; font-size: 10px; }
        .summary { display: flex; gap: 16px; margin-bottom: 16px; }
        .summary-box { flex: 1; border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px 12px; background: #f9fafb; }
        .summary-box .label { font-size: 10px; color: #6b7280; }
        .summary-box .value { font-size: 14px; font-weight: bold; color: #dc2626; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead th { background: #1d4ed8; color: white; padding: 7px 8px; text-align: left; font-size: 10px; }
        tbody tr:nth-child(even) { background: #f3f4f6; }
        tbody td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 10px; vertical-align: top; }
        .badge-danger { background: #fee2e2; color: #dc2626; padding: 2px 6px; border-radius: 4px; font-size: 9px; }
        .dias-atraso { color: #dc2626; font-weight: bold; }
        .footer { margin-top: 20px; text-align: right; font-size: 9px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 8px; }
        .no-data { text-align: center; padding: 30px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Centro Terapêutico — Relatório de Inadimplência</h1>
        <p>Gerado em {{ $hoje->format('d/m/Y \à\s H:i') }} · Contas vencidas e não quitadas</p>
    </div>

    <div class="summary">
        <div class="summary-box">
            <div class="label">Total de contas em atraso</div>
            <div class="value" style="color:#1d4ed8;">{{ $contas->count() }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Valor total em aberto</div>
            <div class="value">R$ {{ number_format($totalEmAberto, 2, ',', '.') }}</div>
        </div>
    </div>

    @if($contas->isEmpty())
    <div class="no-data">Nenhuma conta em atraso encontrada.</div>
    @else
    <table>
        <thead>
            <tr>
                <th>Paciente</th>
                <th>Descrição</th>
                <th>Vencimento</th>
                <th>Dias em atraso</th>
                <th>Valor em aberto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contas as $conta)
            @php
                $diasAtraso = $conta->data_vencimento->diffInDays($hoje);
                $emAberto = $conta->parcelas->where('status', 'pendente')->sum('valor');
            @endphp
            <tr>
                <td>{{ $conta->paciente->nome }}</td>
                <td>{{ $conta->descricao }}</td>
                <td>{{ $conta->data_vencimento->format('d/m/Y') }}</td>
                <td><span class="dias-atraso">{{ $diasAtraso }} dias</span></td>
                <td>R$ {{ number_format($emAberto, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">MedSys · Centro Terapêutico — Documento gerado automaticamente</div>
</body>
</html>

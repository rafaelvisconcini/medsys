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
        .summary { margin-bottom: 16px; display: flex; gap: 12px; flex-wrap: wrap; }
        .summary-box { border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px 14px; background: #f9fafb; min-width: 130px; }
        .summary-box .label { font-size: 10px; color: #6b7280; }
        .summary-box .value { font-size: 13px; font-weight: bold; margin-top: 2px; }
        .value-green { color: #16a34a; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead th { background: #1d4ed8; color: white; padding: 7px 8px; text-align: left; font-size: 10px; }
        tbody tr:nth-child(even) { background: #f3f4f6; }
        tbody td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 10px; }
        .footer { margin-top: 20px; text-align: right; font-size: 9px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 8px; }
        .no-data { text-align: center; padding: 30px; color: #6b7280; }
        .forma-label { text-transform: capitalize; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Centro Terapêutico — Extrato de Recebimentos</h1>
        <p>Período: {{ $de->format('d/m/Y') }} a {{ $ate->format('d/m/Y') }}</p>
    </div>

    <div class="summary">
        <div class="summary-box">
            <div class="label">Total recebido no período</div>
            <div class="value value-green">R$ {{ number_format($totalRecebido, 2, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Número de pagamentos</div>
            <div class="value" style="color:#1d4ed8;">{{ $pagamentos->count() }}</div>
        </div>
        @foreach($porFormaPagamento as $forma => $valor)
        <div class="summary-box">
            <div class="label">{{ ucfirst(str_replace('_', ' ', $forma)) }}</div>
            <div class="value">R$ {{ number_format($valor, 2, ',', '.') }}</div>
        </div>
        @endforeach
    </div>

    @if($pagamentos->isEmpty())
    <div class="no-data">Nenhum pagamento registrado no período.</div>
    @else
    <table>
        <thead>
            <tr>
                <th>Data pgto.</th>
                <th>Paciente</th>
                <th>Descrição</th>
                <th>Forma</th>
                <th>Valor (R$)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pagamentos as $p)
            <tr>
                <td>{{ $p->data_pagamento->format('d/m/Y') }}</td>
                <td>{{ $p->conta->paciente->nome }}</td>
                <td>{{ $p->conta->descricao }}</td>
                <td class="forma-label">{{ str_replace('_', ' ', $p->forma_pagamento ?? '—') }}</td>
                <td>{{ number_format($p->valor, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">Theraflow · Centro Terapêutico — Documento gerado automaticamente</div>
</body>
</html>

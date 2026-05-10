<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; color: #333; }
        .container { max-width: 580px; margin: 32px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .header { background: #1d4ed8; padding: 24px 32px; }
        .header h1 { color: #fff; margin: 0; font-size: 20px; font-weight: 700; }
        .header p { color: #bfdbfe; margin: 4px 0 0; font-size: 13px; }
        .body { padding: 32px; }
        .body h2 { font-size: 18px; margin: 0 0 16px; color: #1e40af; }
        .info-box { background: #f0f4ff; border-left: 4px solid #1d4ed8; border-radius: 4px; padding: 16px 20px; margin: 20px 0; }
        .info-box table { width: 100%; border-collapse: collapse; }
        .info-box td { padding: 5px 0; font-size: 14px; }
        .info-box td:first-child { font-weight: 600; width: 140px; color: #374151; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .badge-blue { background: #dbeafe; color: #1d4ed8; }
        .badge-green { background: #d1fae5; color: #065f46; }
        .footer { background: #f9fafb; border-top: 1px solid #e5e7eb; padding: 16px 32px; font-size: 12px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>Centro Terapêutico</p>
    </div>
    <div class="body">
        @yield('content')
    </div>
    <div class="footer">
        Este é um e-mail automático. Não responda a esta mensagem.<br>
        Em caso de dúvidas, entre em contato diretamente com a clínica.
    </div>
</div>
</body>
</html>

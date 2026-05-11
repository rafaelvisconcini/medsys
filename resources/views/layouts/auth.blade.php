<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MedSys') — Centro Terapêutico</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-body">
    <div class="auth-container">

        {{-- Painel esquerdo com branding --}}
        <div class="auth-brand">
            <div class="auth-brand-inner">
                <div class="brand-icon-lg">
                    <i class="bi bi-heart-pulse-fill"></i>
                </div>
                <h1 class="auth-brand-name">MedSys</h1>
                <p class="auth-brand-sub">Centro Terapêutico Infantil</p>
                <p class="auth-brand-tagline">
                    Gestão integrada de pacientes, agenda e prontuários clínicos para clínicas multidisciplinares.
                </p>
            </div>
        </div>

        {{-- Painel direito com formulário --}}
        <div class="auth-form-wrapper">
            <div class="auth-form-inner">
                @yield('content')
            </div>
        </div>

    </div>
</body>
</html>

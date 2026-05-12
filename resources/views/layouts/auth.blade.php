<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Theraflow'){{ $__env->hasSection('title') ? ' — Theraflow' : '' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-brand">
            <div class="auth-brand-inner">
                <div class="brand-icon-lg"><i class="bi bi-heart-pulse-fill"></i></div>
                <h1 class="auth-brand-name">Theraflow</h1>
                <p class="auth-brand-sub">Centro Terapêutico Infantil</p>
                <p class="auth-brand-tagline">
                    Gestão integrada de pacientes, agenda e prontuários para clínicas multidisciplinares.
                </p>
            </div>
        </div>
        <div class="auth-form-wrapper">
            <div class="auth-form-inner">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MedSys') — Centro Terapêutico</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body>

<div class="app-wrapper" id="wrapper">

    {{-- Sidebar --}}
    <aside id="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <div class="brand-icon"><i class="bi bi-heart-pulse-fill"></i></div>
            <span class="brand-name">MedSys</span>
        </a>

        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}"
               class="nav-link @if(request()->routeIs('dashboard')) active @endif">
                <i class="bi bi-grid-1x2-fill nav-icon"></i><span>Dashboard</span>
            </a>

            @can('gerenciar-pacientes')
            <a href="{{ route('pacientes.index') }}"
               class="nav-link @if(request()->routeIs('pacientes.*')) active @endif">
                <i class="bi bi-people-fill nav-icon"></i><span>Pacientes</span>
            </a>
            @endcan

            @can('agendar')
            <a href="{{ route('agenda.index') }}"
               class="nav-link @if(request()->routeIs('agenda.*')) active @endif">
                <i class="bi bi-calendar3 nav-icon"></i><span>Agenda</span>
            </a>
            @endcan

            @can('ver-prontuario')
            <a href="{{ route('pacientes.index') }}"
               class="nav-link @if(request()->routeIs('prontuarios.*')) active @endif">
                <i class="bi bi-journal-medical nav-icon"></i><span>Prontuários</span>
            </a>
            @endcan

            @can('acessar-financeiro')
            <a href="{{ route('financeiro.index') }}"
               class="nav-link @if(request()->routeIs('financeiro.*')) active @endif">
                <i class="bi bi-cash-stack nav-icon"></i><span>Financeiro</span>
            </a>
            @endcan

            @can('admin')
            <div class="nav-section-label">Administração</div>
            <a href="{{ route('profissionais.index') }}"
               class="nav-link @if(request()->routeIs('profissionais.*')) active @endif">
                <i class="bi bi-person-badge nav-icon"></i><span>Profissionais</span>
            </a>
            <a href="{{ route('usuarios.index') }}"
               class="nav-link @if(request()->routeIs('usuarios.*')) active @endif">
                <i class="bi bi-person-gear nav-icon"></i><span>Usuários</span>
            </a>
            <a href="{{ route('admin.agenda-config.index') }}"
               class="nav-link @if(request()->routeIs('admin.agenda-config.*')) active @endif">
                <i class="bi bi-gear nav-icon"></i><span>Config. Agenda</span>
            </a>
            <a href="{{ route('lgpd.audit.index') }}"
               class="nav-link @if(request()->routeIs('lgpd.*')) active @endif">
                <i class="bi bi-shield-check nav-icon"></i><span>Auditoria (LGPD)</span>
            </a>
            @endcan
        </nav>

        <div class="sidebar-user">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ auth()->user()->perfil->label() }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout" title="Sair">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </aside>

    {{-- Conteúdo principal --}}
    <div class="main-wrapper">
        <header class="topbar">
            <h6 class="page-title mb-0">@yield('page-title', 'Dashboard')</h6>
            @can('gerenciar-pacientes')
            <div class="search-wrapper"><livewire:busca-paciente /></div>
            @endcan
            <div class="header-actions">@yield('header-actions')</div>
        </header>

        @if(session('success') || session('error'))
        <div class="px-4 pt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('success') }}</span>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <span>{{ session('error') }}</span>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
        @endif

        <main class="page-content">@yield('content')</main>
    </div>
</div>

@livewireScripts
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>

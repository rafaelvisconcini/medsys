<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MedSys') — Centro Terapêutico</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body>

<div class="d-flex" id="wrapper">

    {{-- Sidebar --}}
    <nav id="sidebar" class="bg-primary text-white d-flex flex-column p-3" style="min-width:240px;min-height:100vh;">
        <a href="{{ route('dashboard') }}" class="text-white text-decoration-none mb-4 d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314"/>
            </svg>
            <span class="fs-5 fw-bold">MedSys</span>
        </a>

        <ul class="nav flex-column gap-1 flex-grow-1">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}"
                   class="nav-link text-white @if(request()->routeIs('dashboard')) bg-white bg-opacity-25 rounded @endif">
                    Dashboard
                </a>
            </li>

            @can('gerenciar-pacientes')
            <li class="nav-item">
                <a href="{{ route('pacientes.index') }}"
                   class="nav-link text-white @if(request()->routeIs('pacientes.*')) bg-white bg-opacity-25 rounded @endif">
                    Pacientes
                </a>
            </li>
            @endcan

            @can('agendar')
            <li class="nav-item">
                <a href="{{ route('agenda.index') }}"
                   class="nav-link text-white @if(request()->routeIs('agenda.*')) bg-white bg-opacity-25 rounded @endif">
                    Agenda
                </a>
            </li>
            @endcan

            @can('ver-prontuario')
            <li class="nav-item">
                <a href="{{ route('pacientes.index') }}"
                   class="nav-link text-white @if(request()->routeIs('prontuarios.*')) bg-white bg-opacity-25 rounded @endif">
                    Prontuários
                </a>
            </li>
            @endcan

            @can('acessar-financeiro')
            <li class="nav-item">
                <a href="{{ route('financeiro.index') }}"
                   class="nav-link text-white @if(request()->routeIs('financeiro.*')) bg-white bg-opacity-25 rounded @endif">
                    Financeiro
                </a>
            </li>
            @endcan

            @can('admin')
            <li class="mt-3">
                <small class="text-white-50 text-uppercase fw-semibold px-2">Administração</small>
            </li>
            <li class="nav-item">
                <a href="{{ route('profissionais.index') }}"
                   class="nav-link text-white @if(request()->routeIs('profissionais.*')) bg-white bg-opacity-25 rounded @endif">
                    Profissionais
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('usuarios.index') }}"
                   class="nav-link text-white @if(request()->routeIs('usuarios.*')) bg-white bg-opacity-25 rounded @endif">
                    Usuários
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.agenda-config.index') }}"
                   class="nav-link text-white @if(request()->routeIs('admin.agenda-config.*')) bg-white bg-opacity-25 rounded @endif">
                    Config. Agenda
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('lgpd.audit.index') }}"
                   class="nav-link text-white @if(request()->routeIs('lgpd.*')) bg-white bg-opacity-25 rounded @endif">
                    Auditoria (LGPD)
                </a>
            </li>
            @endcan
        </ul>

        <div class="border-top border-white border-opacity-25 pt-3 mt-2">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center"
                     style="width:36px;height:36px;font-weight:bold;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <div class="text-white fw-semibold text-truncate small">{{ auth()->user()->name }}</div>
                    <div class="text-white-50" style="font-size:.75rem;">{{ auth()->user()->perfil->label() }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link text-white p-0" title="Sair">&#x2192;</button>
                </form>
            </div>
        </div>
    </nav>

    {{-- Conteúdo principal --}}
    <div class="flex-grow-1 d-flex flex-column bg-light">
        <header class="bg-white border-bottom px-4 py-2 d-flex align-items-center justify-content-between shadow-sm gap-3">
            <h6 class="mb-0 fw-semibold text-secondary text-nowrap">@yield('page-title', 'Dashboard')</h6>
            @can('gerenciar-pacientes')
            <livewire:busca-paciente />
            @endcan
            <div class="d-flex gap-2 align-items-center">
                @yield('header-actions')
            </div>
        </header>

        <div class="px-4 pt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        <main class="flex-grow-1 p-4">
            @yield('content')
        </main>
    </div>
</div>

@livewireScripts
@stack('scripts')
</body>
</html>

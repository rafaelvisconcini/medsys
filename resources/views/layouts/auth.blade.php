<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MedSys') — Centro Terapêutico</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height:100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="text-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#0d6efd" viewBox="0 0 16 16">
                        <path d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314"/>
                    </svg>
                    <h4 class="mt-2 fw-bold text-primary">MedSys</h4>
                    <p class="text-muted small">Centro Terapêutico Infantil</p>
                </div>
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>

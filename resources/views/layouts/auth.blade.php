<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'F.I.N.O.R Post-grado')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: var(--content-bg);
        }
        .app-wrapper {
            width: 100%;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .main-content {
            width: 100%;
            margin-left: 0;
        }
        .content-area {
            padding: 0;
            max-width: 100%;
        }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <!-- Main Content -->
        <div class="main-content">
            <!-- Content -->
            <main class="content-area">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <script src="{{ asset('js/theme.js') }}"></script>
    @stack('scripts')
</body>
</html>

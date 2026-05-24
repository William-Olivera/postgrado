<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Posgrado') - F.I.N.O.R</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            color: #e2e8f0;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255,255,255,0.06);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
        }
        .sidebar-header {
            padding: 28px 24px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .sidebar-header h2 {
            font-size: 22px;
            font-weight: 700;
            background: linear-gradient(135deg, #818cf8 0%, #c084fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .sidebar-header p { font-size: 12px; color: #64748b; margin-top: 4px; letter-spacing: 1px; }
        .nav-menu { padding: 16px 12px; }
        .nav-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            margin: 4px 8px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            border-radius: 12px;
            transition: all 0.3s;
            border: 1px solid transparent;
        }
        .nav-item:hover {
            background: rgba(255,255,255,0.03);
            color: #e2e8f0;
            border-color: rgba(255,255,255,0.06);
        }
        .nav-item.active {
            background: rgba(99, 102, 241, 0.1);
            color: #818cf8;
            border-color: rgba(99, 102, 241, 0.2);
        }
        .nav-item i { width: 22px; margin-right: 12px; font-size: 15px; }

        /* Main */
        .main-content {
            margin-left: 260px;
            flex: 1;
            padding: 32px;
            min-height: 100vh;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }
        .top-bar h1 { font-size: 26px; font-weight: 700; color: #f8fafc; }
        .top-bar p { color: #64748b; font-size: 14px; margin-top: 4px; }
        .user-pill {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.08);
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 13px;
            color: #94a3b8;
        }
        .user-pill form { display: inline; }
        .btn-logout {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.1);
            color: #94a3b8;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
        }
        .btn-logout:hover { border-color: #ef4444; color: #ef4444; }

        /* Content area */
        .content-area {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 20px;
            padding: 32px;
            min-height: calc(100vh - 200px);
        }

        .alert {
            padding: 14px 20px;
            border-radius: 14px;
            margin-bottom: 24px;
            font-size: 14px;
            backdrop-filter: blur(10px);
            border: 1px solid;
        }
        .alert-success {
            background: rgba(34, 197, 94, 0.08);
            color: #4ade80;
            border-color: rgba(34, 197, 94, 0.2);
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.08);
            color: #f87171;
            border-color: rgba(239, 68, 68, 0.2);
        }

        /* Buttons */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.25);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(59, 130, 246, 0.4);
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 24px;
            transition: color 0.3s;
        }
        .btn-back:hover { color: #94a3b8; }

        /* Table styles */
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left;
            padding: 14px 16px;
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid rgba(255,255,255,0.06);
        }
        td {
            padding: 16px;
            font-size: 14px;
            color: #cbd5e1;
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }
        tr:hover td { background: rgba(255,255,255,0.02); }

        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge-admin { background: rgba(99, 102, 241, 0.12); color: #818cf8; }
        .badge-operador { background: rgba(34, 197, 94, 0.12); color: #4ade80; }
        .badge-activo { background: rgba(34, 197, 94, 0.12); color: #4ade80; }
        .badge-inactivo { background: rgba(239, 68, 68, 0.12); color: #f87171; }
        .badge-pendiente { background: rgba(245, 158, 11, 0.12); color: #fbbf24; }

        .btn-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(15, 23, 42, 0.4);
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s;
            text-decoration: none;
        }
        .btn-icon:hover { background: rgba(99, 102, 241, 0.1); color: #818cf8; border-color: rgba(99, 102, 241, 0.3); }
        .btn-icon.delete:hover { background: rgba(239, 68, 68, 0.1); color: #f87171; border-color: rgba(239, 68, 68, 0.3); }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #475569;
        }
        .empty-state i { font-size: 48px; margin-bottom: 16px; display: block; }

        .pagination {
            padding: 20px 0;
            display: flex;
            justify-content: flex-end;
            gap: 4px;
        }
        .pagination > * {
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            color: #94a3b8;
            text-decoration: none;
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid rgba(255,255,255,0.06);
        }
        .pagination .active {
            background: #3b82f6;
            color: #fff;
        }

        /* Form styles */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        @media (max-width: 640px) {
            .form-row { grid-template-columns: 1fr; }
        }
        .form-group { margin-bottom: 24px; }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #94a3b8;
            margin-bottom: 8px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 14px 16px;
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            color: #f1f5f9;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: all 0.3s;
        }
        .form-group input::placeholder { color: #475569; }
        .form-group input:focus, .form-group select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            background: rgba(15, 23, 42, 0.7);
        }
        .form-group select option { background: #1e293b; color: #f1f5f9; }

        .toggle-group {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
        }
        .toggle-group label { margin: 0; padding: 0; color: #cbd5e1; }
        .toggle-group span { font-size: 12px; color: #64748b; }

        /* Switch Toggle - CORREGIDO */
        .switch {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 26px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .switch .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #334155;
            transition: .3s;
            border-radius: 26px;
        }
        .switch .slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background: #94a3b8;
            transition: .3s;
            border-radius: 50%;
        }
        .switch input:checked + .slider {
            background: #6366f1;
        }
        .switch input:checked + .slider:before {
            transform: translateX(22px);
            background: #fff;
        }

        .btn-submit {
            padding: 14px 32px;
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
            border: none;
            border-radius: 14px;
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.25);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(59, 130, 246, 0.4);
        }

        .error-box {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #f87171;
            padding: 12px 16px;
            border-radius: 14px;
            font-size: 13px;
            margin-bottom: 20px;
        }
        .field-error {
            color: #f87171;
            font-size: 12px;
            margin-top: 6px;
        }
            /* === ELIMINAR FLECHAS DE INPUTS NUMÉRICOS EN TODO EL SISTEMA === */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type=number] {
            -moz-appearance: textfield;
  }
    </style>
    @stack('styles')

</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>F.I.N.O.R</h2>
            <p>POSGRADO</p>
        </div>
        <nav class="nav-menu">
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a href="{{ route('estudiantes.index') }}" class="nav-item {{ request()->routeIs('estudiantes.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Estudiantes
            </a>
            <a href="{{ route('cursos.index') }}" class="nav-item {{ request()->routeIs('cursos.*') ? 'active' : '' }}">
                <i class="fas fa-book-open"></i> Cursos
            </a>
            <a href="{{ route('pagos.index') }}" class="nav-item {{ request()->routeIs('pagos.*') ? 'active' : '' }}">
                <i class="fas fa-cash-register"></i> Pagos
            </a>
            <a href="{{ route('deudas.index') }}" class="nav-item {{ request()->routeIs('deudas.*') ? 'active' : '' }}">
                <i class="fas fa-money-bill-wave"></i> Deudas y Saldos
            </a>
            <a href="{{ route('reportes.index') }}" class="nav-item {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i> Reportes y Planillas
            </a>
            <a href="{{ route('respaldos.index') }}" class="nav-item {{ request()->routeIs('respaldos.*') ? 'active' : '' }}">
                <i class="fas fa-database"></i> Respaldos
            </a>
            @if(auth()->user()->rol === 'administrador')
            <a href="{{ route('usuarios.index') }}" class="nav-item {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                <i class="fas fa-user-shield"></i> Usuarios
            </a>
            @endif
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar">
            <div>
                <h1>@yield('page-title', 'Dashboard')</h1>
                <p>@yield('page-subtitle', '')</p>
            </div>
            <div class="user-pill">
                <span>{{ auth()->user()->name }} ({{ auth()->user()->rol }})</span>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-logout">Salir</button>
                </form>
            </div>
        </div>

        <div class="content-area">
            @yield('content')
        </div>
    </main>

    @yield('modals')
    @stack('scripts')

</body>
</html>

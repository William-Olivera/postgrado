@extends('layouts.app')

@section('title', 'Respaldo de Datos')
@section('page-title', 'Respaldo de Datos')
@section('page-subtitle', 'Administración y resguardo seguro de la información del sistema F.I.N.O.R')

@section('content')

    <style>
        /* Tarjetas de estado superior */
        .backup-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }
        .stat-card-backup {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .stat-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        /* Tabla de registros */
        .tabla-respaldos {
            width: 100%;
            border-collapse: collapse;
            background: rgba(30, 41, 59, 0.2);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 14px;
            overflow: hidden;
        }
        .tabla-respaldos th {
            background: rgba(15, 23, 42, 0.4);
            padding: 16px;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 600;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }
        .tabla-respaldos td {
            padding: 16px;
            color: #e2e8f0;
            font-size: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }
        .tabla-respaldos tr:last-child td {
            border-bottom: none;
        }

        /* Botones de acción específicos */
        .btn-backup-now {
            padding: 12px 24px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .btn-backup-now:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }
        .btn-table-action {
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-download { background: rgba(56, 189, 248, 0.1); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.2); }
        .btn-download:hover { background: rgba(56, 189, 248, 0.2); }
        .btn-delete { background: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2); }
        .btn-delete:hover { background: rgba(239, 68, 68, 0.2); }
    </style>

    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #34d399; padding: 16px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="backup-stats-grid">
        <div class="stat-card-backup">
            <div class="stat-icon-box" style="background: rgba(56, 189, 248, 0.1); color: #38bdf8;">
                <i class="fas fa-database"></i>
            </div>
            <div>
                <span style="color: #64748b; font-size: 13px; display: block;">Base de Datos</span>
                <strong style="color: #f8fafc; font-size: 18px;">MySQL Activa</strong>
            </div>
        </div>

        <div class="stat-card-backup">
            <div class="stat-icon-box" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                <i class="fas fa-file-pdf"></i>
            </div>
            <div>
                <span style="color: #64748b; font-size: 13px; display: block;">Documentos Alumnos</span>
                <strong style="color: #f8fafc; font-size: 18px;">Carpeta Storage</strong>
            </div>
        </div>

        <div class="stat-card-backup">
            <div class="stat-icon-box" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                <i class="fas fa-history"></i>
            </div>
            <div>
                <span style="color: #64748b; font-size: 13px; display: block;">Último Respaldo</span>
                <strong style="color: #f8fafc; font-size: 14px;">{{ count($backups) > 0 ? $backups[0]['fecha'] : 'Ninguno registrado' }}</strong>
            </div>
        </div>
    </div>

    <div style="background: rgba(30, 41, 59, 0.2); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 16px; padding: 24px;">

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
            <div>
                <h3 style="color: #f8fafc; font-size: 18px; font-weight: 600; margin-bottom: 4px;">Historial de Copias de Seguridad</h3>
                <p style="color: #64748b; font-size: 13px;">Archivos comprimidos seguros listos para descargas preventivas.</p>
            </div>

            <form action="{{ route('respaldos.crear') }}" method="POST" id="backupForm">
                @csrf
                <button type="submit" class="btn-backup-now" onclick="mostrarCarga()">
                    <i class="fas fa-download-pattern" id="btnIcon"></i>
                    <span id="btnText">Ejecutar Copia Manual Ahora</span>
                </button>
            </form>
        </div>

        <div id="loaderBackup" style="display: none; background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(56, 189, 248, 0.2); border-radius: 12px; padding: 16px; margin-bottom: 24px; text-align: center; color: #38bdf8;">
            <i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i> Procesando Base de Datos y empaquetando archivos adjuntos... Por favor espere.
        </div>

        <div style="overflow-x: auto;">
            <table class="tabla-respaldos">
                <thead>
                <tr>
                    <th>Nombre de Archivo Corriente</th>
                    <th>Ubicación / Disco</th>
                    <th>Tamaño del ZIP</th>
                    <th>Fecha de Creación</th>
                    <th style="text-align: right;">Descarga / Gestión</th>
                </tr>
                </thead>
                <tbody>
                @forelse($backups as $backup)
                    <tr>
                        <td style="font-family: monospace; color: #cbd5e1; font-size: 13px;">
                            <i class="far fa-file-archive" style="color: #f59e0b; margin-right: 8px; font-size: 15px;"></i>
                            {{ $backup['archivo'] }}
                        </td>
                        <td>
                            <span style="padding: 4px 8px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); border-radius: 6px; font-size: 12px; color: #94a3b8;">
                                Local Server
                            </span>
                        </td>
                        <td style="color: #cbd5e1; font-weight: 500;">{{ $backup['tamano'] }}</td>
                        <td style="color: #94a3b8;">{{ $backup['fecha'] }}</td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 8px;">
                                <a href="{{ route('respaldos.descargar', ['archivo' => $backup['archivo']]) }}" class="btn-table-action btn-download">
                                    <i class="fas fa-cloud-download-alt"></i> Descargar
                                </a>

                                <form action="{{ route('respaldos.eliminar') }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="archivo" value="{{ $backup['archivo'] }}">
                                    <button type="submit" class="btn-table-action btn-delete" onclick="return confirm('¿Seguro que desea eliminar esta copia física del sistema?')">
                                        <i class="fas fa-trash-alt"></i> Borrar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 48px; color: #64748b;">
                            <i class="fas fa-shield-alt" style="font-size: 32px; display: block; margin-bottom: 12px; color: #334155;"></i>
                            No se registran copias manuales todavía en este servidor web.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function mostrarCarga() {
            document.getElementById('loaderBackup').style.display = 'block';
            document.getElementById('btnIcon').className = 'fas fa-circle-notch fa-spin';
            document.getElementById('btnText').textContent = 'Generando Respaldo...';
            document.getElementById('backupForm').style.pointerEvents = 'none';
        }
    </script>

@endsection

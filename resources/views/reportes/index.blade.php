@extends('layouts.app')

@section('page-title', 'Reportes y Planillas')
@section('page-subtitle', 'Control institucional de inscritos, pagos y estados financieros')

@section('content')
    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <style>
        /* Contenedor principal de Reportes */
        .reporte-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 25px;
            margin-top: 10px;
        }

        /* Tarjetas de Reportes con Estilo Oscuro */
        .reporte-card {
            background: rgba(30, 41, 59, 0.45);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .reporte-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
        }

        /* Encabezados internos de la tarjeta */
        .reporte-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            padding-bottom: 15px;
        }

        .reporte-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        /* Colores temáticos institucionales para diferenciar las acciones */
        .icon-pdf {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.25);
        }

        .icon-excel {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }

        .reporte-header h3 {
            font-size: 1.15rem;
            font-weight: 600;
            color: #f8fafc;
            margin: 0;
        }

        .reporte-header p {
            font-size: 0.85rem;
            color: #94a3b8;
            margin: 3px 0 0 0;
        }

        /* Estilos de los Formularios Internos */
        .form-group-custom {
            margin-bottom: 18px;
        }

        .form-group-custom label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            color: #cbd5e1;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control-custom {
            width: 100%;
            padding: 11px 16px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            color: #f8fafc;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            outline: none;
        }

        .form-control-custom:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
            background: rgba(15, 23, 42, 0.8);
        }

        .form-control-custom option {
            background: #1e293b;
            color: #f8fafc;
        }

        /* Fila de fechas alineadas horizontalmente */
        .form-row-dates {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        /* Botones de acción */
        .btn-reporte {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            margin-top: 15px;
        }

        .btn-pdf {
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        .btn-pdf:hover {
            background: linear-gradient(135deg, #f87171 0%, #dc2626 100%);
            transform: translateY(-1px);
        }

        .btn-excel {
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .btn-excel:hover {
            background: linear-gradient(135deg, #34d399 0%, #059669 100%);
            transform: translateY(-1px);
        }
    </style>

    <div class="reporte-container">

        <div class="reporte-card">
            <div>
                <div class="reporte-header">
                    <div class="reporte-icon icon-pdf">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div>
                        <h3>Filtros de Reportes PDF</h3>
                        <p>Generación de extractos y estados de cuenta rápidos</p>
                    </div>
                </div>

                <form action="{{ route('reportes.generar') }}" method="POST">
                    @csrf
                    <div class="form-group-custom">
                        <label>Tipo de Reporte</label>
                        <select name="tipo_reporte" class="form-control-custom" required>
                            <option value="pagos">Reporte General de Pagos</option>
                            <option value="deudas">Reporte de Deudas y Saldos</option>
                            <option value="inscripciones">Reporte de Inscripciones</option>
                        </select>
                    </div>

                    <div class="form-group-custom">
                        <label>Curso / Programa (Opcional)</label>
                        <select name="curso_id" class="form-control-custom">
                            <option value="">-- Todos los Cursos --</option>
                            @foreach($cursos as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }} (V. {{ $c->version }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-row-dates">
                        <div class="form-group-custom">
                            <label>Fecha Desde</label>
                            <input type="date" name="fecha_desde" class="form-control-custom">
                        </div>
                        <div class="form-group-custom">
                            <label>Fecha Hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control-custom">
                        </div>
                    </div>

                    <button type="submit" class="btn-reporte btn-pdf">
                        <i class="fas fa-file-download"></i> Generar Reporte PDF
                    </button>
                </form>
            </div>
        </div>

        <div class="reporte-card">
            <div>
                <div class="reporte-header">
                    <div class="reporte-icon icon-excel">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    <div>
                        <h3>Planillas Excel de Postgrado</h3>
                        <p>Estructuras dinámicas con fórmulas nativas para auditorías</p>
                    </div>
                </div>

                <form id="formExcel">
                    @csrf
                    <div class="form-group-custom">
                        <label>Seleccionar Programa Académico</label>
                        <select name="curso_id" id="excel_curso_id" class="form-control-custom" required>
                            <option value="">-- Elija un programa de la lista --</option>
                            @foreach($cursos as $c)
                                <option value="{{ $c->id }}">{{ $c->tipo }}: {{ $c->nombre }} - Versión {{ $c->version }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin: 20px 0; padding: 15px; background: rgba(255,255,255,0.03); border-radius: 10px; border-left: 4px solid #34d399;">
                        <p style="margin: 0; font-size: 0.85rem; color: #a1a1aa; line-height: 1.5;">
                            <i class="fas fa-info-circle" style="color: #34d399; margin-right: 5px;"></i>
                            La descarga generará automáticamente las columnas dinámicas de <strong>Fecha y Monto</strong> calculando el número de módulos configurados en el programa.
                        </p>
                    </div>

                    <button type="button" id="btnDescargarExcel" class="btn-reporte btn-excel">
                        <i class="fas fa-file-excel"></i> Descargar Planilla Oficial (.xlsx)
                    </button>
                </form>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnExcel = document.getElementById('btnDescargarExcel');
            const selectCurso = document.getElementById('excel_curso_id');

            if(btnExcel) {
                btnExcel.addEventListener('click', function() {
                    const cursoId = selectCurso.value;
                    if(!cursoId) {
                        alert('Por favor, seleccione un programa académico para exportar.');
                        return;
                    }

                    // Cambiar estado visual del botón durante el procesamiento
                    btnExcel.disabled = true;
                    btnExcel.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando Planilla...';

                    // Construcción limpia de la ruta asignada en web.php
                    const url = `{{ url('reportes/exportar-planilla') }}/${cursoId}`;

                    // Forzar descarga directa en pestaña controlada
                    window.location.href = url;

                    // Restaurar botón después de iniciar el flujo
                    setTimeout(() => {
                        btnExcel.disabled = false;
                        btnExcel.innerHTML = '<i class="fas fa-file-excel"></i> Descargar Planilla Oficial (.xlsx)';
                    }, 2500);
                });
            }
        });
    </script>
@endsection

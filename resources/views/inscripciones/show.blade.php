@extends('layouts.app')

@section('title', "Inscripciones - {$curso->nombre}")
@section('page-title', $curso->nombre)
@section('page-subtitle', "{$curso->tipo->value} · v{$curso->version}.{$curso->edicion} · {$curso->periodo}")

@section('content')

<style>
    /* Estilos generales de la cabecera */
    .header-show {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .btn-volver {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: rgba(15,23,42,0.5);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 12px;
        color: #94a3b8;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
    }
    .btn-volver:hover {
        background: rgba(255,255,255,0.05);
        color: #f1f5f9;
    }
    .contador-inscritos {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #64748b;
    }

    /* Buscador */
    .buscador-estudiantes {
        position: relative;
        flex: 1;
        max-width: 300px;
    }
    .buscador-estudiantes i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 14px;
    }
    .buscador-estudiantes input {
        width: 100%;
        padding: 12px 16px 12px 40px;
        background: rgba(15,23,42,0.5);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 14px;
        color: #f1f5f9;
        font-size: 14px;
        outline: none;
    }

    /* Botón Nueva Inscripción */
    .btn-nueva-inscripcion {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 24px;
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    .btn-nueva-inscripcion:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(16,185,129,0.3);
    }

    /* Tabla */
    .tabla-container {
        background: rgba(30,41,59,0.4);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 16px;
        overflow: hidden;
    }
    .tabla-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 24px;
        background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%);
    }
    .tabla-header-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 16px;
        font-weight: 700;
        color: #f8fafc;
    }
    .tabla-header-badge {
        padding: 4px 12px;
        background: rgba(255,255,255,0.1);
        border-radius: 20px;
        font-size: 12px;
        color: rgba(255,255,255,0.8);
    }
    .tabla-inscripciones {
        width: 100%;
        border-collapse: collapse;
    }
    .tabla-inscripciones th {
        text-align: left;
        padding: 14px 16px;
        font-size: 11px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        background: rgba(15,23,42,0.3);
    }
    .tabla-inscripciones td {
        padding: 16px;
        font-size: 14px;
        color: #94a3b8;
        border-bottom: 1px solid rgba(255,255,255,0.04);
    }
    .tabla-inscripciones tr:hover td {
        background: rgba(15,23,42,0.2);
    }

    /* Badges y Estados */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-modalidad { background: rgba(34,197,94,0.15); color: #4ade80; }
    .badge-estado-pendiente { background: rgba(245,158,11,0.15); color: #fbbf24; }
    .badge-estado-pagado { background: rgba(34,197,94,0.15); color: #4ade80; }
    .badge-estado-sin-pagar { background: rgba(239,68,68,0.15); color: #f87171; }

    .saldo-cero { color: #4ade80; font-weight: 700; }
    .saldo-pendiente { color: #fbbf24; font-weight: 700; }

    /* Acciones */
    .acciones { display: flex; gap: 8px; }
    .btn-accion {
        width: 32px; height: 32px;
        border-radius: 8px;
        border: 1px solid rgba(255,255,255,0.08);
        background: rgba(15,23,42,0.4);
        color: #94a3b8;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        font-size: 13px;
        position: relative;
    }
    .btn-accion.pagar:hover { background: rgba(34,197,94,0.1); color: #4ade80; }
    .btn-accion.config:hover { background: rgba(99,102,241,0.1); color: #818cf8; }
    .btn-accion.eliminar:hover { background: rgba(239,68,68,0.1); color: #f87171; }

    /* Dropdown */
    .dropdown-salida {
        position: absolute;
        top: 100%; right: 0;
        margin-top: 8px;
        background: rgba(30,41,59,0.98);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 12px;
        padding: 8px;
        min-width: 200px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        z-index: 100;
        display: none;
    }
    .dropdown-salida.active { display: block; }
    .dropdown-salida.drop-up {
        top: auto;
        bottom: 100%;
        margin-top: 0;
        margin-bottom: 8px;
    }
    .dropdown-item {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 12px;
        border-radius: 8px;
        font-size: 13px;
        color: #f1f5f9;
        cursor: pointer;
    }
    .dropdown-item:hover { background: rgba(99,102,241,0.1); }

    /* MODALES - REDISEÑO UI */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.8);
        backdrop-filter: blur(12px);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .modal-overlay.active {
        display: flex !important;
    }
    .modal-content {
        background: rgba(30,41,59,0.98);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 28px;
        width: 100%;
        max-width: 850px; /* Notablemnte más ancho */
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.8);
        position: relative;
    }

    /* Soporte para Múltiples Cursos */
    .curso-block {
        margin-bottom: 24px;
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 20px;
        overflow: hidden;
        background: rgba(15,23,42,0.3);
    }
    .curso-block-header {
        padding: 16px 24px;
        background: linear-gradient(135deg, #312e81 0%, #1e1b4b 100%);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Acordeón e Interactividad */
    .pago-item {
        border-bottom: 1px solid rgba(255,255,255,0.04);
        transition: all 0.3s ease;
    }
    .pago-item-trigger {
        padding: 16px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
    }
    .pago-item-trigger:hover {
        background: rgba(255,255,255,0.03);
    }
    .pago-detalles {
        display: none;
        padding: 20px 24px;
        background: rgba(99,102,241,0.05);
        border-top: 1px solid rgba(255,255,255,0.05);
        animation: slideDown 0.3s ease-out;
    }
    .pago-detalles.active {
        display: block;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }
    .info-box {
        padding: 12px;
        background: rgba(15,23,42,0.4);
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.05);
    }
    .info-label { font-size: 11px; color: #64748b; text-transform: uppercase; margin-bottom: 4px; }
    .info-value { font-size: 14px; color: #f1f5f9; font-weight: 500; }
</style>

<div class="header-show">
    <div style="display:flex; align-items:center; gap:16px;">
        <a href="{{ route('cursos.index') }}" class="btn-volver">
            <i class="fas fa-arrow-left"></i> Volver a Cursos
        </a>
        <div class="contador-inscritos">
            <i class="fas fa-users" style="color:#6366f1;"></i>
            <!-- Aquí se muestra el conteo real -->
            <span>{{ $inscripciones->count() }} inscritos</span>
        </div>
    </div>
    <div style="display:flex; align-items:center; gap:16px;">
        <div class="buscador-estudiantes">
            <i class="fas fa-search"></i>
            <input type="text" id="buscarEstudiante" placeholder="Buscar estudiante...">
        </div>
        @php
            $disponibles = max(0, $curso->cupo - $inscripciones->count());
        @endphp
        @if($disponibles > 0)
            <button onclick="openModal('nuevaInscripcionModal')" class="btn-nueva-inscripcion">
                <i class="fas fa-plus"></i> Nueva Inscripción
            </button>
        @endif
    </div>
</div>

@if(session('success'))
    <div style="padding:14px 20px; background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.2); border-radius:12px; color:#4ade80; margin-bottom:24px; display:flex; align-items:center; gap:10px;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="padding:14px 20px; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2); border-radius:12px; color:#f87171; margin-bottom:24px; display:flex; align-items:center; gap:10px;">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

<div class="tabla-container">
    <div class="tabla-header">
        <div class="tabla-header-title">
            <i class="fas fa-users" style="color:#fbbf24;"></i>
            Lista de Estudiantes Inscritos
        </div>
        <div class="tabla-header-badge">{{ $inscripciones->count() }} registros</div>
    </div>

    <table class="tabla-inscripciones">
        <thead>
            <tr>
                <th>Estudiante</th>
                <th>Modalidad</th>
                <th>Estado Acad.</th>
                <th>Estado Pago</th>
                <th>Saldo (Bs)</th>
                <th style="text-align:center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inscripciones as $inscripcion)
                @php
                    $saldoPendiente = $inscripcion->planPago->saldo_pendiente ?? 0;
                    $estaPagado = $saldoPendiente <= 0;
                    $esUltimo = $loop->last || ($loop->count > 2 && $loop->iteration > $loop->count - 2);
                @endphp
                <tr data-nombre="{{ strtolower($inscripcion->estudiante->nombre_completo) }}">
                    <td>
                        <div class="estudiante-info">
                            <div class="estudiante-nombre">{{ $inscripcion->estudiante->nombre_completo }}</div>
                            <div class="estudiante-datos">
                                CI: {{ $inscripcion->estudiante->cedula }} · Reg: {{ $inscripcion->estudiante->registro }}
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-modalidad">
                            {{ $inscripcion->tipo_inscripcion->value }} Completa
                        </span>
                    </td>
                    <td>
                        <span class="badge" style="background: {{ $inscripcion->estado_academico->color() }}26; color: {{ $inscripcion->estado_academico->color() }};">
                            <i class="fas {{ $inscripcion->estado_academico->value === 'Pendiente' ? 'fa-clock' : 'fa-check-circle' }}" style="font-size:10px;"></i>
                            {{ $inscripcion->estado_academico->label() }}
                        </span>
                    </td>
                    <td>
                        <span class="badge" style="background: {{ $inscripcion->estado_financiero->color() }}26; color: {{ $inscripcion->estado_financiero->color() }};">
                            <i class="fas {{ $inscripcion->estado_financiero->value === 'Completado' ? 'fa-check-circle' : ($inscripcion->estado_financiero->value === 'Sin Pagar' ? 'fa-times-circle' : 'fa-info-circle') }}" style="font-size:10px;"></i>
                            {{ $inscripcion->estado_financiero->label() }}
                        </span>
                    </td>
                    <td class="{{ $estaPagado ? 'saldo-cero' : 'saldo-pendiente' }}">
                        {{ number_format($saldoPendiente, 2) }}
                    </td>
                    <td>
                        <div class="acciones" style="position:relative;">
                            <button class="btn-accion pagar" title="Registrar pago" onclick="window.location.href='{{ route('pagos.index', ['inscripcion_id' => $inscripcion->id]) }}'">
                                <i class="fas fa-money-bill-wave"></i>
                            </button>

                            <div style="position:relative;">
                                <button class="btn-accion config" onclick="toggleDropdown(this)" title="Configurar">
                                    <i class="fas fa-cog"></i>
                                </button>
                                <div class="dropdown-salida {{ $esUltimo ? 'drop-up' : '' }}">
                                    <div class="dropdown-salida-header">Salida Anticipada</div>
                                    <div class="dropdown-item" onclick="cambiarTipoInscripcion({{ $inscripcion->id }}, 'Diplomado')">
                                        <i class="fas fa-graduation-cap" style="color:#3b82f6;"></i> Solo Diplomado
                                    </div>
                                    <div class="dropdown-item" onclick="cambiarTipoInscripcion({{ $inscripcion->id }}, 'Especialidad')">
                                        <i class="fas fa-user-graduate" style="color:#8b5cf6;"></i> Hasta Especialidad
                                    </div>
                                </div>
                            </div>

                            <button class="btn-accion eliminar" onclick="confirmarEliminar({{ $inscripcion->id }}, '{{ addslashes($inscripcion->estudiante->nombre_completo) }}')" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:60px 20px;">
                        <i class="fas fa-users" style="font-size:48px; color:#334155; display:block; margin-bottom:16px;"></i>
                        <p style="font-size:16px; color:#64748b;">No hay estudiantes inscritos aún</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

@section('modals')

<!-- Modal Nueva Inscripción -->
<div id="nuevaInscripcionModal" class="modal-overlay" onclick="if(event.target === this) closeModal('nuevaInscripcionModal')">
    <div style="background:rgba(30,41,59,0.98); border:1px solid rgba(255,255,255,0.1); border-radius:24px; width:100%; max-width:520px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.8); position:relative;">
        <div style="padding:28px 32px 0; display:flex; justify-content:space-between; align-items:center;">
            <h2 style="font-size:20px; font-weight:700; color:#f8fafc; display:flex; align-items:center; gap:12px;">
                <i class="fas fa-user-plus" style="color:#10b981; font-size:20px;"></i> Nueva Inscripción
            </h2>
            <button onclick="closeModal('nuevaInscripcionModal')" style="width:36px; height:36px; border-radius:10px; border:1px solid rgba(255,255,255,0.1); background:rgba(15,23,42,0.5); color:#94a3b8; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div style="padding:24px 32px 32px;">
            <form method="POST" action="{{ route('inscripciones.store') }}" id="formNuevaInscripcion">
                @csrf
                <input type="hidden" name="curso_id" value="{{ $curso->id }}">
                <input type="hidden" name="estudiante_id" id="estudiante_id_seleccionado" value="">

                <div style="margin-bottom:20px; padding:12px 16px; background:rgba(99,102,241,0.1); border:1px solid rgba(99,102,241,0.2); border-radius:10px; font-size:14px; color:#818cf8;">
                    <i class="fas fa-book" style="margin-right:8px;"></i> {{ $curso->nombre }}
                </div>

                <!-- Estudiante -->
                <div style="margin-bottom:20px;">
                    <label style="font-size:14px; font-weight:600; color:#f1f5f9; display:block; margin-bottom:10px;">
                        Estudiante <span style="color:#ef4444;">*</span>
                    </label>
                    <div style="position:relative;">
                        <i class="fas fa-search" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#64748b;"></i>
                        <input type="text" id="buscadorEstudiante" placeholder="Nombre, CI o N° registro..."
                            style="width:100%; padding:12px 16px 12px 40px; background:rgba(15,23,42,0.5); border:1px solid rgba(255,255,255,0.08); border-radius:12px; color:#f1f5f9; font-size:14px; outline:none;">
                    </div>
                    <div id="resultadosBusqueda" style="display:none; margin-top:8px; max-height:200px; overflow-y:auto; background:rgba(15,23,42,0.8); border:1px solid rgba(255,255,255,0.08); border-radius:12px;"></div>

                    <!-- Estudiante seleccionado -->
                    <div id="estudianteSeleccionado" style="display:none; margin-top:12px; padding:14px; background:rgba(99,102,241,0.1); border:1px solid rgba(99,102,241,0.2); border-radius:12px;">
                        <div style="font-size:14px; font-weight:600; color:#f1f5f9;" id="nombreSeleccionado"></div>
                        <div style="font-size:12px; color:#818cf8; margin-top:4px;" id="datosSeleccionado"></div>
                        <div id="descuentoContainer" style="margin-top:8px;"></div>
                    </div>
                </div>

                <!-- Fecha -->
                <div style="margin-bottom:20px;">
                    <label style="font-size:14px; font-weight:600; color:#f1f5f9; display:block; margin-bottom:10px;">
                        Fecha de inscripción <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="date" name="fecha_inscripcion" value="{{ date('Y-m-d') }}"
                        style="width:100%; padding:12px 16px; background:rgba(15,23,42,0.5); border:1px solid rgba(255,255,255,0.08); border-radius:12px; color:#f1f5f9; font-size:14px; outline:none;">
                </div>

                <div style="display:flex; gap:12px; justify-content:flex-end;">
                    <button type="button" onclick="closeModal('nuevaInscripcionModal')" style="padding:12px 24px; border-radius:12px; border:1px solid rgba(255,255,255,0.1); background:transparent; color:#94a3b8; font-size:14px; cursor:pointer;">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-submit" id="btnGuardarInscripcion" disabled style="padding:12px 24px; border-radius:12px; border:none; background:linear-gradient(135deg, #059669 0%, #10b981 100%); color:#fff; font-size:14px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:8px;">
                        <i class="fas fa-save"></i> Registrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Eliminar -->
<div id="eliminarModal" class="modal-overlay" onclick="if(event.target === this) closeModal('eliminarModal')">
    <div style="background:rgba(30,41,59,0.98); border:1px solid rgba(255,255,255,0.1); border-radius:24px; width:100%; max-width:400px; padding:32px; text-align:center; box-shadow:0 25px 50px -12px rgba(0,0,0,0.8);">
        <div style="width:64px; height:64px; border-radius:50%; background:rgba(239,68,68,0.15); display:flex; align-items:center; justify-content:center; margin:0 auto 20px; font-size:28px; color:#ef4444;">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3 style="font-size:20px; font-weight:700; color:#f8fafc; margin-bottom:8px;">¿Eliminar inscripción?</h3>
        <p style="font-size:14px; color:#94a3b8; margin-bottom:8px;" id="nombreEliminar"></p>
        <p style="font-size:13px; color:#64748b; margin-bottom:24px;">Esta acción es irreversible.</p>

        <form id="formEliminar" method="POST" action="">
            @csrf
            @method('DELETE')
            <div style="display:flex; gap:12px; justify-content:center;">
                <button type="button" onclick="closeModal('eliminarModal')" style="padding:12px 24px; border-radius:12px; border:1px solid rgba(255,255,255,0.1); background:transparent; color:#94a3b8; font-size:14px; cursor:pointer;">
                    Cancelar
                </button>
                <button type="submit" style="padding:12px 24px; border-radius:12px; border:none; background:#ef4444; color:#fff; font-size:14px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:8px;">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let estudianteSeleccionado = null;
    let timeoutBusqueda = null;

    // Buscador de estudiantes (AJAX)
    document.getElementById('buscadorEstudiante').addEventListener('input', function() {
        const query = this.value.trim();
        const resultados = document.getElementById('resultadosBusqueda');
        clearTimeout(timeoutBusqueda);

        if (query.length < 2) {
            resultados.style.display = 'none';
            return;
        }

        timeoutBusqueda = setTimeout(() => {
            fetch(`/api/buscar-estudiante?q=${encodeURIComponent(query)}`)
                .then(r => r.json())
                .then(data => mostrarResultados(data));
        }, 300);
    });

    function mostrarResultados(estudiantes) {
        const container = document.getElementById('resultadosBusqueda');
        if (estudiantes.length === 0) {
            container.innerHTML = '<div style="padding:16px; color:#64748b; text-align:center;">No se encontraron estudiantes</div>';
            container.style.display = 'block';
            return;
        }

        container.innerHTML = estudiantes.map(e => `
            <div style="padding:12px 16px; cursor:pointer; border-bottom:1px solid rgba(255,255,255,0.04); transition:all 0.2s;"
                 onmouseover="this.style.background='rgba(99,102,241,0.1)'"
                 onmouseout="this.style.background='transparent'"
                 onclick="seleccionarEstudiante(${e.id}, '${e.nombres}', '${e.paterno}', '${e.materno || ''}', '${e.cedula}', '${e.registro}', ${e.descuento_porcentaje})">
                <div style="font-weight:600; color:#f1f5f9; font-size:14px;">${e.nombres} ${e.paterno} ${e.materno || ''}</div>
                <div style="font-size:12px; color:#64748b; margin-top:2px;">CI: ${e.cedula} · Registro: ${e.registro}</div>
            </div>
        `).join('');

        container.style.display = 'block';
    }

    function seleccionarEstudiante(id, nombres, paterno, materno, cedula, registro, descuento) {
        estudianteSeleccionado = { id, nombres, paterno, materno, cedula, registro, descuento };

        document.getElementById('estudiante_id_seleccionado').value = id;
        document.getElementById('nombreSeleccionado').textContent = `${nombres} ${paterno} ${materno}`;
        document.getElementById('datosSeleccionado').textContent = `CI: ${cedula} · Registro: ${registro}`;

        const descuentoHtml = descuento > 0
            ? `<span style="display:inline-flex; align-items:center; gap:6px; padding:4px 12px; background:rgba(34,197,94,0.15); color:#4ade80; border-radius:20px; font-size:12px; font-weight:600;"><i class="fas fa-tag"></i> ${descuento}% descuento en costo de estudio</span>`
            : `<span style="display:inline-flex; align-items:center; gap:6px; padding:4px 12px; background:rgba(239,68,68,0.15); color:#f87171; border-radius:20px; font-size:12px; font-weight:600;"><i class="fas fa-tag"></i> Sin descuento</span>`;

        document.getElementById('descuentoContainer').innerHTML = descuentoHtml;
        document.getElementById('estudianteSeleccionado').style.display = 'block';
        document.getElementById('resultadosBusqueda').style.display = 'none';
        document.getElementById('buscadorEstudiante').value = '';
        document.getElementById('btnGuardarInscripcion').disabled = false;
    }

    // Dropdown salida anticipada
    function toggleDropdown(btn) {
        const dropdown = btn.nextElementSibling;
        document.querySelectorAll('.dropdown-salida.active').forEach(d => {
            if (d !== dropdown) d.classList.remove('active');
        });
        dropdown.classList.toggle('active');
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.btn-accion.config')) {
            document.querySelectorAll('.dropdown-salida.active').forEach(d => d.classList.remove('active'));
        }
    });

    function cambiarTipoInscripcion(id, tipo) {
        if (!confirm(`¿Cambiar a "${tipo}"? Se ajustarán las cuotas.`)) return;
        fetch(`/inscripciones/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ tipo_inscripcion: tipo })
        }).then(() => location.reload());
    }

    // Eliminar
    function confirmarEliminar(id, nombre) {
        document.getElementById('nombreEliminar').textContent = nombre;
        document.getElementById('formEliminar').action = `/inscripciones/${id}`;
        openModal('eliminarModal');
    }

    // Buscar en tabla local
    document.getElementById('buscarEstudiante')?.addEventListener('input', function() {
        const termino = this.value.toLowerCase().trim();
        document.querySelectorAll('.tabla-inscripciones tbody tr[data-nombre]').forEach(row => {
            row.style.display = row.dataset.nombre.includes(termino) ? '' : 'none';
        });
    });

    // ========================================
    // FUNCIONES PARA CONTROLAR MODALES (CORREGIDO)
    // ========================================
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    // Cerrar modales con tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal('nuevaInscripcionModal');
            closeModal('eliminarModal');
        }
    });
    // ========================================
</script>

@endsection

@extends('layouts.app')

@section('title', 'Gestión de Estudiantes')
@section('page-title', 'Gestión de Estudiantes')
@section('page-subtitle', 'Registro y administración de estudiantes de postgrado')

@section('content')

<div style="display:flex; align-items:center; gap:16px; margin-bottom:24px; flex-wrap:wrap;">
    <button onclick="openModal('createModal')" class="btn-primary">
        <i class="fas fa-plus"></i> Registrar Estudiante
    </button>

    <div style="position:relative; flex:1; max-width:400px;">
        <i class="fas fa-search" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#64748b; font-size:14px;"></i>
        <input type="text" id="buscarEstudiante" placeholder="Buscar por nombre, CI o registro..."
            style="width:100%; padding:12px 16px 12px 40px; background:rgba(15,23,42,0.5); border:1px solid rgba(255,255,255,0.08); border-radius:14px; color:#f1f5f9; font-size:14px; font-family:'Inter',sans-serif; outline:none;">
        <div id="resultadosBusqueda" style="display:none; position:absolute; top:calc(100% + 8px); left:0; right:0; background:rgba(30,41,59,0.98); border:1px solid rgba(255,255,255,0.1); border-radius:14px; max-height:300px; overflow-y:auto; z-index:100; box-shadow:0 10px 40px rgba(0,0,0,0.5);"></div>
    </div>

    @if(session('success'))
        <div id="successAlert" class="alert alert-success" style="display:flex; align-items:center; gap:10px; margin:0; animation:slideIn 0.3s ease;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div id="errorAlert" class="alert alert-error" style="display:flex; align-items:center; gap:10px; margin:0; animation:slideIn 0.3s ease;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif
</div>

<style>
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(-10px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance: textfield;
    }
    .btn-ver, .btn-edit, .btn-baja, .btn-alta {
        padding: 8px 14px; border-radius: 8px;
        border: 1px solid rgba(255,255,255,0.08);
        background: rgba(15,23,42,0.4); color: #94a3b8;
        font-size: 12px; cursor: pointer; transition: all 0.3s;
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        font-family: 'Inter', sans-serif;
    }
    .btn-ver:hover { background: rgba(34,197,94,0.1); color: #4ade80; border-color: rgba(34,197,94,0.3); }
    .btn-edit:hover { background: rgba(99,102,241,0.1); color: #818cf8; border-color: rgba(99,102,241,0.3); }
    .btn-baja:hover { background: rgba(245,158,11,0.1); color: #fbbf24; border-color: rgba(245,158,11,0.3); }
    .btn-alta:hover { background: rgba(34,197,94,0.15); color: #4ade80; border-color: rgba(34,197,94,0.3); }
    .fila-inactiva td { color: #64748b !important; }
    .fila-inactiva td:first-child { text-decoration: line-through; }
    .badge-activo {
        background: rgba(34,197,94,0.15); color: #4ade80;
        padding: 4px 12px; border-radius: 20px; font-size: 11px;
        font-weight: 600; display: inline-flex; align-items: center; gap: 4px;
    }
    .badge-inactivo {
        background: rgba(239,68,68,0.15); color: #f87171;
        padding: 4px 12px; border-radius: 20px; font-size: 11px;
        font-weight: 600; display: inline-flex; align-items: center; gap: 4px;
    }
    .resultado-item {
        padding: 12px 16px; cursor: pointer; border-bottom: 1px solid rgba(255,255,255,0.04);
        transition: all 0.2s; display: flex; align-items: center; gap: 12px;
    }
    .resultado-item:hover { background: rgba(99,102,241,0.1); }
    .resultado-item:last-child { border-bottom: none; }
</style>

<!-- TABLA DE ESTUDIANTES -->
<div style="background:rgba(30,41,59,0.4); backdrop-filter:blur(20px); border:1px solid rgba(255,255,255,0.06); border-radius:20px; overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid rgba(255,255,255,0.06);">
                    <th style="text-align:left; padding:14px 16px; font-size:12px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">Nombre Completo</th>
                    <th style="text-align:left; padding:14px 16px; font-size:12px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">Registro</th>
                    <th style="text-align:left; padding:14px 16px; font-size:12px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">CI</th>
                    <th style="text-align:left; padding:14px 16px; font-size:12px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">Celular</th>
                    <th style="text-align:center; padding:14px 16px; font-size:12px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">Estado</th>
                    <th style="text-align:center; padding:14px 16px; font-size:12px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px; width:240px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($estudiantes as $estudiante)
                    <tr class="fila-estudiante {{ $estudiante->activo ? '' : 'fila-inactiva' }}" style="border-bottom:1px solid rgba(255,255,255,0.04);"
                        data-id="{{ $estudiante->id }}"
                        data-nombre="{{ strtolower($estudiante->nombre_completo) }}"
                        data-ci="{{ $estudiante->cedula }}"
                        data-registro="{{ strtolower($estudiante->registro) }}">
                        <td style="padding:14px 16px; font-size:14px; color:#f1f5f9; font-weight:500;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:12px; color:#fff; background:linear-gradient(135deg, #3b82f6 0%, #6366f1 100%); flex-shrink:0;">
                                    {{ $estudiante->iniciales }}
                                </div>
                                {{ $estudiante->nombre_completo }}
                                @if($estudiante->descuento_porcentaje > 0 && $estudiante->activo)
                                    <span style="background:rgba(245,158,11,0.15); color:#fbbf24; padding:2px 8px; border-radius:10px; font-size:10px; font-weight:600;">-{{ $estudiante->descuento_porcentaje }}%</span>
                                @endif
                            </div>
                        </td>
                        <td style="padding:14px 16px; font-size:14px; color:#cbd5e1;">{{ $estudiante->registro }}</td>
                        <td style="padding:14px 16px; font-size:14px; color:#cbd5e1;">{{ $estudiante->cedula }}</td>
                        <td style="padding:14px 16px; font-size:14px; color:#cbd5e1;">{{ $estudiante->celular ?: '-' }}</td>
                        <td style="padding:14px 16px; text-align:center;">
                            @if($estudiante->activo)
                                <span class="badge-activo"><i class="fas fa-check-circle" style="font-size:10px;"></i>Activo</span>
                            @else
                                <span class="badge-inactivo"><i class="fas fa-times-circle" style="font-size:10px;"></i>Inactivo</span>
                            @endif
                        </td>
                        <td style="padding:14px 16px; text-align:center;">
                            <div style="display:flex; gap:6px; justify-content:center; align-items:center;">
                                <button class="btn-ver" onclick="verDetalle({{ $estudiante->id }})">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                                <button class="btn-edit"
                                    data-id="{{ $estudiante->id }}"
                                    data-nombres="{{ $estudiante->nombres }}"
                                    data-paterno="{{ $estudiante->paterno }}"
                                    data-materno="{{ $estudiante->materno }}"
                                    data-registro="{{ $estudiante->registro }}"
                                    data-cedula="{{ $estudiante->cedula }}"
                                    data-celular="{{ $estudiante->celular ?? '' }}"
                                    data-observaciones="{{ $estudiante->observaciones ?? '' }}"
                                    data-descuento="{{ $estudiante->descuento_porcentaje }}">
                                    <i class="fas fa-pen"></i> Editar
                                </button>
                                <a href="{{ route('documentos.show', $estudiante->id) }}" class="btn-primary"
                                   style="background: #eab308; color: #000; padding: 6px 12px; font-size: 12px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-weight: 500;">
                                    <i class="fas fa-folder-open"></i> Documentos
                                </a>
                                @if($estudiante->activo)
                                    <form method="POST" action="{{ route('estudiantes.baja', $estudiante) }}" class="form-baja-alta" data-action-type="baja" data-nombre="{{ addslashes($estudiante->nombre_completo) }}" style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn-baja">
                                            <i class="fas fa-user-slash"></i> Baja
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('estudiantes.alta', $estudiante) }}" class="form-baja-alta" data-action-type="alta" data-nombre="{{ addslashes($estudiante->nombre_completo) }}" style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn-alta">
                                            <i class="fas fa-user-check"></i> Alta
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:60px 20px; color:#475569;">
                            <i class="fas fa-user-graduate" style="font-size:48px; margin-bottom:16px; display:block; color:#334155;"></i>
                            <p>No hay estudiantes registrados</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top:24px;">
    {{ $estudiantes->links() }}
</div>

@endsection

@section('modals')

<style>
    .modal-overlay {
        display: none !important;
        position: fixed !important;
        top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important;
        background: rgba(0,0,0,0.7) !important;
        backdrop-filter: blur(10px) !important;
        z-index: 99999 !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 20px !important;
        overflow-y: auto !important;
    }
    .modal-overlay.active { display: flex !important; }
    .detalle-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .detalle-item {
        background: rgba(15,23,42,0.5);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 12px;
        padding: 14px 16px;
    }
    .detalle-label {
        font-size: 11px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .detalle-valor {
        font-size: 14px;
        color: #f1f5f9;
        font-weight: 500;
    }
    @media (max-width: 480px) {
        .detalle-grid { grid-template-columns: 1fr; }
    }
</style>

<!-- MODAL CREAR -->
<div id="createModal" class="modal-overlay" onclick="if(event.target === this) closeModal('createModal')">
    <div style="background:rgba(30,41,59,0.98); border:1px solid rgba(255,255,255,0.1); border-radius:24px; width:100%; max-width:600px; max-height:90vh; overflow-y:auto; box-shadow:0 25px 50px -12px rgba(0,0,0,0.8); position:relative;">
        <div style="padding:28px 32px 0; display:flex; justify-content:space-between; align-items:center;">
            <h2 style="font-size:22px; font-weight:700; color:#f8fafc; display:flex; align-items:center; gap:12px;">
                <i class="fas fa-user-plus" style="color:#6366f1; font-size:20px;"></i> Registrar Estudiante
            </h2>
            <button onclick="closeModal('createModal')" style="width:36px; height:36px; border-radius:10px; border:1px solid rgba(255,255,255,0.1); background:rgba(15,23,42,0.5); color:#94a3b8; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div style="padding:24px 32px 32px;">
            <form method="POST" action="{{ route('estudiantes.store') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label>Nombres</label>
                        <input type="text" name="nombres" value="{{ old('nombres') }}" placeholder="Ej: Carlos Daniel" required>
                        @error('nombres')<<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Apellido Paterno</label>
                        <input type="text" name="paterno" value="{{ old('paterno') }}" placeholder="Ej: Cortés" required>
                        @error('paterno')<<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Apellido Materno</label>
                        <input type="text" name="materno" value="{{ old('materno') }}" placeholder="Ej: Barco">
                        @error('materno')<<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>N° de Registro</label>
                        <input type="text" name="registro" value="{{ old('registro') }}" placeholder="Ej: 2026-001" required>
                        @error('registro')<<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Cédula de Identidad</label>
                        <input type="text" name="cedula" value="{{ old('cedula') }}" placeholder="Ej: 1234567" required>
                        @error('cedula')<<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Celular</label>
                        <input type="text" name="celular" value="{{ old('celular') }}" placeholder="Ej: 77712345">
                        @error('celular')<<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Descuento (%)</label>
                        <input type="number" name="descuento_porcentaje" value="{{ old('descuento_porcentaje', 0) }}" min="0" max="100" placeholder="0" onwheel="this.blur()">
                        @error('descuento_porcentaje')<<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-group">
                    <label>Observaciones</label>
                    <input type="text" name="observaciones" value="{{ old('observaciones') }}" placeholder="Notas adicionales...">
                    @error('observaciones')<<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div style="display:flex; gap:12px; justify-content:flex-end; margin-top:8px;">
                    <button type="button" onclick="closeModal('createModal')" style="padding:12px 24px; border-radius:12px; border:1px solid rgba(255,255,255,0.1); background:transparent; color:#94a3b8; font-size:14px; cursor:pointer; font-family:'Inter',sans-serif;">Cancelar</button>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Guardar Estudiante
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR -->
<div id="editModal" class="modal-overlay" onclick="if(event.target === this) closeModal('editModal')">
    <div style="background:rgba(30,41,59,0.98); border:1px solid rgba(255,255,255,0.1); border-radius:24px; width:100%; max-width:600px; max-height:90vh; overflow-y:auto; box-shadow:0 25px 50px -12px rgba(0,0,0,0.8); position:relative;">
        <div style="padding:28px 32px 0; display:flex; justify-content:space-between; align-items:center;">
            <h2 style="font-size:22px; font-weight:700; color:#f8fafc; display:flex; align-items:center; gap:12px;">
                <i class="fas fa-user-edit" style="color:#6366f1; font-size:20px;"></i> Editar Estudiante
            </h2>
            <button onclick="closeModal('editModal')" style="width:36px; height:36px; border-radius:10px; border:1px solid rgba(255,255,255,0.1); background:rgba(15,23,42,0.5); color:#94a3b8; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div style="padding:24px 32px 32px;">
            <form id="editEstudianteForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group">
                        <label>Nombres</label>
                        <input type="text" id="edit_nombres" name="nombres" required>
                    </div>
                    <div class="form-group">
                        <label>Apellido Paterno</label>
                        <input type="text" id="edit_paterno" name="paterno" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Apellido Materno</label>
                        <input type="text" id="edit_materno" name="materno">
                    </div>
                    <div class="form-group">
                        <label>N° de Registro</label>
                        <input type="text" id="edit_registro" name="registro" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Cédula de Identidad</label>
                        <input type="text" id="edit_cedula" name="cedula" required>
                    </div>
                    <div class="form-group">
                        <label>Celular</label>
                        <input type="text" id="edit_celular" name="celular">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Descuento (%)</label>
                        <input type="number" id="edit_descuento" name="descuento_porcentaje" min="0" max="100" value="0" onwheel="this.blur()">
                    </div>
                </div>
                <div class="form-group">
                    <label>Observaciones</label>
                    <input type="text" id="edit_observaciones" name="observaciones" placeholder="Notas adicionales...">
                </div>
                <div style="display:flex; gap:12px; justify-content:flex-end; margin-top:8px;">
                    <button type="button" onclick="closeModal('editModal')" style="padding:12px 24px; border-radius:12px; border:1px solid rgba(255,255,255,0.1); background:transparent; color:#94a3b8; font-size:14px; cursor:pointer; font-family:'Inter',sans-serif;">Cancelar</button>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL VER DETALLE -->
<div id="detalleModal" class="modal-overlay" onclick="if(event.target === this) closeModal('detalleModal')">
    <div style="background:rgba(30,41,59,0.98); border:1px solid rgba(255,255,255,0.1); border-radius:24px; width:100%; max-width:520px; max-height:90vh; overflow-y:auto; box-shadow:0 25px 50px -12px rgba(0,0,0,0.8); position:relative;">
        <div style="padding:28px 32px 0; display:flex; justify-content:space-between; align-items:center;">
            <h2 style="font-size:22px; font-weight:700; color:#f8fafc; display:flex; align-items:center; gap:12px;">
                <i class="fas fa-id-card" style="color:#6366f1; font-size:20px;"></i> Detalle del Estudiante
            </h2>
            <button onclick="closeModal('detalleModal')" style="width:36px; height:36px; border-radius:10px; border:1px solid rgba(255,255,255,0.1); background:rgba(15,23,42,0.5); color:#94a3b8; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div style="padding:24px 32px 32px;">
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:24px; padding-bottom:20px; border-bottom:1px solid rgba(255,255,255,0.06);">
                <div id="detalle_avatar" style="width:56px; height:56px; border-radius:16px; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:18px; color:#fff; background:linear-gradient(135deg, #3b82f6 0%, #6366f1 100%); flex-shrink:0;">
                </div>
                <div>
                    <div id="detalle_nombre" style="font-size:18px; font-weight:700; color:#f8fafc;"></div>
                    <span id="detalle_estado" style="display:inline-block; margin-top:4px; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:500;"></span>
                </div>
            </div>

            <div class="detalle-grid">
                <div class="detalle-item">
                    <div class="detalle-label">N° de Registro</div>
                    <div class="detalle-valor" id="detalle_registro"></div>
                </div>
                <div class="detalle-item">
                    <div class="detalle-label">Cédula de Identidad</div>
                    <div class="detalle-valor" id="detalle_cedula"></div>
                </div>
                <div class="detalle-item">
                    <div class="detalle-label">Celular</div>
                    <div class="detalle-valor" id="detalle_celular"></div>
                </div>
                <div class="detalle-item">
                    <div class="detalle-label">Descuento</div>
                    <div class="detalle-valor" id="detalle_descuento"></div>
                </div>
            </div>

            <div class="detalle-item" style="margin-top:16px;">
                <div class="detalle-label">Observaciones</div>
                <div class="detalle-valor" id="detalle_observaciones" style="color:#94a3b8; font-weight:400;"></div>
            </div>

            <div class="detalle-item" style="margin-top:16px;">
                <div class="detalle-label">Fecha de Registro</div>
                <div class="detalle-valor" id="detalle_fecha"></div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CONFIRMACIÓN BAJA/ALTA -->
<div id="confirmModal" class="modal-overlay" onclick="if(event.target === this) closeConfirmModal()">
    <div style="background:rgba(30,41,59,0.98); border:1px solid rgba(255,255,255,0.1); border-radius:24px; width:100%; max-width:420px; padding:32px; text-align:center; box-shadow:0 25px 50px -12px rgba(0,0,0,0.8); position:relative;">
        <div id="confirmIcon" style="width:64px; height:64px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; font-size:28px; transition:all 0.3s;"></div>
        <h3 id="confirmTitle" style="font-size:20px; font-weight:700; color:#f8fafc; margin-bottom:8px;"></h3>
        <p id="confirmText" style="font-size:14px; color:#94a3b8; margin-bottom:28px; line-height:1.5;"></p>
        <div style="display:flex; gap:12px; justify-content:center;">
            <button onclick="closeConfirmModal()" style="padding:12px 24px; border-radius:12px; border:1px solid rgba(255,255,255,0.1); background:transparent; color:#94a3b8; font-size:14px; cursor:pointer; font-family:'Inter',sans-serif; transition:all 0.2s;">Cancelar</button>
            <button id="confirmBtn" style="padding:12px 24px; border-radius:12px; border:none; font-size:14px; cursor:pointer; font-family:'Inter',sans-serif; font-weight:600; transition:all 0.2s;"></button>
        </div>
    </div>
</div>

<script>
    const estudiantesData = {
        @foreach($estudiantes as $est)
        {{ $est->id }}: {
            nombre: '{{ addslashes($est->nombre_completo) }}',
            nombres: '{{ addslashes($est->nombres) }}',
            paterno: '{{ addslashes($est->paterno) }}',
            materno: '{{ addslashes($est->materno ?? '') }}',
            registro: '{{ $est->registro }}',
            ci: '{{ $est->cedula }}',
            celular: '{{ $est->celular ?: 'No registrado' }}',
            observaciones: '{{ addslashes($est->observaciones ?: 'Sin observaciones') }}',
            descuento: {{ $est->descuento_porcentaje }},
            activo: {{ $est->activo ? 'true' : 'false' }},
            fecha: '{{ $est->created_at->format('d/m/Y H:i') }}',
            iniciales: '{{ $est->iniciales }}'
        },
        @endforeach
    };

    function openModal(modalId) {
        document.querySelectorAll('.modal-overlay.active').forEach(m => m.classList.remove('active'));
        document.getElementById(modalId).classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
        if (!document.querySelector('.modal-overlay.active')) {
            document.body.style.overflow = '';
        }
    }

    let formPendiente = null;

    function closeConfirmModal() {
        document.getElementById('confirmModal').classList.remove('active');
        if (!document.querySelector('.modal-overlay.active')) {
            document.body.style.overflow = '';
        }
        formPendiente = null;
    }

    document.querySelectorAll('.form-baja-alta').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            formPendiente = this;
            const tipo = this.dataset.actionType;
            const nombre = this.dataset.nombre;

            const icon = document.getElementById('confirmIcon');
            const title = document.getElementById('confirmTitle');
            const text = document.getElementById('confirmText');
            const btn = document.getElementById('confirmBtn');

            if (tipo === 'baja') {
                icon.innerHTML = '<i class="fas fa-user-slash" style="color:#fbbf24;"></i>';
                icon.style.background = 'rgba(245,158,11,0.15)';
                icon.style.border = '1px solid rgba(245,158,11,0.2)';
                title.textContent = 'Dar de baja';
                text.innerHTML = `¿Estás seguro de dar de <strong style="color:#fbbf24;">baja</strong> a <strong style="color:#f1f5f9;">${nombre}</strong>?`;
                btn.textContent = 'Sí, dar de baja';
                btn.style.background = 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)';
                btn.style.color = '#fff';
                btn.onmouseover = () => btn.style.filter = 'brightness(1.1)';
                btn.onmouseout = () => btn.style.filter = 'brightness(1)';
            } else {
                icon.innerHTML = '<i class="fas fa-user-check" style="color:#4ade80;"></i>';
                icon.style.background = 'rgba(34,197,94,0.15)';
                icon.style.border = '1px solid rgba(34,197,94,0.2)';
                title.textContent = 'Dar de alta';
                text.innerHTML = `¿Estás seguro de dar de <strong style="color:#4ade80;">alta</strong> a <strong style="color:#f1f5f9;">${nombre}</strong>?`;
                btn.textContent = 'Sí, dar de alta';
                btn.style.background = 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)';
                btn.style.color = '#fff';
                btn.onmouseover = () => btn.style.filter = 'brightness(1.1)';
                btn.onmouseout = () => btn.style.filter = 'brightness(1)';
            }

            openModal('confirmModal');
        });
    });

    document.getElementById('confirmBtn').addEventListener('click', function() {
        if (formPendiente) {
            formPendiente.submit();
        }
        closeConfirmModal();
    });

    function verDetalle(id) {
        const e = estudiantesData[id];
        if (!e) return;

        document.getElementById('detalle_avatar').textContent = e.iniciales;
        document.getElementById('detalle_nombre').textContent = e.nombre;
        document.getElementById('detalle_registro').textContent = e.registro;
        document.getElementById('detalle_cedula').textContent = e.ci;
        document.getElementById('detalle_celular').textContent = e.celular;
        document.getElementById('detalle_descuento').textContent = e.descuento > 0 ? e.descuento + '%' : 'Sin descuento';
        document.getElementById('detalle_observaciones').textContent = e.observaciones;
        document.getElementById('detalle_fecha').textContent = e.fecha;

        const estadoBadge = document.getElementById('detalle_estado');
        if (e.activo) {
            estadoBadge.textContent = 'ACTIVO';
            estadoBadge.style.cssText = 'display:inline-block; margin-top:4px; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:500; background:rgba(34,197,94,0.15); color:#4ade80;';
        } else {
            estadoBadge.textContent = 'DE BAJA';
            estadoBadge.style.cssText = 'display:inline-block; margin-top:4px; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:500; background:rgba(239,68,68,0.15); color:#f87171;';
        }

        openModal('detalleModal');
    }

    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            document.getElementById('editEstudianteForm').action = '/estudiantes/' + id;
            document.getElementById('edit_nombres').value = this.dataset.nombres;
            document.getElementById('edit_paterno').value = this.dataset.paterno;
            document.getElementById('edit_materno').value = this.dataset.materno || '';
            document.getElementById('edit_registro').value = this.dataset.registro;
            document.getElementById('edit_cedula').value = this.dataset.cedula;
            document.getElementById('edit_celular').value = this.dataset.celular || '';
            document.getElementById('edit_observaciones').value = this.dataset.observaciones || '';
            document.getElementById('edit_descuento').value = this.dataset.descuento || 0;
            openModal('editModal');
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal('createModal');
            closeModal('editModal');
            closeModal('detalleModal');
            closeConfirmModal();
        }
    });

    setTimeout(function() {
        const successAlert = document.getElementById('successAlert');
        const errorAlert = document.getElementById('errorAlert');
        if (successAlert) {
            successAlert.style.animation = 'fadeOut 0.5s ease forwards';
            setTimeout(() => successAlert.remove(), 500);
        }
        if (errorAlert) {
            errorAlert.style.animation = 'fadeOut 0.5s ease forwards';
            setTimeout(() => errorAlert.remove(), 500);
        }
    }, 4000);

    const inputBuscar = document.getElementById('buscarEstudiante');
    const resultadosDiv = document.getElementById('resultadosBusqueda');
    const filas = document.querySelectorAll('.fila-estudiante');

    inputBuscar.addEventListener('input', function() {
        const termino = this.value.toLowerCase().trim();

        filas.forEach(fila => {
            const nombre = fila.dataset.nombre;
            const ci = fila.dataset.ci;
            const registro = fila.dataset.registro;

            if (nombre.includes(termino) || ci.includes(termino) || registro.includes(termino)) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });

        if (termino.length < 2) {
            resultadosDiv.style.display = 'none';
            return;
        }

        const coincidencias = Object.entries(estudiantesData).filter(([id, e]) =>
            e.nombre.toLowerCase().includes(termino) ||
            e.ci.includes(termino) ||
            e.registro.toLowerCase().includes(termino)
        );

        if (coincidencias.length === 0) {
            resultadosDiv.style.display = 'none';
            return;
        }

        resultadosDiv.innerHTML = coincidencias.map(([id, e]) => `
            <div class="resultado-item" onclick="seleccionarEstudiante(${id})">
                <div style="width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:12px; color:#fff; background:linear-gradient(135deg, #3b82f6 0%, #6366f1 100%); flex-shrink:0;">
                    ${e.iniciales}
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="font-size:14px; font-weight:500; color:#f1f5f9;">${e.nombre}</div>
                    <div style="font-size:12px; color:#64748b;">Reg: ${e.registro} | CI: ${e.ci}</div>
                </div>
                ${e.descuento > 0 ? `<span style="background:rgba(245,158,11,0.15); color:#fbbf24; padding:2px 8px; border-radius:10px; font-size:10px; font-weight:600;">-${e.descuento}%</span>` : ''}
                ${!e.activo ? `<span style="background:rgba(239,68,68,0.15); color:#f87171; padding:2px 8px; border-radius:10px; font-size:10px; font-weight:600;">BAJA</span>` : ''}
            </div>
        `).join('');

        resultadosDiv.style.display = 'block';
    });

    document.addEventListener('click', function(e) {
        if (!inputBuscar.contains(e.target) && !resultadosDiv.contains(e.target)) {
            resultadosDiv.style.display = 'none';
        }
    });

    function seleccionarEstudiante(id) {
        const fila = document.querySelector(`.fila-estudiante[data-id="${id}"]`);
        if (fila) {
            fila.scrollIntoView({ behavior: 'smooth', block: 'center' });
            fila.style.background = 'rgba(99,102,241,0.15)';
            setTimeout(() => {
                fila.style.background = '';
                fila.style.transition = 'background 1s ease';
            }, 2000);
        }
        resultadosDiv.style.display = 'none';
        inputBuscar.value = '';

        filas.forEach(f => f.style.display = 'none');
        if (fila) fila.style.display = '';
    }

    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('wheel', function(e) {
            e.preventDefault();
            this.blur();
        });
    });
</script>

@endsection

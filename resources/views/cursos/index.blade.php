@extends('layouts.app')

@section('title', 'Gestión de Cursos')
@section('page-title', 'Gestión de Cursos')
@section('page-subtitle', 'Administra los programas de posgrado F.I.N.O.R')

@section('content')

<div style="display:flex; align-items:center; gap:16px; margin-bottom:32px; flex-wrap:wrap;">
    <button onclick="openModal('createModal')" class="btn-primary">
        <i class="fas fa-plus"></i> Crear Curso Nuevo
    </button>

    <div style="position:relative; flex:1; max-width:400px;">
        <i class="fas fa-search" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#64748b; font-size:14px;"></i>
        <input type="text" id="buscarCurso" placeholder="Buscar por nombre, tipo o período..."
            style="width:100%; padding:12px 16px 12px 40px; background:rgba(15,23,42,0.5); border:1px solid rgba(255,255,255,0.08); border-radius:14px; color:#f1f5f9; font-size:14px; font-family:'Inter',sans-serif; outline:none;">
    </div>

    @if(session('success'))
        <div id="successAlert" class="alert alert-success" style="display:flex; align-items:center; gap:10px; margin:0; animation:slideIn 0.3s ease;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
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
    .grid-cursos {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 24px;
    }
    .curso-card {
        background: rgba(30,41,59,0.4);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
        cursor: pointer;
    }
    .curso-card:hover {
        transform: translateY(-4px);
        border-color: rgba(255,255,255,0.12);
        box-shadow: 0 20px 40px rgba(0,0,0,0.4);
    }
    .curso-card.inactivo {
        opacity: 0.5;
    }
    .curso-card.inactivo:hover {
        opacity: 0.7;
    }
    .curso-header {
        height: 4px;
        width: 100%;
    }
    .curso-body {
        padding: 24px;
    }
    .curso-tipo-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 16px;
    }
    .curso-nombre {
        font-size: 20px;
        font-weight: 700;
        color: #f8fafc;
        line-height: 1.3;
        margin-bottom: 8px;
    }
    .curso-version {
        font-size: 13px;
        color: #64748b;
        font-weight: 500;
        margin-bottom: 16px;
    }
    .curso-info-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        font-size: 14px;
        color: #94a3b8;
    }
    .curso-info-row i {
        width: 16px;
        text-align: center;
        color: #64748b;
    }
    .curso-cupo-bar {
        width: 100%;
        height: 6px;
        background: rgba(15,23,42,0.5);
        border-radius: 3px;
        margin-top: 16px;
        overflow: hidden;
    }
    .curso-cupo-fill {
        height: 100%;
        border-radius: 3px;
        transition: width 0.3s ease;
    }
    .curso-cupo-text {
        display: flex;
        justify-content: space-between;
        margin-top: 6px;
        font-size: 12px;
        color: #64748b;
    }
    .curso-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 24px;
        border-top: 1px solid rgba(255,255,255,0.06);
    }
    .curso-estado {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .curso-actions {
        display: flex;
        gap: 8px;
    }
    .btn-curso-action {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: 1px solid rgba(255,255,255,0.08);
        background: rgba(15,23,42,0.4);
        color: #94a3b8;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        font-size: 14px;
    }
    .btn-curso-action.ver:hover { background: rgba(34,197,94,0.1); color: #4ade80; border-color: rgba(34,197,94,0.3); }
    .btn-curso-action.edit:hover { background: rgba(99,102,241,0.1); color: #818cf8; border-color: rgba(99,102,241,0.3); }
    .btn-curso-action.toggle:hover { background: rgba(245,158,11,0.1); color: #fbbf24; border-color: rgba(245,158,11,0.3); }
    .btn-curso-action.toggle.activo:hover { background: rgba(239,68,68,0.1); color: #f87171; border-color: rgba(239,68,68,0.3); }
    .costos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 12px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid rgba(255,255,255,0.06);
    }
    .costo-item {
        text-align: center;
    }
    .costo-label {
        font-size: 10px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .costo-valor {
        font-size: 15px;
        font-weight: 700;
        color: #f1f5f9;
    }
    .modulos-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        background: rgba(99,102,241,0.15);
        color: #818cf8;
        margin-bottom: 16px;
    }
    .defensa-input-group { display: none; }
    .defensa-input-group.visible { display: block; }
    @media (max-width: 768px) {
        .grid-cursos { grid-template-columns: 1fr; }
    }
</style>

<div class="grid-cursos" id="gridCursos">
    @forelse($cursos as $curso)
        <div class="curso-card {{ $curso->activo ? '' : 'inactivo' }}"
             data-nombre="{{ strtolower((string) $curso->nombre) }}"
             data-tipo="{{ strtolower($curso->tipo->value) }}"
             data-periodo="{{ strtolower($curso->periodo) }}"
             onclick="window.location.href='{{ route('cursos.show', $curso) }}'">

            <div class="curso-header" style="background: {{ $curso->color_tipo }};"></div>

            <div class="curso-body">
                <span class="curso-tipo-badge" style="background: {{ $curso->color_tipo_bg }}; color: {{ $curso->color_tipo }};">
                    <i class="fas {{ $curso->icono_tipo }}"></i> {{ $curso->tipo->value }}
                </span>

                <div class="curso-nombre">{{ $curso->nombre }}</div>
                <div class="curso-version">
                    <i class="fas fa-code-branch" style="margin-right:6px; color:#64748b;"></i>
                    V{{ $curso->version }}E{{ $curso->edicion }} — {{ $curso->periodo }}
                </div>

                <span class="modulos-badge">
                    <i class="fas fa-cubes"></i> {{ $curso->total_modulos }} módulos
                </span>

                <div class="curso-info-row">
                    <i class="fas fa-users"></i>
                    <span>{{ $curso->inscripciones_count }} inscritos</span>
                    <i class="fas fa-check-circle" style="margin-left:12px; color:#22c55e;"></i>
                    <span>{{ $curso->activos_count }} activos</span>
                </div>

                <div class="curso-cupo-bar">
                    @php
                        $porcentaje = $curso->cupo > 0 ? min(100, ($curso->inscripciones_count / $curso->cupo) * 100) : 0;
                        $barColor = $porcentaje >= 90 ? '#ef4444' : ($porcentaje >= 70 ? '#f59e0b' : '#22c55e');
                    @endphp
                    <div class="curso-cupo-fill" style="width: {{ $porcentaje }}%; background: {{ $barColor }};"></div>
                </div>
                <div class="curso-cupo-text">
                    <span>{{ $curso->cupo_disponible }} disponibles</span>
                    <span>{{ number_format($porcentaje, 0) }}%</span>
                </div>

                <div class="costos-grid">
                    <div class="costo-item">
                        <div class="costo-label">Matrícula</div>
                        <div class="costo-valor">{{ number_format($curso->costo_matricula, 0) }} Bs</div>
                    </div>
                    <div class="costo-item">
                        <div class="costo-label">Total Estudio</div>
                        <div class="costo-valor">{{ number_format($curso->costo_total_estudio, 0) }} Bs</div>
                    </div>
                    <div class="costo-item">
                        <div class="costo-label">Módulo</div>
                        <div class="costo-valor">{{ number_format($curso->costo_modulo, 0) }} Bs</div>
                    </div>
                    @if($curso->tipo->value == 'Diplomado' || $curso->tipo->value == 'Especialidad' || $curso->tipo->value == 'Maestría')
                    <div class="costo-item">
                        <div class="costo-label">Def. Diplomado</div>
                        <div class="costo-valor">{{ number_format($curso->costo_defensa_diplomado, 0) }} Bs</div>
                    </div>
                    @endif
                    @if($curso->tipo->value == 'Especialidad' || $curso->tipo->value == 'Maestría')
                    <div class="costo-item">
                        <div class="costo-label">Def. Especialidad</div>
                        <div class="costo-valor">{{ number_format($curso->costo_defensa_especialidad, 0) }} Bs</div>
                    </div>
                    @endif
                    @if($curso->tipo->value == 'Maestría')
                    <div class="costo-item">
                        <div class="costo-label">Def. Maestría</div>
                        <div class="costo-valor">{{ number_format($curso->costo_defensa_maestria, 0) }} Bs</div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="curso-footer">
                @if($curso->activo)
                    <span class="curso-estado" style="background: rgba(34,197,94,0.15); color: #4ade80;">
                        <i class="fas fa-check-circle" style="font-size:10px;"></i> Activo
                    </span>
                @else
                    <span class="curso-estado" style="background: rgba(239,68,68,0.15); color: #f87171;">
                        <i class="fas fa-times-circle" style="font-size:10px;"></i> Inactivo
                    </span>
                @endif

                <div class="curso-actions">
                    <button class="btn-curso-action ver" onclick="event.stopPropagation(); verDetalleCurso({{ $curso->id }})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn-curso-action edit" onclick="event.stopPropagation(); openEditModal({{ $curso->id }})">
                        <i class="fas fa-pen"></i>
                    </button>
                    <form method="POST" action="{{ route('cursos.toggle', $curso) }}" style="display:inline;" class="form-toggle" onclick="event.stopPropagation()">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn-curso-action toggle {{ $curso->activo ? 'activo' : '' }}" title="{{ $curso->activo ? 'Desactivar' : 'Activar' }}">
                            <i class="fas {{ $curso->activo ? 'fa-pause' : 'fa-play' }}"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; text-align:center; padding:80px 20px; color:#475569;">
            <i class="fas fa-book-open" style="font-size:64px; margin-bottom:20px; display:block; color:#334155;"></i>
            <p style="font-size:18px; margin-bottom:8px;">No hay cursos registrados</p>
            <p style="font-size:14px; color:#64748b;">Haz clic en "Crear Curso Nuevo" para comenzar</p>
        </div>
    @endforelse
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
    .costos-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 12px;
    }
    .modulos-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 12px;
        margin-top: 12px;
    }
    .cuotas-preview {
        background: rgba(15,23,42,0.5);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 12px;
        padding: 16px;
        margin-top: 16px;
    }
    .cuota-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid rgba(255,255,255,0.04);
        font-size: 14px;
    }
    .cuota-item:last-child { border-bottom: none; }
    @media (max-width: 600px) {
        .costos-form-grid { grid-template-columns: 1fr; }
        .modulos-form-grid { grid-template-columns: 1fr; }
    }
</style>

<div id="createModal" class="modal-overlay" onclick="if(event.target === this) closeModal('createModal')">
    <div style="background:rgba(30,41,59,0.98); border:1px solid rgba(255,255,255,0.1); border-radius:24px; width:100%; max-width:720px; max-height:90vh; overflow-y:auto; box-shadow:0 25px 50px -12px rgba(0,0,0,0.8); position:relative;">
        <div style="padding:28px 32px 0; display:flex; justify-content:space-between; align-items:center;">
            <h2 style="font-size:22px; font-weight:700; color:#f8fafc; display:flex; align-items:center; gap:12px;">
                <i class="fas fa-plus-circle" style="color:#6366f1; font-size:20px;"></i> Crear Curso Nuevo
            </h2>
            <button onclick="closeModal('createModal')" style="width:36px; height:36px; border-radius:10px; border:1px solid rgba(255,255,255,0.1); background:rgba(15,23,42,0.5); color:#94a3b8; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div style="padding:24px 32px 32px;">
            <form method="POST" action="{{ route('cursos.store') }}" id="createForm">
                @csrf

                <div class="form-row">
                    <div class="form-group" style="flex:2;">
                        <label>Nombre del Curso</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="Ej: Educación Superior" required>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Tipo</label>
                        <select name="tipo" id="create_tipo" required class="plan-selector" onchange="actualizarTipoForm('create')">
                            <option value="">Seleccionar...</option>
                            <option value="Diplomado" {{ old('tipo') == 'Diplomado' ? 'selected' : '' }}>Diplomado</option>
                            <option value="Especialidad" {{ old('tipo') == 'Especialidad' ? 'selected' : '' }}>Especialidad</option>
                            <option value="Maestría" {{ old('tipo') == 'Maestría' ? 'selected' : '' }}>Maestría</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Versión</label>
                        <input type="number" name="version" value="{{ old('version') }}" min="1" required onwheel="this.blur()" placeholder="Ej: 4">
                    </div>
                    <div class="form-group">
                        <label>Edición</label>
                        <input type="number" name="edicion" value="{{ old('edicion') }}" min="1" required onwheel="this.blur()" placeholder="Ej: 2">
                    </div>
                    <div class="form-group">
                        <label>Período</label>
                        <input type="text" name="periodo" value="{{ old('periodo') }}" placeholder="Ej: 2026-1" required style="width:100%; padding:12px 16px; background:rgba(15,23,42,0.5); border:1px solid rgba(255,255,255,0.08); border-radius:12px; color:#f1f5f9; font-size:14px; font-family:'Inter',sans-serif;">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="flex:1;">
                        <label>Cupo</label>
                        <input type="number" name="cupo" value="{{ old('cupo') }}" min="1" required onwheel="this.blur()" placeholder="Ej: 30">
                    </div>
                </div>

                <div style="margin:20px 0 16px; font-size:14px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">Costos</div>

                <div class="costos-form-grid">
                    <div class="form-group">
                        <label>Matrícula (Bs)</label>
                        <input type="number" name="costo_matricula" id="create_matricula" value="{{ old('costo_matricula') }}" min="0" step="0.01" required onwheel="this.blur()" oninput="calcularModulo('create')" onchange="calcularModulo('create')" placeholder="Ej: 500">
                    </div>
                    <div class="form-group">
                        <label>Costo Total Estudio (Bs)</label>
                        <input type="number" name="costo_total_estudio" id="create_costo_total_estudio" value="{{ old('costo_total_estudio') }}" min="0" step="0.01" required onwheel="this.blur()" oninput="calcularModulo('create')" onchange="calcularModulo('create')" placeholder="Ej: 12500">
                        <small style="color:#64748b; font-size:11px; display:block; margin-top:4px;">Se divide entre los módulos</small>
                    </div>
                    <div class="form-group">
                        <label>Módulo Calculado (Bs)</label>
                        <input type="number" id="create_modulo" value="" min="0" step="0.01" readonly style="background:rgba(15,23,42,0.3);">
                    </div>
                    <div class="form-group defensa-input-group" id="create_group_def_dip">
                        <label>Def. Diplomado (Bs)</label>
                        <input type="number" name="costo_defensa_diplomado" id="create_defensa_dip" value="{{ old('costo_defensa_diplomado') }}" min="0" step="0.01" onwheel="this.blur()" oninput="calcularModulo('create')" onchange="calcularModulo('create')" placeholder="Ej: 500">
                    </div>
                    <div class="form-group defensa-input-group" id="create_group_def_esp">
                        <label>Def. Especialidad (Bs)</label>
                        <input type="number" name="costo_defensa_especialidad" id="create_defensa_esp" value="{{ old('costo_defensa_especialidad') }}" min="0" step="0.01" onwheel="this.blur()" oninput="calcularModulo('create')" onchange="calcularModulo('create')" placeholder="Ej: 700">
                    </div>
                    <div class="form-group defensa-input-group" id="create_group_def_mae">
                        <label>Def. Maestría (Bs)</label>
                        <input type="number" name="costo_defensa_maestria" id="create_defensa_mae" value="{{ old('costo_defensa_maestria') }}" min="0" step="0.01" onwheel="this.blur()" oninput="calcularModulo('create')" onchange="calcularModulo('create')" placeholder="Ej: 4000">
                    </div>
                </div>

                <div style="margin:20px 0 16px; font-size:14px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">Módulos por Fase</div>

                <div class="modulos-form-grid">
                    <div class="form-group">
                        <label>Módulos Diplomado</label>
                        <input type="number" name="nro_modulos_diplomado" id="create_modulos_dip" value="{{ old('nro_modulos_diplomado') }}" min="1" required onwheel="this.blur()" oninput="calcularModulo('create')" onchange="calcularModulo('create')" placeholder="Ej: 5">
                    </div>
                    <div class="form-group defensa-input-group" id="create_group_mod_esp">
                        <label>Módulos Especialidad</label>
                        <input type="number" name="nro_modulos_especialidad" id="create_modulos_esp" value="{{ old('nro_modulos_especialidad') }}" min="0" onwheel="this.blur()" oninput="calcularModulo('create')" onchange="calcularModulo('create')" placeholder="Ej: 5">
                    </div>
                    <div class="form-group defensa-input-group" id="create_group_mod_mae">
                        <label>Módulos Maestría</label>
                        <input type="number" name="nro_modulos_maestria" id="create_modulos_mae" value="{{ old('nro_modulos_maestria') }}" min="0" onwheel="this.blur()" oninput="calcularModulo('create')" onchange="calcularModulo('create')" placeholder="Ej: 5">
                    </div>
                </div>

                <div class="cuotas-preview" id="create_preview" style="display:none;">
                    <div style="font-size:12px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px;">Vista previa del plan de pagos</div>
                    <div id="create_preview_content"></div>
                    <div style="margin-top:12px; padding-top:12px; border-top:1px solid rgba(255,255,255,0.1); display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:14px; color:#94a3b8;">Costo Total Programa:</span>
                        <span style="font-size:20px; font-weight:700; color:#f8fafc;" id="create_total"></span>
                    </div>
                </div>

                <div style="display:flex; gap:12px; justify-content:flex-end; margin-top:24px;">
                    <button type="button" onclick="closeModal('createModal')" style="padding:12px 24px; border-radius:12px; border:1px solid rgba(255,255,255,0.1); background:transparent; color:#94a3b8; font-size:14px; cursor:pointer; font-family:'Inter',sans-serif;">Cancelar</button>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Guardar Curso
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="editModal" class="modal-overlay" onclick="if(event.target === this) closeModal('editModal')">
    <div style="background:rgba(30,41,59,0.98); border:1px solid rgba(255,255,255,0.1); border-radius:24px; width:100%; max-width:720px; max-height:90vh; overflow-y:auto; box-shadow:0 25px 50px -12px rgba(0,0,0,0.8); position:relative;">
        <div style="padding:28px 32px 0; display:flex; justify-content:space-between; align-items:center;">
            <h2 style="font-size:22px; font-weight:700; color:#f8fafc; display:flex; align-items:center; gap:12px;">
                <i class="fas fa-pen" style="color:#6366f1; font-size:20px;"></i> Editar Curso
            </h2>
            <button onclick="closeModal('editModal')" style="width:36px; height:36px; border-radius:10px; border:1px solid rgba(255,255,255,0.1); background:rgba(15,23,42,0.5); color:#94a3b8; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div style="padding:24px 32px 32px;">
            <form id="editCursoForm" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <div class="form-group" style="flex:2;">
                        <label>Nombre del Curso</label>
                        <input type="text" id="edit_nombre" name="nombre" required>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Tipo</label>
                        <select id="edit_tipo" name="tipo" required class="plan-selector" onchange="actualizarTipoForm('edit')">
                            <option value="Diplomado">Diplomado</option>
                            <option value="Especialidad">Especialidad</option>
                            <option value="Maestría">Maestría</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Versión</label>
                        <input type="number" id="edit_version" name="version" min="1" required onwheel="this.blur()">
                    </div>
                    <div class="form-group">
                        <label>Edición</label>
                        <input type="number" id="edit_edicion" name="edicion" min="1" required onwheel="this.blur()">
                    </div>
                    <div class="form-group">
                        <label>Período</label>
                        <input type="text" id="edit_periodo" name="periodo" required style="width:100%; padding:12px 16px; background:rgba(15,23,42,0.5); border:1px solid rgba(255,255,255,0.08); border-radius:12px; color:#f1f5f9; font-size:14px; font-family:'Inter',sans-serif;">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="flex:1;">
                        <label>Cupo</label>
                        <input type="number" id="edit_cupo" name="cupo" min="1" required onwheel="this.blur()">
                    </div>
                </div>

                <div style="margin:20px 0 16px; font-size:14px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">Costos</div>

                <div class="costos-form-grid">
                    <div class="form-group">
                        <label>Matrícula (Bs)</label>
                        <input type="number" id="edit_matricula" name="costo_matricula" min="0" step="0.01" required onwheel="this.blur()" oninput="calcularModulo('edit')" onchange="calcularModulo('edit')">
                    </div>
                    <div class="form-group">
                        <label>Costo Total Estudio (Bs)</label>
                        <input type="number" id="edit_costo_total_estudio" name="costo_total_estudio" min="0" step="0.01" required onwheel="this.blur()" oninput="calcularModulo('edit')" onchange="calcularModulo('edit')">
                    </div>
                    <div class="form-group">
                        <label>Módulo Calculado (Bs)</label>
                        <input type="number" id="edit_modulo" readonly style="background:rgba(15,23,42,0.3);">
                    </div>
                    <div class="form-group defensa-input-group" id="edit_group_def_dip">
                        <label>Def. Diplomado (Bs)</label>
                        <input type="number" id="edit_defensa_dip" name="costo_defensa_diplomado" min="0" step="0.01" onwheel="this.blur()" oninput="calcularModulo('edit')" onchange="calcularModulo('edit')">
                    </div>
                    <div class="form-group defensa-input-group" id="edit_group_def_esp">
                        <label>Def. Especialidad (Bs)</label>
                        <input type="number" id="edit_defensa_esp" name="costo_defensa_especialidad" min="0" step="0.01" onwheel="this.blur()" oninput="calcularModulo('edit')" onchange="calcularModulo('edit')">
                    </div>
                    <div class="form-group defensa-input-group" id="edit_group_def_mae">
                        <label>Def. Maestría (Bs)</label>
                        <input type="number" id="edit_defensa_mae" name="costo_defensa_maestria" min="0" step="0.01" onwheel="this.blur()" oninput="calcularModulo('edit')" onchange="calcularModulo('edit')">
                    </div>
                </div>

                <div style="margin:20px 0 16px; font-size:14px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px;">Módulos por Fase</div>

                <div class="modulos-form-grid">
                    <div class="form-group">
                        <label>Módulos Diplomado</label>
                        <input type="number" id="edit_modulos_dip" name="nro_modulos_diplomado" min="1" required onwheel="this.blur()" oninput="calcularModulo('edit')" onchange="calcularModulo('edit')">
                    </div>
                    <div class="form-group defensa-input-group" id="edit_group_mod_esp">
                        <label>Módulos Especialidad</label>
                        <input type="number" id="edit_modulos_esp" name="nro_modulos_especialidad" min="0" onwheel="this.blur()" oninput="calcularModulo('edit')" onchange="calcularModulo('edit')">
                    </div>
                    <div class="form-group defensa-input-group" id="edit_group_mod_mae">
                        <label>Módulos Maestría</label>
                        <input type="number" id="edit_modulos_mae" name="nro_modulos_maestria" min="0" onwheel="this.blur()" oninput="calcularModulo('edit')" onchange="calcularModulo('edit')">
                    </div>
                </div>

                <div class="cuotas-preview" id="edit_preview" style="display:none;">
                    <div style="font-size:12px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px;">Vista previa del plan de pagos</div>
                    <div id="edit_preview_content"></div>
                    <div style="margin-top:12px; padding-top:12px; border-top:1px solid rgba(255,255,255,0.1); display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:14px; color:#94a3b8;">Costo Total Programa:</span>
                        <span style="font-size:20px; font-weight:700; color:#f8fafc;" id="edit_total"></span>
                    </div>
                </div>

                <div style="display:flex; gap:12px; justify-content:flex-end; margin-top:24px;">
                    <button type="button" onclick="closeModal('editModal')" style="padding:12px 24px; border-radius:12px; border:1px solid rgba(255,255,255,0.1); background:transparent; color:#94a3b8; font-size:14px; cursor:pointer; font-family:'Inter',sans-serif;">Cancelar</button>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="detalleModal" class="modal-overlay" onclick="if(event.target === this) closeModal('detalleModal')">
    <div style="background:rgba(30,41,59,0.98); border:1px solid rgba(255,255,255,0.1); border-radius:24px; width:100%; max-width:560px; max-height:90vh; overflow-y:auto; box-shadow:0 25px 50px -12px rgba(0,0,0,0.8); position:relative;">
        <div style="padding:28px 32px 0; display:flex; justify-content:space-between; align-items:center;">
            <h2 style="font-size:22px; font-weight:700; color:#f8fafc; display:flex; align-items:center; gap:12px;">
                <i class="fas fa-book-open" style="color:#6366f1; font-size:20px;"></i> Detalle del Curso
            </h2>
            <button onclick="closeModal('detalleModal')" style="width:36px; height:36px; border-radius:10px; border:1px solid rgba(255,255,255,0.1); background:rgba(15,23,42,0.5); color:#94a3b8; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div style="padding:24px 32px 32px;" id="detalleContent"></div>
    </div>
</div>

<div id="toggleModal" class="modal-overlay" onclick="if(event.target === this) closeModal('toggleModal')">
    <div style="background:rgba(30,41,59,0.98); border:1px solid rgba(255,255,255,0.1); border-radius:24px; width:100%; max-width:420px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.8); position:relative;">
        <div style="padding:28px 32px 0; display:flex; justify-content:space-between; align-items:center;">
            <h2 style="font-size:20px; font-weight:700; color:#f8fafc; display:flex; align-items:center; gap:12px;">
                <i class="fas fa-exclamation-triangle" style="color:#f59e0b; font-size:18px;"></i> Confirmar Acción
            </h2>
            <button onclick="closeModal('toggleModal')" style="width:36px; height:36px; border-radius:10px; border:1px solid rgba(255,255,255,0.1); background:rgba(15,23,42,0.5); color:#94a3b8; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div style="padding:24px 32px 32px;">
            <p style="font-size:15px; color:#cbd5e1; margin-bottom:24px; line-height:1.6;" id="toggleMessage"></p>
            <div style="display:flex; gap:12px; justify-content:flex-end;">
                <button onclick="closeModal('toggleModal')" style="padding:12px 24px; border-radius:12px; border:1px solid rgba(255,255,255,0.1); background:transparent; color:#94a3b8; font-size:14px; cursor:pointer; font-family:'Inter',sans-serif;">Cancelar</button>
                <button id="toggleConfirmBtn" class="btn-submit">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
    const cursosData = {
        @foreach($cursos as $c)
        {{ $c->id }}: {
            nombre: '{{ addslashes($c->nombre) }}',
            tipo: '{{ $c->tipo->value }}',
            version: {{ $c->version }},
            edicion: {{ $c->edicion }},
            periodo: '{{ addslashes($c->periodo) }}',
            matricula: {{ $c->costo_matricula }},
            costo_total_estudio: {{ $c->costo_total_estudio }},
            costo_modulo: {{ $c->costo_modulo }},
            defensa_dip: {{ $c->costo_defensa_diplomado }},
            defensa_esp: {{ $c->costo_defensa_especialidad }},
            defensa_mae: {{ $c->costo_defensa_maestria }},
            modulos_dip: {{ $c->nro_modulos_diplomado }},
            modulos_esp: {{ $c->nro_modulos_especialidad ?? 0 }},
            modulos_mae: {{ $c->nro_modulos_maestria ?? 0 }},
            total_modulos: {{ $c->total_modulos }},
            cupo: {{ $c->cupo }},
            inscritos: {{ $c->inscripciones_count }},
            activo: {{ $c->activo ? 'true' : 'false' }},
            color: '{{ $c->color_tipo }}',
            colorBg: '{{ $c->color_tipo_bg }}',
            icono: '{{ $c->icono_tipo }}',
            costo_total_programa: {{ $c->costo_total_programa }},
            fecha: '{{ $c->created_at->format('d/m/Y') }}'
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
        document.body.style.overflow = '';

        if (modalId === 'createModal') {
            limpiarFormulario('create');
        }
    }

    function limpiarFormulario(prefix) {
        const form = document.getElementById(prefix + 'Form');
        if (form) form.reset();

        document.getElementById(prefix + '_modulo').value = '';
        document.getElementById(prefix + '_preview').style.display = 'none';

        document.getElementById(prefix + '_group_def_dip').classList.remove('visible');
        document.getElementById(prefix + '_group_def_esp').classList.remove('visible');
        document.getElementById(prefix + '_group_def_mae').classList.remove('visible');
        document.getElementById(prefix + '_group_mod_esp').classList.remove('visible');
        document.getElementById(prefix + '_group_mod_mae').classList.remove('visible');
    }

    function actualizarTipoForm(prefix) {
        const tipo = document.getElementById(prefix + '_tipo').value;

        document.getElementById(prefix + '_group_def_dip').classList.remove('visible');
        document.getElementById(prefix + '_group_def_esp').classList.remove('visible');
        document.getElementById(prefix + '_group_def_mae').classList.remove('visible');
        document.getElementById(prefix + '_group_mod_esp').classList.remove('visible');
        document.getElementById(prefix + '_group_mod_mae').classList.remove('visible');

        if (tipo === 'Diplomado') {
            document.getElementById(prefix + '_group_def_dip').classList.add('visible');
        } else if (tipo === 'Especialidad') {
            document.getElementById(prefix + '_group_def_dip').classList.add('visible');
            document.getElementById(prefix + '_group_def_esp').classList.add('visible');
            document.getElementById(prefix + '_group_mod_esp').classList.add('visible');
        } else if (tipo === 'Maestría') {
            document.getElementById(prefix + '_group_def_dip').classList.add('visible');
            document.getElementById(prefix + '_group_def_esp').classList.add('visible');
            document.getElementById(prefix + '_group_def_mae').classList.add('visible');
            document.getElementById(prefix + '_group_mod_esp').classList.add('visible');
            document.getElementById(prefix + '_group_mod_mae').classList.add('visible');
        }

        calcularModulo(prefix);
    }

    function calcularModulo(prefix) {
        const tipo = document.getElementById(prefix + '_tipo').value;
        const costoTotal = parseFloat(document.getElementById(prefix + '_costo_total_estudio').value) || 0;
        const matricula = parseFloat(document.getElementById(prefix + '_matricula').value) || 0;
        const modulosDip = parseInt(document.getElementById(prefix + '_modulos_dip').value) || 0;
        const modulosEsp = parseInt(document.getElementById(prefix + '_modulos_esp').value) || 0;
        const modulosMae = parseInt(document.getElementById(prefix + '_modulos_mae').value) || 0;

        if (!tipo || costoTotal <= 0 || modulosDip <= 0) {
            document.getElementById(prefix + '_modulo').value = '';
            document.getElementById(prefix + '_preview').style.display = 'none';
            return;
        }

        let totalModulos = modulosDip;
        if (tipo === 'Especialidad') totalModulos += modulosEsp;
        if (tipo === 'Maestría') totalModulos += modulosEsp + modulosMae;

        const modulo = totalModulos > 0 ? costoTotal / totalModulos : 0;
        document.getElementById(prefix + '_modulo').value = modulo > 0 ? modulo.toFixed(2) : '';

        actualizarPreview(prefix);
    }

    function actualizarPreview(prefix) {
        const tipo = document.getElementById(prefix + '_tipo').value;
        const matricula = parseFloat(document.getElementById(prefix + '_matricula').value) || 0;
        const costoTotal = parseFloat(document.getElementById(prefix + '_costo_total_estudio').value) || 0;
        const modulo = parseFloat(document.getElementById(prefix + '_modulo').value) || 0;
        const modulosDip = parseInt(document.getElementById(prefix + '_modulos_dip').value) || 0;
        const modulosEsp = parseInt(document.getElementById(prefix + '_modulos_esp').value) || 0;
        const modulosMae = parseInt(document.getElementById(prefix + '_modulos_mae').value) || 0;
        const defensaDip = parseFloat(document.getElementById(prefix + '_defensa_dip').value) || 0;
        const defensaEsp = parseFloat(document.getElementById(prefix + '_defensa_esp').value) || 0;
        const defensaMae = parseFloat(document.getElementById(prefix + '_defensa_mae').value) || 0;

        if (!tipo || modulo <= 0 || modulosDip <= 0) {
            document.getElementById(prefix + '_preview').style.display = 'none';
            return;
        }

        let html = '';
        let total = matricula;

        html += `<div class="cuota-item"><span>Matrícula</span><span style="font-weight:600;">${matricula.toLocaleString('es-ES', {minimumFractionDigits: 2})} Bs</span></div>`;

        // Módulos diplomado
        for (let i = 1; i <= modulosDip; i++) {
            html += `<div class="cuota-item"><span>Módulo Diplomado ${i}</span><span style="font-weight:600;">${modulo.toLocaleString('es-ES', {minimumFractionDigits: 2})} Bs</span></span></div>`;
            total += modulo;
        }

        // Defensa diplomado
        if (defensaDip > 0) {
            html += `<div class="cuota-item" style="color:#fbbf24;"><span>Defensa Diplomado</span><span style="font-weight:600;">${defensaDip.toLocaleString('es-ES', {minimumFractionDigits: 2})} Bs</span></div>`;
            total += defensaDip;
        }

        // Módulos especialidad
        if ((tipo === 'Especialidad' || tipo === 'Maestría') && modulosEsp > 0) {
            for (let i = 1; i <= modulosEsp; i++) {
                html += `<div class="cuota-item"><span>Módulo Especialidad ${i}</span><span style="font-weight:600;">${modulo.toLocaleString('es-ES', {minimumFractionDigits: 2})} Bs</span></div>`;
                total += modulo;
            }
            if (defensaEsp > 0) {
                html += `<div class="cuota-item" style="color:#fbbf24;"><span>Defensa Especialidad</span><span style="font-weight:600;">${defensaEsp.toLocaleString('es-ES', {minimumFractionDigits: 2})} Bs</span></div>`;
                total += defensaEsp;
            }
        }

        // Módulos maestría
        if (tipo === 'Maestría' && modulosMae > 0) {
            for (let i = 1; i <= modulosMae; i++) {
                html += `<div class="cuota-item"><span>Módulo Maestría ${i}</span><span style="font-weight:600;">${modulo.toLocaleString('es-ES', {minimumFractionDigits: 2})} Bs</span></div>`;
                total += modulo;
            }
            if (defensaMae > 0) {
                html += `<div class="cuota-item" style="color:#fbbf24;"><span>Defensa Maestría</span><span style="font-weight:600;">${defensaMae.toLocaleString('es-ES', {minimumFractionDigits: 2})} Bs</span></div>`;
                total += defensaMae;
            }
        }

        document.getElementById(prefix + '_preview_content').innerHTML = html;
        document.getElementById(prefix + '_total').textContent = total.toLocaleString('es-ES', {minimumFractionDigits: 2}) + ' Bs';
        document.getElementById(prefix + '_preview').style.display = 'block';
    }

    function openEditModal(id) {
        const c = cursosData[id];
        if (!c) return;

        document.getElementById('editCursoForm').action = '/cursos/' + id;
        document.getElementById('edit_nombre').value = c.nombre;
        document.getElementById('edit_tipo').value = c.tipo;
        document.getElementById('edit_version').value = c.version;
        document.getElementById('edit_edicion').value = c.edicion;
        document.getElementById('edit_periodo').value = c.periodo;
        document.getElementById('edit_matricula').value = c.matricula;
        document.getElementById('edit_costo_total_estudio').value = c.costo_total_estudio;
        document.getElementById('edit_modulo').value = c.costo_modulo.toFixed(2);
        document.getElementById('edit_defensa_dip').value = c.defensa_dip;
        document.getElementById('edit_defensa_esp').value = c.defensa_esp;
        document.getElementById('edit_defensa_mae').value = c.defensa_mae;
        document.getElementById('edit_cupo').value = c.cupo;
        document.getElementById('edit_modulos_dip').value = c.modulos_dip;
        document.getElementById('edit_modulos_esp').value = c.modulos_esp;
        document.getElementById('edit_modulos_mae').value = c.modulos_mae;

        actualizarTipoForm('edit');
        actualizarPreview('edit');
        openModal('editModal');
    }

    function verDetalleCurso(id) {
        const c = cursosData[id];
        if (!c) return;

        const porcentaje = c.cupo > 0 ? Math.min(100, (c.inscritos / c.cupo) * 100) : 0;
        const barColor = porcentaje >= 90 ? '#ef4444' : (porcentaje >= 70 ? '#f59e0b' : '#22c55e');
        const disponibles = Math.max(0, c.cupo - c.inscritos);

        let modulosHtml = `
            <div style="font-size:14px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px; margin:20px 0 12px;">Estructura de Módulos</div>
            <div style="background:rgba(15,23,42,0.5); border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:16px;">
                <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid rgba(255,255,255,0.04); font-size:14px;">
                    <span style="color:#94a3b8;">Diplomado</span>
                    <span style="font-weight:600; color:#f1f5f9;">${c.modulos_dip} módulos</span>
                </div>
                ${c.modulos_esp > 0 ? `
                <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid rgba(255,255,255,0.04); font-size:14px;">
                    <span style="color:#94a3b8;">Especialidad</span>
                    <span style="font-weight:600; color:#f1f5f9;">${c.modulos_esp} módulos</span>
                </div>` : ''}
                ${c.modulos_mae > 0 ? `
                <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid rgba(255,255,255,0.04); font-size:14px;">
                    <span style="color:#94a3b8;">Maestría</span>
                    <span style="font-weight:600; color:#f1f5f9;">${c.modulos_mae} módulos</span>
                </div>` : ''}
                <div style="display:flex; justify-content:space-between; padding:8px 0; font-size:14px; border-top:1px solid rgba(255,255,255,0.1); margin-top:4px;">
                    <span style="color:#818cf8; font-weight:600;">Total Módulos</span>
                    <span style="font-weight:700; color:#818cf8;">${c.total_modulos}</span>
                </div>
            </div>
        `;

        let cuotasHtml = '';
        if (c.total_modulos > 0) {
            cuotasHtml = `
                <div style="font-size:14px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px; margin:20px 0 12px;">Plan de Pagos</div>
                <div style="background:rgba(15,23,42,0.5); border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:16px;">
                    <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid rgba(255,255,255,0.04); font-size:14px;">
                        <span style="color:#94a3b8;">Matrícula</span>
                        <span style="font-weight:600; color:#f1f5f9;">${c.matricula.toLocaleString('es-ES', {minimumFractionDigits: 2})} Bs</span>
                    </div>
            `;

            for (let i = 1; i <= c.total_modulos; i++) {
                let fase = 'Diplomado';
                let numFase = i;
                if (i > c.modulos_dip && i <= c.modulos_dip + c.modulos_esp) {
                    fase = 'Especialidad';
                    numFase = i - c.modulos_dip;
                } else if (i > c.modulos_dip + c.modulos_esp) {
                    fase = 'Maestría';
                    numFase = i - c.modulos_dip - c.modulos_esp;
                }

                cuotasHtml += `
                    <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid rgba(255,255,255,0.04); font-size:14px;">
                        <span style="color:#94a3b8;">${fase} - Cuota ${numFase}</span>
                        <span style="font-weight:600; color:#f1f5f9;">${c.costo_modulo.toLocaleString('es-ES', {minimumFractionDigits: 2})} Bs</span>
                    </div>
                `;

                if (i === c.modulos_dip && c.defensa_dip > 0) {
                    cuotasHtml += `
                        <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid rgba(255,255,255,0.04); font-size:14px;">
                            <span style="color:#fbbf24;">Defensa Diplomado</span>
                            <span style="font-weight:600; color:#fbbf24;">${c.defensa_dip.toLocaleString('es-ES', {minimumFractionDigits: 2})} Bs</span>
                        </div>
                    `;
                }
                if (i === c.modulos_dip + c.modulos_esp && c.defensa_esp > 0) {
                    cuotasHtml += `
                        <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid rgba(255,255,255,0.04); font-size:14px;">
                            <span style="color:#fbbf24;">Defensa Especialidad</span>
                            <span style="font-weight:600; color:#fbbf24;">${c.defensa_esp.toLocaleString('es-ES', {minimumFractionDigits: 2})} Bs</span>
                        </div>
                    `;
                }
                if (i === c.total_modulos && c.defensa_mae > 0) {
                    cuotasHtml += `
                        <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid rgba(255,255,255,0.04); font-size:14px;">
                            <span style="color:#fbbf24;">Defensa Maestría</span>
                            <span style="font-weight:600; color:#fbbf24;">${c.defensa_mae.toLocaleString('es-ES', {minimumFractionDigits: 2})} Bs</span>
                        </div>
                    `;
                }
            }
            cuotasHtml += '</div>';
        }

        let defensasHtml = '';
        let defensasItems = [];
        if (c.tipo === 'Diplomado' || c.tipo === 'Especialidad' || c.tipo === 'Maestría') {
            defensasItems.push({label: 'Defensa Diplomado', valor: c.defensa_dip});
        }
        if (c.tipo === 'Especialidad' || c.tipo === 'Maestría') {
            defensasItems.push({label: 'Defensa Especialidad', valor: c.defensa_esp});
        }
        if (c.tipo === 'Maestría') {
            defensasItems.push({label: 'Defensa Maestría', valor: c.defensa_mae});
        }

        if (defensasItems.length > 0) {
            defensasHtml = `
                <div style="font-size:14px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px; margin:20px 0 12px;">Defensas</div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
            `;
            defensasItems.forEach(d => {
                defensasHtml += `
                    <div style="background:rgba(15,23,42,0.5); border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:14px; text-align:center;">
                        <div style="font-size:11px; color:#64748b; margin-bottom:4px;">${d.label}</div>
                        <div style="font-size:18px; font-weight:700; color:#f1f5f9;">${d.valor.toLocaleString('es-ES', {minimumFractionDigits: 2})} <span style="font-size:12px; font-weight:400; color:#64748b;">Bs</span></div>
                    </div>
                `;
            });
            defensasHtml += '</div>';
        }

        document.getElementById('detalleContent').innerHTML = `
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:24px; padding-bottom:20px; border-bottom:1px solid rgba(255,255,255,0.06);">
                <div style="width:56px; height:56px; border-radius:16px; display:flex; align-items:center; justify-content:center; font-size:24px; color:${c.color}; background:${c.colorBg}; flex-shrink:0;">
                    <i class="fas ${c.icono}"></i>
                </div>
                <div>
                    <div style="font-size:20px; font-weight:700; color:#f8fafc;">${c.nombre}</div>
                    <span style="display:inline-block; margin-top:4px; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; background:${c.colorBg}; color:${c.color};">
                        ${c.tipo} — V${c.version}E${c.edicion}
                    </span>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">
                <div style="background:rgba(15,23,42,0.5); border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:14px 16px;">
                    <div style="font-size:11px; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px;">Período</div>
                    <div style="font-size:14px; color:#f1f5f9; font-weight:500;">${c.periodo}</div>
                </div>
                <div style="background:rgba(15,23,42,0.5); border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:14px 16px;">
                    <div style="font-size:11px; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px;">Fecha Creación</div>
                    <div style="font-size:14px; color:#f1f5f9; font-weight:500;">${c.fecha}</div>
                </div>
            </div>

            <div style="background:rgba(15,23,42,0.5); border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:16px; margin-bottom:20px;">
                <div style="font-size:11px; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px;">Cupos</div>
                <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:14px; color:#f1f5f9;">
                    <span>${c.inscritos} inscritos</span>
                    <span>${disponibles} disponibles</span>
                </div>
                <div style="width:100%; height:8px; background:rgba(15,23,42,0.8); border-radius:4px; overflow:hidden;">
                    <div style="width:${porcentaje}%; height:100%; background:${barColor}; border-radius:4px; transition:width 0.3s;"></div>
                </div>
                <div style="text-align:right; margin-top:6px; font-size:12px; color:#64748b;">${porcentaje.toFixed(0)}% ocupado</div>
            </div>

            <div style="font-size:14px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:16px;">Costos Base</div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
                <div style="background:rgba(15,23,42,0.5); border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:14px; text-align:center;">
                    <div style="font-size:11px; color:#64748b; margin-bottom:4px;">Matrícula</div>
                    <div style="font-size:18px; font-weight:700; color:#f1f5f9;">${c.matricula.toLocaleString('es-ES', {minimumFractionDigits: 2})} <span style="font-size:12px; font-weight:400; color:#64748b;">Bs</span></div>
                </div>
                <div style="background:rgba(15,23,42,0.5); border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:14px; text-align:center;">
                    <div style="font-size:11px; color:#64748b; margin-bottom:4px;">Costo Total Estudio</div>
                    <div style="font-size:18px; font-weight:700; color:#f1f5f9;">${c.costo_total_estudio.toLocaleString('es-ES', {minimumFractionDigits: 2})} <span style="font-size:12px; font-weight:400; color:#64748b;">Bs</span></div>
                </div>
                <div style="background:rgba(15,23,42,0.5); border:1px solid rgba(255,255,255,0.06); border-radius:12px; padding:14px; text-align:center;">
                    <div style="font-size:11px; color:#64748b; margin-bottom:4px;">Módulo Calculado</div>
                    <div style="font-size:18px; font-weight:700; color:#f1f5f9;">${c.costo_modulo.toLocaleString('es-ES', {minimumFractionDigits: 2})} <span style="font-size:12px; font-weight:400; color:#64748b;">Bs</span></div>
                </div>
            </div>

            ${defensasHtml}

            ${modulosHtml}

            ${cuotasHtml}

            <div style="background:linear-gradient(135deg, ${c.color}20 0%, ${c.color}10 100%); border:1px solid ${c.color || '#3b82f6'}; border-radius:16px; padding:20px; text-align:center; margin-top:20px; opacity:0.9;">
                <div style="font-size:12px; color:${c.color}; font-weight:600; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">Costo Total del Programa</div>
                <div style="font-size:32px; font-weight:800; color:#f8fafc;">${c.costo_total_programa.toLocaleString('es-ES', {minimumFractionDigits: 2})} <span style="font-size:16px; font-weight:400; color:#94a3b8;">Bs</span></div>
            </div>
        `;

        openModal('detalleModal');
    }

    document.getElementById('buscarCurso').addEventListener('input', function() {
        const termino = this.value.toLowerCase().trim();
        const cards = document.querySelectorAll('.curso-card');

        cards.forEach(card => {
            const nombre = card.dataset.nombre;
            const tipo = card.dataset.tipo;
            const periodo = card.dataset.periodo;

            if (nombre.includes(termino) || tipo.includes(termino) || periodo.includes(termino)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });

    let toggleFormToSubmit = null;

    document.querySelectorAll('.form-toggle').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button');
            const icon = btn.querySelector('i');
            const isActive = icon.classList.contains('fa-pause');
            const action = isActive ? 'desactivar' : 'activar';

            document.getElementById('toggleMessage').textContent = `¿Estás seguro de ${action} este curso?`;
            toggleFormToSubmit = this;
            openModal('toggleModal');
        });
    });

    document.getElementById('toggleConfirmBtn').addEventListener('click', function() {
        if (toggleFormToSubmit) {
            toggleFormToSubmit.submit();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal('createModal');
            closeModal('editModal');
            closeModal('detalleModal');
            closeModal('toggleModal');
        }
    });

    setTimeout(function() {
        const alert = document.getElementById('successAlert');
        if (alert) {
            alert.style.animation = 'fadeOut 0.5s ease forwards';
            setTimeout(() => alert.remove(), 500);
        }
    }, 4000);
</script>

@endsection

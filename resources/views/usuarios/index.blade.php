@extends('layouts.app')

@section('title', 'Gestión de Usuarios')
@section('page-title', 'Gestión de Usuarios')
@section('page-subtitle', 'Administra las cuentas de acceso para administradores y operadores')

@section('content')

<div style="display:flex; align-items:center; gap:16px; margin-bottom:24px; flex-wrap:wrap;">
    <button id="btnCreateUser" class="btn-primary">
        <i class="fas fa-plus"></i> Crear Usuario
    </button>

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
</style>

<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:20px;">
    @forelse($usuarios as $usuario)
        <div style="background:rgba(30,41,59,0.6); backdrop-filter:blur(20px); border:1px solid rgba(255,255,255,0.08); border-radius:20px; padding:24px; position:relative; overflow:hidden; transition:all 0.3s;">
            <div style="position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg, #3b82f6, #6366f1);"></div>

            <div style="position:absolute; top:20px; right:20px; width:10px; height:10px; border-radius:50%; box-shadow:0 0 8px currentColor; {{ $usuario->activo ? 'background:#22c55e; color:#22c55e;' : 'background:#ef4444; color:#ef4444;' }}"></div>

            <div style="display:flex; align-items:center; gap:14px; margin-bottom:20px;">
                <div style="width:48px; height:48px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:16px; color:#fff; flex-shrink:0; background:{{ $usuario->rol === 'administrador' ? 'linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%)' : 'linear-gradient(135deg, #22c55e 0%, #10b981 100%)' }};">
                    {{ strtoupper(substr($usuario->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $usuario->name)[1] ?? '', 0, 1)) }}
                </div>
                <div>
                    <div style="font-size:16px; font-weight:600; color:#f1f5f9;">{{ $usuario->name }}</div>
                    <span style="display:inline-block; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:500; {{ $usuario->rol === 'administrador' ? 'background:rgba(99,102,241,0.12); color:#818cf8;' : 'background:rgba(34,197,94,0.12); color:#4ade80;' }}">
                        {{ ucfirst($usuario->rol) }}
                    </span>
                </div>
            </div>

            <div style="border-top:1px solid rgba(255,255,255,0.06); padding-top:16px; margin-bottom:16px;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px; font-size:13px; color:#94a3b8;">
                    <i class="fas fa-envelope" style="width:16px; color:#64748b; font-size:12px;"></i>
                    <span style="color:#cbd5e1;">{{ $usuario->email }}</span>
                </div>
                <div style="display:flex; align-items:center; gap:10px; font-size:13px; color:#94a3b8;">
                    <i class="fas fa-calendar" style="width:16px; color:#64748b; font-size:12px;"></i>
                    <span style="color:#cbd5e1;">Creado: {{ $usuario->created_at->format('d/m/Y') }}</span>
                </div>
            </div>

            <div style="display:flex; gap:8px; border-top:1px solid rgba(255,255,255,0.06); padding-top:16px;">
                <button class="btn-edit"
                    data-id="{{ $usuario->id }}"
                    data-name="{{ $usuario->name }}"
                    data-email="{{ $usuario->email }}"
                    data-rol="{{ $usuario->rol }}"
                    data-activo="{{ $usuario->activo ? '1' : '0' }}">
                    <i class="fas fa-pen"></i> Editar
                </button>
                @if($usuario->id !== auth()->id())
                    <button class="btn-delete"
                        data-id="{{ $usuario->id }}"
                        data-name="{{ $usuario->name }}">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                @else
                    <button class="btn-delete" disabled>
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                @endif
            </div>
        </div>
    @empty
        <div style="text-align:center; padding:60px 20px; color:#475569; grid-column:1 / -1;">
            <i class="fas fa-users-slash" style="font-size:56px; margin-bottom:20px; display:block; color:#334155;"></i>
            <p>No hay usuarios registrados</p>
        </div>
    @endforelse
</div>

<div style="margin-top:24px;">
    {{ $usuarios->links() }}
</div>

<style>
    .btn-edit, .btn-delete {
        flex: 1; padding: 10px; border-radius: 10px;
        border: 1px solid rgba(255,255,255,0.08);
        background: rgba(15,23,42,0.4); color: #94a3b8;
        font-size: 13px; cursor: pointer; transition: all 0.3s;
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        font-family: 'Inter', sans-serif;
    }
    .btn-edit:hover { background: rgba(99,102,241,0.1); color: #818cf8; border-color: rgba(99,102,241,0.3); }
    .btn-delete:hover { background: rgba(239,68,68,0.1); color: #f87171; border-color: rgba(239,68,68,0.3); }
    .btn-delete:disabled { opacity: 0.4; cursor: not-allowed; color: #475569; }
    .btn-delete:disabled:hover { background: rgba(15,23,42,0.4); border-color: rgba(255,255,255,0.08); color: #475569; }
</style>

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
</style>

<!-- MODAL CREAR -->
<div id="createModal" class="modal-overlay">
    <div style="background:rgba(30,41,59,0.98); border:1px solid rgba(255,255,255,0.1); border-radius:24px; width:100%; max-width:560px; max-height:90vh; overflow-y:auto; box-shadow:0 25px 50px -12px rgba(0,0,0,0.8); position:relative;">
        <div style="padding:28px 32px 0; display:flex; justify-content:space-between; align-items:center;">
            <h2 style="font-size:22px; font-weight:700; color:#f8fafc; display:flex; align-items:center; gap:12px;">
                <i class="fas fa-user-plus" style="color:#6366f1; font-size:20px;"></i> Nuevo Usuario
            </h2>
            <button class="modal-close" data-modal="createModal" style="width:36px; height:36px; border-radius:10px; border:1px solid rgba(255,255,255,0.1); background:rgba(15,23,42,0.5); color:#94a3b8; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div style="padding:24px 32px 32px;">
            <form method="POST" action="{{ route('usuarios.store') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre Completo</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Ej: Juan Pérez" required>
                        @error('name')<<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Correo Electrónico</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Ej: juan@ejemplo.com" required>
                        @error('email')<<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Contraseña</label>
                        <input type="password" name="password" placeholder="Mínimo 6 caracteres" required>
                        @error('password')<<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Rol</label>
                        <select name="rol" required>
                            <option value="">Seleccionar rol...</option>
                            <option value="administrador" {{ old('rol') == 'administrador' ? 'selected' : '' }}>Administrador</option>
                            <option value="operador" {{ old('rol') == 'operador' ? 'selected' : '' }}>Operador</option>
                        </select>
                        @error('rol')<<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div style="margin-bottom:24px;">
                    <div class="toggle-group">
                        <div>
                            <label style="margin:0; padding:0; color:#cbd5e1; display:block; font-size:13px; font-weight:500;">Estado de la Cuenta</label>
                            <span style="font-size:12px; color:#64748b;">Activo / Inactivo</span>
                        </div>
                        <label class="switch">
                            <input type="checkbox" name="activo" value="1" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
                <div style="display:flex; gap:12px; justify-content:flex-end;">
                    <button type="button" class="modal-cancel" data-modal="createModal" style="padding:12px 24px; border-radius:12px; border:1px solid rgba(255,255,255,0.1); background:transparent; color:#94a3b8; font-size:14px; cursor:pointer; font-family:'Inter',sans-serif;">Cancelar</button>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR -->
<div id="editModal" class="modal-overlay">
    <div style="background:rgba(30,41,59,0.98); border:1px solid rgba(255,255,255,0.1); border-radius:24px; width:100%; max-width:560px; max-height:90vh; overflow-y:auto; box-shadow:0 25px 50px -12px rgba(0,0,0,0.8); position:relative;">
        <div style="padding:28px 32px 0; display:flex; justify-content:space-between; align-items:center;">
            <h2 style="font-size:22px; font-weight:700; color:#f8fafc; display:flex; align-items:center; gap:12px;">
                <i class="fas fa-user-edit" style="color:#6366f1; font-size:20px;"></i> Editar Usuario
            </h2>
            <button class="modal-close" data-modal="editModal" style="width:36px; height:36px; border-radius:10px; border:1px solid rgba(255,255,255,0.1); background:rgba(15,23,42,0.5); color:#94a3b8; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div style="padding:24px 32px 32px;">
            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre Completo</label>
                        <input type="text" id="edit_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Correo Electrónico</label>
                        <input type="email" id="edit_email" name="email" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Nueva Contraseña (opcional)</label>
                        <input type="password" name="password" placeholder="Dejar vacío para mantener">
                        @error('password')<<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Rol</label>
                        <select id="edit_rol" name="rol" required>
                            <option value="administrador">Administrador</option>
                            <option value="operador">Operador</option>
                        </select>
                    </div>
                </div>
                <div style="margin-bottom:24px;">
                    <div class="toggle-group">
                        <div>
                            <label style="margin:0; padding:0; color:#cbd5e1; display:block; font-size:13px; font-weight:500;">Estado de la Cuenta</label>
                            <span style="font-size:12px; color:#64748b;">Activo / Inactivo</span>
                        </div>
                        <label class="switch">
                            <input type="checkbox" id="edit_activo" name="activo" value="1">
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
                <div style="display:flex; gap:12px; justify-content:flex-end;">
                    <button type="button" class="modal-cancel" data-modal="editModal" style="padding:12px 24px; border-radius:12px; border:1px solid rgba(255,255,255,0.1); background:transparent; color:#94a3b8; font-size:14px; cursor:pointer; font-family:'Inter',sans-serif;">Cancelar</button>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL ELIMINAR -->
<div id="deleteModal" class="modal-overlay">
    <div style="background:rgba(30,41,59,0.98); border:1px solid rgba(255,255,255,0.1); border-radius:24px; width:100%; max-width:420px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.8); text-align:center; position:relative;">
        <div style="padding:32px;">
            <div style="width:64px; height:64px; border-radius:50%; background:rgba(239,68,68,0.1); display:flex; align-items:center; justify-content:center; margin:0 auto 20px;">
                <i class="fas fa-exclamation-triangle" style="font-size:28px; color:#f87171;"></i>
            </div>
            <h3 style="font-size:20px; font-weight:700; color:#f8fafc; margin-bottom:8px;">¿Eliminar usuario?</h3>
            <p style="color:#94a3b8; font-size:14px; margin-bottom:24px;">Estás por eliminar a <strong id="delete_name" style="color:#f1f5f9;"></strong>. Esta acción no se puede deshacer.</p>
            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div style="display:flex; gap:12px; justify-content:center;">
                    <button type="button" class="modal-cancel" data-modal="deleteModal" style="padding:12px 24px; border-radius:12px; border:1px solid rgba(255,255,255,0.1); background:transparent; color:#94a3b8; font-size:14px; cursor:pointer; font-family:'Inter',sans-serif;">Cancelar</button>
                    <button type="submit" style="padding:12px 24px; border-radius:12px; border:none; background:#ef4444; color:#fff; font-size:14px; font-weight:600; cursor:pointer; font-family:'Inter',sans-serif; display:inline-flex; align-items:center; gap:8px;">
                        <i class="fas fa-trash"></i> Sí, Eliminar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(modalId) {
        document.querySelectorAll('.modal-overlay.active').forEach(m => m.classList.remove('active'));
        document.getElementById(modalId).classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
        document.body.style.overflow = '';
    }

    // Create user button
    document.getElementById('btnCreateUser').addEventListener('click', function() {
        openModal('createModal');
    });

    // Modal overlay click to close
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });

    // Modal close buttons
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', function() {
            closeModal(this.dataset.modal);
        });
    });

    // Modal cancel buttons
    document.querySelectorAll('.modal-cancel').forEach(btn => {
        btn.addEventListener('click', function() {
            closeModal(this.dataset.modal);
        });
    });

    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const email = this.dataset.email;
            const rol = this.dataset.rol;
            const activo = this.dataset.activo === '1';

            document.getElementById('editForm').action = '/usuarios/' + id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_rol').value = rol;
            document.getElementById('edit_activo').checked = activo;
            openModal('editModal');
        });
    });

    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.disabled) return;
            const id = this.dataset.id;
            const name = this.dataset.name;
            document.getElementById('deleteForm').action = '/usuarios/' + id;
            document.getElementById('delete_name').textContent = name;
            openModal('deleteModal');
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal('createModal');
            closeModal('editModal');
            closeModal('deleteModal');
        }
    });

    // Auto-ocultar alertas después de 4 segundos
    setTimeout(function() {
        const successAlert = document.getElementById('successAlert');
        const errorAlert = document.getElementById('errorAlert');
        if (successAlert) {
            if (successAlert.style) {
                successAlert.style.animation = 'fadeOut 0.5s ease forwards';
            }
            setTimeout(() => successAlert.remove(), 500);
        }
        if (errorAlert) {
            if (errorAlert.style) {
                errorAlert.style.animation = 'fadeOut 0.5s ease forwards';
            }
            setTimeout(() => errorAlert.remove(), 500);
        }
    }, 4000);
</script>

@endsection

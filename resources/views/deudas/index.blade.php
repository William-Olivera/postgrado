@extends('layouts.app')

@section('page-title', 'Deudas y Saldos')
@section('page-subtitle', 'Control de deudas de estudiantes')

@section('content')

<div class="deudas-header">
    <div class="buscador-deudas">
        <i class="fas fa-search"></i>
        <input type="text" id="deudaSearch" placeholder="Buscar por nombre, CI o registro..." autocomplete="off">
    </div>
</div>

<div class="stats-deudas">
    <div class="stat-deuda stat-deuda-total">
        <div class="stat-deuda-label">Deuda Total</div>
        <div class="stat-deuda-value">Bs {{ number_format($deudaTotal, 2) }}</div>
    </div>
    <div class="stat-deuda stat-deuda-count">
        <div class="stat-deuda-label">Estudiantes con Deuda</div>
        <div class="stat-deuda-value">{{ $estudiantesConDeuda }}</div>
    </div>
</div>

<div class="grid-estudiantes" id="gridEstudiantes">
    @forelse($estudiantesData as $item)
        @php
            $est = $item['estudiante'];
            $colorProgreso = $item['progreso'] >= 80 ? '#22c55e' : ($item['progreso'] >= 40 ? '#818cf8' : '#f87171');
        @endphp
        <div class="estudiante-card"
             data-id="{{ $est->id }}"
             data-search="{{ strtolower($est->nombre_completo . ' ' . $est->cedula . ' ' . $est->registro) }}"
             onclick="abrirDetalle({{ $est->id }})">
            <div class="card-top">
                <div class="card-avatar">{{ $est->iniciales }}</div>
                <div class="card-info">
                    <div class="card-nombre">{{ $est->paterno }} {{ $est->materno }} {{ $est->nombres }}</div>
                    <div class="card-datos">CI: {{ $est->cedula }} · Reg: {{ $est->registro }}</div>
                </div>
                <div class="card-progreso-ring" style="--pct: {{ $item['progreso'] }}; --color: {{ $colorProgreso }};">
                    <span>{{ $item['progreso'] }}%</span>
                </div>
            </div>
            <div class="card-montos">
                <div class="monto-item monto-pagado">
                    <span class="monto-label">Pagado</span>
                    <span class="monto-valor">Bs {{ number_format($item['pagado'], 2) }}</span>
                </div>
                <div class="monto-item monto-pendiente">
                    <span class="monto-label">Pendiente</span>
                    <span class="monto-valor">Bs {{ number_format($item['pendiente'], 2) }}</span>
                </div>
            </div>
            <div class="card-footer">
                <span>Ver detalle de pagos <i class="fas fa-chevron-right"></i></span>
            </div>
        </div>
    @empty
        <div class="empty-deudas">
            <i class="fas fa-users"></i>
            <p>No hay estudiantes inscritos</p>
        </div>
    @endforelse
</div>

<div class="empty-search" id="emptySearch" style="display:none;">
    <i class="fas fa-search"></i>
    <p>No se encontraron estudiantes</p>
</div>

<style>
    .deudas-header { margin-bottom: 24px; }
    .buscador-deudas { position: relative; max-width: 480px; }
    .buscador-deudas i {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: #64748b; font-size: 14px;
    }
    .buscador-deudas input {
        width: 100%; padding: 13px 16px 13px 42px;
        background: rgba(15,23,42,0.5); border: 1px solid rgba(255,255,255,0.08);
        border-radius: 14px; color: #f1f5f9; font-size: 14px;
        font-family: 'Inter', sans-serif; outline: none;
    }
    .buscador-deudas input:focus { border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99,102,241,0.1); }
    .stats-deudas { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 28px; }
    .stat-deuda {
        padding: 24px; border-radius: 16px;
        background: rgba(15,23,42,0.5); border: 1px solid rgba(255,255,255,0.06);
    }
    .stat-deuda-label { font-size: 13px; color: #64748b; margin-bottom: 8px; }
    .stat-deuda-value { font-size: 32px; font-weight: 700; }
    .stat-deuda-total .stat-deuda-value { color: #f87171; }
    .stat-deuda-count .stat-deuda-value { color: #60a5fa; }
    .grid-estudiantes {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 20px;
    }
    .estudiante-card {
        background: rgba(15,23,42,0.5); border: 1px solid rgba(255,255,255,0.06);
        border-radius: 16px; overflow: hidden; cursor: pointer;
        transition: all 0.3s;
    }
    .estudiante-card:hover {
        transform: translateY(-3px);
        border-color: rgba(99,102,241,0.3);
        box-shadow: 0 12px 30px rgba(0,0,0,0.3);
    }
    .card-top { display: flex; align-items: center; gap: 14px; padding: 20px; }
    .card-avatar {
        width: 48px; height: 48px; border-radius: 50%; flex-shrink: 0;
        background: linear-gradient(135deg, #6366f1, #818cf8);
        color: #fff; font-weight: 700; font-size: 16px;
        display: flex; align-items: center; justify-content: center;
    }
    .card-info { flex: 1; min-width: 0; }
    .card-nombre { font-size: 15px; font-weight: 600; color: #f8fafc; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .card-datos { font-size: 12px; color: #64748b; margin-top: 2px; }
    .card-progreso-ring {
        width: 52px; height: 52px; border-radius: 50%; flex-shrink: 0;
        background: conic-gradient(var(--color) calc(var(--pct) * 1%), rgba(255,255,255,0.06) 0);
        display: flex; align-items: center; justify-content: center;
        position: relative;
    }
    .card-progreso-ring::before {
        content: ''; position: absolute; inset: 6px; border-radius: 50%;
        background: rgba(15,23,42,0.9);
    }
    .card-progreso-ring span {
        position: relative; font-size: 11px; font-weight: 700; color: #e2e8f0;
    }
    .card-montos {
        display: grid; grid-template-columns: 1fr 1fr; gap: 1px;
        background: rgba(255,255,255,0.04); border-top: 1px solid rgba(255,255,255,0.04);
    }
    .monto-item { padding: 14px 20px; background: rgba(15,23,42,0.3); }
    .monto-label { display: block; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; margin-bottom: 4px; }
    .monto-pagado .monto-valor { color: #60a5fa; font-weight: 700; font-size: 15px; }
    .monto-pendiente .monto-valor { color: #f87171; font-weight: 700; font-size: 15px; }
    .card-footer {
        padding: 12px 20px; border-top: 1px solid rgba(255,255,255,0.04);
        font-size: 12px; color: #818cf8; font-weight: 500;
    }
    .card-footer i { font-size: 10px; margin-left: 4px; }
    .empty-deudas, .empty-search {
        grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: #475569;
    }
    .empty-deudas i, .empty-search i { font-size: 48px; display: block; margin-bottom: 16px; color: #334155; }

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
        background: #111827;
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 32px;
        width: 100%;
        max-width: 900px;
        max-height: 92vh;
        overflow-y: auto;
        box-shadow: 0 40px 80px -15px rgba(0,0,0,0.9);
        position: relative;
    }

    .curso-block {
        margin-bottom: 20px;
        border-radius: 30px;
        overflow: hidden;
        background: rgba(30,41,59,0.2);
        transition: all 0.3s ease;
    }
    .curso-block-header {
        padding: 24px 32px;
        background: linear-gradient(135deg, #2e1065 0%, #1e1b4b 100%);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-radius: 30px;
        transition: all 0.3s ease;
    }
    .curso-block-header:hover {
        filter: brightness(1.2);
    }
    .curso-block-header.expanded {
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }

    .pago-item {
        border-bottom: 1px solid rgba(255,255,255,0.03);
    }
    .pago-item:last-child { border-bottom: none; }

    .modal-est-avatar {
        width: 64px; height: 64px; border-radius: 22px;
        background: linear-gradient(135deg, #6366f1, #a855f7);
        color: #fff; display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 26px;
        box-shadow: 0 0 25px rgba(99,102,241,0.5);
    }

    .btn-close-modal {
        width: 36px; height: 36px; border-radius: 10px;
        border: 1px solid rgba(255,255,255,0.1);
        background: rgba(31, 41, 55, 0.8);
        color: #94a3b8; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s;
    }
    .btn-close-modal:hover {
        background: #ef4444; color: #fff; border-color: #ef4444;
    }

    .pago-item-trigger {
        padding: 16px 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: background 0.2s;
    }
    .pago-item-trigger:hover { background: rgba(255,255,255,0.02); }

    .pago-detalles {
        display: none;
        padding: 20px 32px;
        background: rgba(15,23,42,0.5);
        border-top: 1px solid rgba(255,255,255,0.03);
    }
    .pago-detalles.active { display: block; }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 12px;
    }
    .info-box {
        padding: 12px 16px;
        background: rgba(30,41,59,0.3);
        border-radius: 14px;
        border: 1px solid rgba(255,255,255,0.03);
    }
    .info-label { font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
    .info-value { font-size: 13px; color: #f1f5f9; font-weight: 600; }

    .badge-estado {
        padding: 4px 12px; border-radius: 20px; font-size: 10px; font-weight: 700; letter-spacing: 0.5px;
    }
    .badge-pagado { background: rgba(34,197,94,0.15); color: #4ade80; }
    .badge-pendiente { background: rgba(245,158,11,0.15); color: #fbbf24; }
    .badge-parcial { background: rgba(59,130,246,0.15); color: #60a5fa; }

    /* Action buttons styling */
    .action-buttons {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: 1px solid rgba(255,255,255,0.1);
        background: rgba(30,41,59,0.6);
        color: #94a3b8;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        position: relative;
        text-decoration: none;
    }
    .action-btn:hover {
        background: rgba(99,102,241,0.2);
        border-color: rgba(99,102,241,0.4);
        color: #818cf8;
        transform: translateY(-2px);
    }
    .action-btn.delete:hover {
        background: rgba(239,68,68,0.2);
        border-color: rgba(239,68,68,0.4);
        color: #f87171;
    }
    .action-btn.add:hover {
        background: rgba(34,197,94,0.2);
        border-color: rgba(34,197,94,0.4);
        color: #4ade80;
    }
    .action-btn i {
        font-size: 14px;
    }
    .action-btn .tooltip {
        position: absolute;
        bottom: calc(100% + 8px);
        left: 50%;
        transform: translateX(-50%);
        background: #1e293b;
        color: #f1f5f9;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: all 0.2s;
        border: 1px solid rgba(255,255,255,0.1);
        z-index: 10;
    }
    .action-btn:hover .tooltip {
        opacity: 1;
        visibility: visible;
    }
    .action-btn .tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 6px solid transparent;
        border-top-color: #1e293b;
    }

    /* Scrollbar personalizado para el modal */
    .modal-content::-webkit-scrollbar { width: 6px; }
    .modal-content::-webkit-scrollbar-track { background: transparent; }
    .modal-content::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    .modal-content::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
</style>

@endsection

@section('modals')
<div id="deudasSaldosModal" class="modal-overlay" onclick="if(event.target === this) cerrarDetalle()">
    <div class="modal-content">
        <!-- HEADER DEL MODAL SEGÚN DISEÑO -->
        <div style="padding:40px 48px; display:flex; justify-content:space-between; align-items:flex-start;">
            <div style="display:flex; align-items:center; gap:24px;">
                <div id="modalEstAvatar" class="modal-est-avatar"></div>
                <div>
                    <h2 id="modalEstNombre" style="font-size:28px; font-weight:800; color:#fff; letter-spacing:-0.5px;"></h2>
                    <p id="modalEstDatos" style="font-size:15px; color:#64748b; margin-top:4px; font-weight:500;"></p>
                </div>
            </div>
            <button onclick="cerrarDetalle()" class="btn-close-modal">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- CONTENEDOR DE CURSOS -->
        <div style="padding:0 48px 48px;" id="modalCursosContainer">
            <div style="text-align:center; padding:60px 0;">
                <i class="fas fa-spinner fa-spin" style="font-size:32px; color:#6366f1; margin-bottom:16px;"></i>
                <p style="color:#64748b;">Cargando historial...</p>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA SUBIR ARCHIVO -->
<div id="subirArchivoModal" class="modal-overlay" onclick="if(event.target === this) cerrarModalSubir()">
    <div class="modal-content" style="max-width:500px;">
        <div style="padding:32px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                <h3 style="font-size:20px; font-weight:700; color:#fff;">Adjuntar Archivo</h3>
                <button onclick="cerrarModalSubir()" class="btn-close-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="formSubirArchivo" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="pagoIdSubir" name="pago_id">

                <div style="margin-bottom:20px;">
                    <label style="display:block; font-size:13px; color:#94a3b8; margin-bottom:8px;">Seleccionar archivo (PDF, JPG, JPEG, PNG)</label>
                    <input type="file" id="archivoSubir" name="archivo_adjunto" accept=".pdf,.jpg,.jpeg,.png" required
                           style="width:100%; padding:12px; background:rgba(15,23,42,0.6); border:1px solid rgba(255,255,255,0.08); border-radius:12px; color:#f1f5f9; font-size:14px;">
                </div>

                <div id="subirError" style="display:none; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2); color:#fca5a5; padding:12px; border-radius:12px; font-size:13px; margin-bottom:16px;"></div>

                <div style="display:flex; gap:12px;">
                    <button type="button" onclick="cerrarModalSubir()" style="flex:1; padding:12px; background:rgba(51,65,85,0.4); border:1px solid rgba(255,255,255,0.1); border-radius:12px; color:#94a3b8; font-weight:600; cursor:pointer;">
                        Cancelar
                    </button>
                    <button type="submit" id="btnSubir" style="flex:1; padding:12px; background:#3b82f6; border:none; border-radius:12px; color:#fff; font-weight:600; cursor:pointer;">
                        Subir Archivo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL DE CONFIRMACIÓN PARA ELIMINAR -->
<div id="confirmarEliminarModal" class="modal-overlay" onclick="if(event.target === this) cerrarModalEliminar()">
    <div class="modal-content" style="max-width:400px;">
        <div style="padding:32px; text-align:center;">
            <div style="width:64px; height:64px; background:rgba(239,68,68,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px;">
                <i class="fas fa-trash" style="font-size:24px; color:#ef4444;"></i>
            </div>
            <h3 style="font-size:20px; font-weight:700; color:#fff; margin-bottom:12px;">¿Eliminar archivo?</h3>
            <p style="font-size:14px; color:#94a3b8; margin-bottom:24px;">Esta acción no se puede deshacer.</p>
            <div style="display:flex; gap:12px;">
                <button onclick="cerrarModalEliminar()" style="flex:1; padding:12px; background:rgba(51,65,85,0.4); border:1px solid rgba(255,255,255,0.1); border-radius:12px; color:#94a3b8; font-weight:600; cursor:pointer;">
                    Cancelar
                </button>
                <button id="btnConfirmarEliminar" style="flex:1; padding:12px; background:#ef4444; border:none; border-radius:12px; color:#fff; font-weight:600; cursor:pointer;">
                    Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- NOTIFICACIÓN TOAST -->
<div id="toast" style="position:fixed; bottom:24px; right:24px; background:#1e293b; border:1px solid rgba(255,255,255,0.1); border-radius:12px; padding:16px 20px; display:flex; align-items:center; gap:12px; transform:translateY(100px); opacity:0; transition:all 0.3s ease; z-index:10000;">
    <i id="toastIcon" class="fas fa-check-circle" style="font-size:20px; color:#4ade80;"></i>
    <span id="toastMessage" style="color:#f1f5f9; font-size:14px; font-weight:500;"></span>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('deudaSearch').addEventListener('input', function() {
        const q = this.value.trim().toLowerCase();
        const cards = document.querySelectorAll('.estudiante-card');
        let visible = 0;
        cards.forEach(card => {
            const match = !q || card.dataset.search.includes(q);
            card.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        document.getElementById('emptySearch').style.display = (visible === 0 && cards.length > 0) ? 'block' : 'none';
    });

    function abrirDetalle(id) {
        const modal = document.getElementById('deudasSaldosModal');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';

        const container = document.getElementById('modalCursosContainer');
        container.innerHTML = `
            <div style="text-align:center; padding:40px 0;">
                <i class="fas fa-spinner fa-spin" style="font-size:32px; color:#6366f1; margin-bottom:16px;"></i>
                <p style="color:#64748b;">Cargando historial académico y financiero...</p>
            </div>`;

        fetch(`{{ url('deudas') }}/${id}`)
            .then(r => r.json())
            .then(data => {
                console.log('Datos recibidos:', data);
                const est = data.estudiante;
                document.getElementById('modalEstNombre').textContent = est.nombre_completo;
                document.getElementById('modalEstDatos').textContent = `CI: ${est.cedula} · Registro: ${est.registro}`;
                const iniciales = (est.nombre_completo.split(' ')[0][0] + (est.paterno ? est.paterno[0] : '')).toUpperCase();
                document.getElementById('modalEstAvatar').textContent = iniciales;

                const html = data.cursos.map((curso, idx) => `
                    <div class="curso-block">
                        <div class="curso-block-header" onclick="toggleCurso(${idx})" id="header-curso-${idx}">
                            <div>
                                <h4 style="color:#fff; font-size:18px; font-weight:800; letter-spacing:-0.2px;">${curso.curso}</h4>
                                <p style="color:rgba(255,255,255,0.5); font-size:13px; margin-top:4px; font-weight:500;">
                                    ${curso.tipo} · ${curso.periodo}
                                </p>
                            </div>
                            <div style="display:flex; align-items:center; gap:32px;">
                                <div style="text-align:right;">
                                    <div style="color:#fbbf24; font-size:20px; font-weight:800;">Bs ${curso.saldo_pendiente.toLocaleString('es-BO', {minimumFractionDigits: 2})}</div>
                                    <div style="color:rgba(255,255,255,0.4); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; margin-top:2px;">Saldo Pendiente</div>
                                </div>
                                <i class="fas fa-chevron-down arrow-curso" id="arrow-curso-${idx}" style="color:rgba(255,255,255,0.4); font-size:18px; transition: transform 0.3s;"></i>
                            </div>
                        </div>
                        <div class="pago-items-container" id="curso-content-${idx}" style="display: none;">
                            <div style="padding: 10px 0;">
                                ${curso.cuotas.map(c => `
                                    <div class="pago-item">
                                        <div class="pago-item-trigger" onclick="this.nextElementSibling.classList.toggle('active')">
                                            <div style="display:flex; align-items:center; gap:16px;">
                                                <div style="width:40px; height:40px; border-radius:12px; background:${c.saldo <= 0 ? 'rgba(34,197,94,0.1)' : 'rgba(245,158,11,0.1)'}; color:${c.saldo <= 0 ? '#4ade80' : '#fbbf24'}; display:flex; align-items:center; justify-content:center; font-size:16px;">
                                                    <i class="fas ${c.concepto === 'Matricula' ? 'fa-university' : (c.concepto.includes('Deposito') ? 'fa-wallet' : 'fa-graduation-cap')}"></i>
                                                </div>
                                                <div>
                                                    <div style="font-weight:700; color:#f1f5f9; font-size:14px;">${c.concepto} ${c.fase ? '<span style="color:#64748b; font-weight:500; font-size:12px; margin-left:4px;">('+c.fase+')</span>' : ''}</div>
                                                    <div style="font-size:12px; color:#64748b; margin-top:2px;">Programado: Bs ${c.monto_programado.toFixed(2)}</div>
                                                </div>
                                            </div>
                                            <div style="display:flex; align-items:center; gap:20px;">
                                                <div class="badge-estado ${c.estado === 'Pagado' ? 'badge-pagado' : (c.estado === 'Parcial' ? 'badge-parcial' : 'badge-pendiente')}">
                                                    ${c.estado.toUpperCase()}
                                                </div>
                                                <i class="fas fa-chevron-down" style="color:#475569; font-size:12px;"></i>
                                            </div>
                                        </div>
                                        <div class="pago-detalles">
                                            ${c.pagos && c.pagos.length > 0 ? `
                                                <div class="info-grid">
                                                    <div class="info-box">
                                                        <div class="info-label">Fecha</div>
                                                        <div class="info-value">${new Date(c.pagos[0].fecha_pago + 'T12:00:00').toLocaleDateString('es-BO')}</div>
                                                    </div>
                                                    <div class="info-box">
                                                        <div class="info-label">Comprobante</div>
                                                        <div class="info-value">${c.pagos[0].nro_comprobante}</div>
                                                    </div>
                                                    <div class="info-box">
                                                        <div class="info-label">Monto</div>
                                                        <div class="info-value">Bs ${c.pagos[0].monto.toFixed(2)}</div>
                                                    </div>
                                                    <div class="info-box" style="display:flex; align-items:center; justify-content:center;">
                                                        <div class="action-buttons" style="gap:8px;">
                                                            ${c.pagos[0].archivo_adjunto ? `
                                                                <a href="/storage/${c.pagos[0].archivo_adjunto}" target="_blank" class="action-btn">
                                                                    <i class="fas fa-eye"></i>
                                                                    <span class="tooltip">Ver</span>
                                                                </a>
                                                                <a href="/storage/${c.pagos[0].archivo_adjunto}" download class="action-btn">
                                                                    <i class="fas fa-download"></i>
                                                                    <span class="tooltip">Descargar</span>
                                                                </a>
                                                            ` : `
                                                                <button onclick="añadirArchivo(${c.pagos[0].id})" class="action-btn add">
                                                                    <i class="fas fa-plus"></i>
                                                                    <span class="tooltip">Añadir</span>
                                                                </button>
                                                                <button class="action-btn" style="opacity:0.3; cursor:not-allowed;" disabled>
                                                                    <i class="fas fa-download"></i>
                                                                    <span class="tooltip">Descargar</span>
                                                                </button>
                                                            `}
                                                            <button onclick="eliminarArchivo(${c.pagos[0].id})" class="action-btn delete">
                                                                <i class="fas fa-trash"></i>
                                                                <span class="tooltip">Eliminar</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            ` : `
                                                <div style="text-align:center; padding:12px; background:rgba(255,255,255,0.02); border-radius:12px; color:#64748b; font-size:12px;">
                                                    No hay pagos registrados
                                                </div>
                                            `}
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>`).join('');

                container.innerHTML = html || '<p style="color:#64748b;text-align:center;">Sin cursos registrados</p>';
            });
    }

    function cerrarDetalle() {
        document.getElementById('deudasSaldosModal').classList.remove('active');
        document.body.style.overflow = '';
    }

    function toggleCurso(idx) {
        const content = document.getElementById(`curso-content-${idx}`);
        const header = document.getElementById(`header-curso-${idx}`);
        const arrow = document.getElementById(`arrow-curso-${idx}`);
        const isHidden = content.style.display === 'none';

        content.style.display = isHidden ? 'block' : 'none';
        arrow.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';

        if (isHidden) {
            header.classList.add('expanded');
        } else {
            header.classList.remove('expanded');
        }
    }

    document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarDetalle(); });

    let pagoIdEliminar = null;

    function eliminarArchivo(pagoId) {
        pagoIdEliminar = pagoId;
        document.getElementById('confirmarEliminarModal').classList.add('active');
    }

    function cerrarModalEliminar() {
        document.getElementById('confirmarEliminarModal').classList.remove('active');
        pagoIdEliminar = null;
    }

    document.getElementById('btnConfirmarEliminar').addEventListener('click', function() {
        if (!pagoIdEliminar) return;

        fetch(`{{ url('pagos/eliminar-archivo') }}/${pagoIdEliminar}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                mostrarNotificacion('Archivo eliminado correctamente', 'success');
                cerrarModalEliminar();
                const estId = document.querySelector('.estudiante-card[onclick*="abrirDetalle"]')?.dataset.id;
                if (estId) abrirDetalle(parseInt(estId));
            } else {
                mostrarNotificacion('Error al eliminar el archivo: ' + (data.message || 'Error desconocido'), 'error');
            }
        })
        .catch(err => {
            mostrarNotificacion('Error al eliminar el archivo', 'error');
            console.error(err);
        });
    });

    function añadirArchivo(pagoId) {
        document.getElementById('pagoIdSubir').value = pagoId;
        document.getElementById('archivoSubir').value = '';
        document.getElementById('subirError').style.display = 'none';
        document.getElementById('subirArchivoModal').classList.add('active');
    }

    function cerrarModalSubir() {
        document.getElementById('subirArchivoModal').classList.remove('active');
    }

    document.getElementById('formSubirArchivo').addEventListener('submit', function(e) {
        e.preventDefault();
        const pagoId = document.getElementById('pagoIdSubir').value;
        const archivo = document.getElementById('archivoSubir').files[0];

        if (!archivo) {
            document.getElementById('subirError').textContent = 'Por favor seleccione un archivo';
            document.getElementById('subirError').style.display = 'block';
            return;
        }

        const formData = new FormData();
        formData.append('archivo_adjunto', archivo);

        const btn = document.getElementById('btnSubir');
        btn.disabled = true;
        btn.textContent = 'Subiendo...';

        fetch(`{{ url('pagos/subir-archivo') }}/${pagoId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                mostrarNotificacion('Archivo subido correctamente', 'success');
                cerrarModalSubir();
                const estId = document.querySelector('.estudiante-card[onclick*="abrirDetalle"]')?.dataset.id;
                if (estId) abrirDetalle(parseInt(estId));
            } else {
                document.getElementById('subirError').textContent = data.message || 'Error al subir el archivo';
                document.getElementById('subirError').style.display = 'block';
            }
        })
        .catch(err => {
            document.getElementById('subirError').textContent = 'Error al subir el archivo';
            document.getElementById('subirError').style.display = 'block';
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Subir Archivo';
        });
    });

    function mostrarNotificacion(mensaje, tipo = 'success') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        const toastIcon = document.getElementById('toastIcon');

        toastMessage.textContent = mensaje;

        if (tipo === 'success') {
            toastIcon.className = 'fas fa-check-circle';
            toastIcon.style.color = '#4ade80';
        } else {
            toastIcon.className = 'fas fa-exclamation-circle';
            toastIcon.style.color = '#f87171';
        }

        toast.style.transform = 'translateY(0)';
        toast.style.opacity = '1';

        setTimeout(() => {
            toast.style.transform = 'translateY(100px)';
            toast.style.opacity = '0';
        }, 3000);
    }

</script>
@endpush

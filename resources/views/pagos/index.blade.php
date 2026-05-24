@extends('layouts.app')

@section('page-title', 'Pagos')
@section('page-subtitle', 'Registro de transacciones y cobros de matrícula')

@section('content')

@if(session('success'))
    <div class="alert alert-success" id="successMessage" style="position:fixed; top:80px; left:50%; transform:translateX(-50%); z-index:1000; display:flex; align-items:center; gap:10px; margin:0; animation:slideIn 0.3s ease;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
@endif

<div class="pago-layout">
    <div class="pago-form-card">
        <div class="pago-form-header">
            <div class="pago-form-icon"><i class="fas fa-cash-register"></i></div>
            <div>
                <h2>Registrar Nueva Transacción</h2>
                <p>Complete los datos para registrar el pago</p>
            </div>
        </div>

        <form method="POST" action="{{ route('pagos.store') }}" id="formPago">
            @csrf
            <input type="hidden" name="detalle_plan_pago_id" id="detalle_plan_pago_id" value="{{ old('detalle_plan_pago_id') }}">

            <div class="form-section">
                <label class="section-label"><i class="fas fa-search"></i> Buscar Estudiante</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" id="buscarEstudiante" placeholder="Nombre o código (Ej: MAT-2024-001)..." autocomplete="off">
                </div>
                <div id="resultadosEstudiante" class="search-results"></div>
            </div>

            <div class="form-section">
                <label class="section-label"><i class="fas fa-graduation-cap"></i> Programa Asignado</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-university"></i>
                    <select id="programaAsignado" class="select-programa" disabled>
                        <option value="">Seleccione estudiante primero...</option>
                    </select>
                </div>
            </div>

            <div class="form-section">
                <label class="section-label" style="color:#f59e0b;"><i class="fas fa-receipt"></i> DETALLE DEL COBRO</label>

                <div style="display:flex; gap:16px; margin-bottom:16px;">
                    <div class="form-field" style="flex:1;">
                        <label>Fase / Concepto</label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-tag"></i>
                            <select name="glosa" id="tipoPago" disabled>
                                <option value="">Seleccionar...</option>
                                <option value="Matricula">Matrícula</option>
                                <option value="Deposito">Módulo</option>
                                <option value="Defensa Diplomado">Defensa de Diplomado</option>
                                <option value="Defensa Especialidad">Defensa de Especialidad</option>
                                <option value="Defensa Maestria">Defensa de Maestría</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-field" id="wrapNroModulo" style="width:80px; display:none;">
                        <label>Nº</label>
                        <div class="input-icon-wrap no-icon">
                            <input type="number" id="nroModulo" placeholder="Nº" min="1" step="1">
                        </div>
                    </div>
                </div>

                <div class="form-field">
                    <label>Nº de Comprobante</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-file-invoice"></i>
                        <input type="text" name="nro_comprobante" id="nroComprobante" placeholder="Ej. 21649751" value="{{ old('nro_comprobante') }}" disabled>
                    </div>
                </div>

                <div class="form-field">
                    <label>Monto a Cobrar (Bs)</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-coins"></i>
                        <input type="number" name="monto" id="montoPago" step="0.01" min="0.01" placeholder="0.00" value="{{ old('monto') }}" readonly>
                    </div>
                    <small class="saldo-hint" id="saldoHint"></small>
                </div>

                <div class="form-field">
                    <label>Fecha de Pago</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-calendar"></i>
                        <input type="date" name="fecha_pago" id="fechaPago" value="{{ old('fecha_pago', date('Y-m-d')) }}" disabled>
                    </div>
                </div>

                <div class="form-field">
                    <label>Observación (Opcional)</label>
                    <div class="input-icon-wrap no-icon">
                        <textarea name="observacion" id="observacion" rows="2" style="width:100%; background:rgba(15,23,42,0.5); border:1px solid rgba(255,255,255,0.08); border-radius:12px; color:#f1f5f9; padding:12px; outline:none; font-size:14px; resize:none;" disabled placeholder="Añada una nota interna..."></textarea>
                    </div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 2fr; gap:12px;">
                <button type="button" class="btn-limpiar" onclick="limpiarEstudiante()">
                    <i class="fas fa-eraser"></i> Limpiar
                </button>
                <button type="submit" class="btn-registrar" id="btnRegistrar" disabled>
                    <i class="fas fa-save"></i> Guardar Pago
                </button>
            </div>
        </form>
    </div>

    <div class="info-panels">
        <!-- ESTADO DE CUENTA -->
        <div class="pago-card">
            <div class="pago-card-header" style="justify-content:flex-start; border-bottom:1px solid rgba(255,255,255,0.05); padding:16px;">
                <i class="fas fa-chart-pie" style="color:#3b82f6; margin-right:8px;"></i>
                <h3 style="font-size:14px; color:#f1f5f9;">Estado de Cuenta</h3>
            </div>
            <div class="pago-card-body" id="estadoCuenta" style="min-height:220px; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:20px;">
                <i class="fas fa-user-clock" style="font-size:40px; color:#334155; margin-bottom:12px;"></i>
                <p style="color:#64748b; font-size:13px;">Seleccione un estudiante para ver su estado</p>
            </div>
        </div>

        <!-- RESUMEN DE TRANSACCIÓN -->
        <div class="pago-card resumen-card">
            <div class="resumen-border-top"></div>
            <div class="pago-card-header" style="justify-content:flex-start; padding:16px;">
                <i class="fas fa-file-alt" style="color:#f59e0b; margin-right:8px;"></i>
                <h3 style="font-size:14px; color:#f1f5f9;">Resumen de Transacción Actual</h3>
            </div>
            <div class="pago-card-body" id="resumenTransaccion" style="padding:0 24px 24px;">
                <div style="text-align:center; padding:40px 0;">
                    <p style="color:#64748b; font-size:13px;">Ingrese el detalle del cobro</p>
                </div>
            </div>
            <div class="resumen-border-bottom"></div>
        </div>
    </div>
</div>

<style>
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(-50%) translateY(-10px); }
        to { opacity: 1; transform: translateX(-50%) translateY(0); }
    }
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
    .pago-layout { display: grid; grid-template-columns: 1fr 400px; gap: 24px; align-items: start; }
    .pago-form-card, .pago-card {
        background: rgba(30, 41, 59, 0.4);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 16px;
    }
    .pago-form-card { padding: 24px; }
    .info-panels { display: flex; flex-direction: column; gap: 24px; position: sticky; top: 20px; }

    .resumen-card { position: relative; border: none; background: rgba(30, 41, 59, 0.5); }
    .resumen-border-top, .resumen-border-bottom {
        height: 6px;
        background: repeating-linear-gradient(45deg, #f59e0b, #f59e0b 10px, #0f172a 10px, #0f172a 20px);
        border-radius: 4px;
    }

    .pago-form-header { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
    .pago-form-header h2 { font-size: 16px; font-weight: 700; color: #f8fafc; }

    .section-label {
        display: block; font-size: 13px; font-weight: 600; color: #94a3b8; margin-bottom: 8px;
    }
    .input-icon-wrap { position: relative; }
    .input-icon-wrap i {
        position: absolute; left: 14px; top: 50%;
        transform: translateY(-50%); color: #64748b; font-size: 14px;
    }
    .input-icon-wrap input, .input-icon-wrap select, .select-programa {
        width: 100%; padding: 12px 16px 12px 42px;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 12px; color: #f1f5f9; font-size: 14px;
        outline: none; transition: all 0.2s;
        appearance: none;
    }
    .select-programa:not(:disabled) {
        border-color: #6366f1;
        cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 16px center;
        background-size: 16px;
    }
    .input-icon-wrap.no-icon input, .input-icon-wrap.no-icon textarea { padding-left: 16px; }
    .input-icon-wrap input:focus, .input-icon-wrap select:focus, .input-icon-wrap textarea:focus {
        border-color: #f59e0b; background: rgba(15, 23, 42, 0.8);
    }
    .input-icon-wrap input[readonly] { background: rgba(15, 23, 42, 0.3); color: #64748b; cursor: default; }

    /* Quitar flechas de input number */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance: textfield;
    }

    .search-results {
        position: absolute; width: 100%; z-index: 100; margin-top: 4px;
        background: #1e293b; border: 1px solid rgba(255,255,255,0.1);
        border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); display: none;
    }
    .search-item { padding: 10px 16px; cursor: pointer; border-bottom: 1px solid rgba(255,255,255,0.05); }
    .search-item:hover { background: rgba(245, 158, 11, 0.1); }

    .btn-limpiar {
        padding: 12px; background: rgba(51, 65, 85, 0.4); border: 1px solid rgba(255,255,255,0.1);
        border-radius: 12px; color: #94a3b8; font-weight: 600; cursor: pointer; transition: all 0.2s;
    }
    .btn-limpiar:hover { background: rgba(51, 65, 85, 0.6); color: #f1f5f9; }

    .btn-registrar {
        padding: 12px; background: #059669; border: none; border-radius: 12px;
        color: #fff; font-weight: 700; cursor: pointer; transition: all 0.2s;
    }
    .btn-registrar:disabled { opacity: 0.4; cursor: not-allowed; }
    .btn-registrar:not(:disabled):hover { background: #10b981; transform: translateY(-1px); }

    .resumen-data { border-top: 1px solid rgba(255,255,255,0.08); padding-top: 16px; }
    .resumen-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; }
    .resumen-label { color: #94a3b8; }
    .resumen-value { color: #f1f5f9; font-weight: 500; text-align: right; }
    .resumen-total {
        display: flex; justify-content: space-between; align-items: center;
        margin-top: 16px; padding-top: 16px; border-top: 2px solid #f59e0b;
    }
</style>

<script>
    let estudianteData = null;
    let cuotasData = [];
    let timeoutBusqueda = null;

    const buscarInput = document.getElementById('buscarEstudiante');
    const resultados = document.getElementById('resultadosEstudiante');
    const programaAsignado = document.getElementById('programaAsignado');
    const tipoPago = document.getElementById('tipoPago');
    const nroModulo = document.getElementById('nroModulo');
    const wrapNroModulo = document.getElementById('wrapNroModulo');
    const nroComprobante = document.getElementById('nroComprobante');
    const montoPago = document.getElementById('montoPago');
    const fechaPago = document.getElementById('fechaPago');
    const observacion = document.getElementById('observacion');
    const btnRegistrar = document.getElementById('btnRegistrar');
    const detalleInput = document.getElementById('detalle_plan_pago_id');
    const saldoHint = document.getElementById('saldoHint');
    const resumenContainer = document.getElementById('resumenTransaccion');
    const estadoCuentaContainer = document.getElementById('estadoCuenta');

    buscarInput.addEventListener('input', function() {
        clearTimeout(timeoutBusqueda);
        const q = this.value.trim();
        if (q.length < 2) { resultados.style.display = 'none'; return; }

        timeoutBusqueda = setTimeout(() => {
            fetch(`{{ route('pagos.buscar') }}?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.length) {
                        resultados.innerHTML = '<div class="search-item"><span>Sin resultados</span></div>';
                    } else {
                        resultados.innerHTML = data.map(e => `
                            <div class="search-item" onclick="seleccionarEstudiante(${e.id})">
                                <strong>${e.nombres} ${e.paterno} ${e.materno || ''}</strong>
                                <span>CI: ${e.cedula} · Reg: ${e.registro}</span>
                            </div>
                        `).join('');
                    }
                    resultados.style.display = 'block';
                });
        }, 300);
    });

    function seleccionarEstudiante(id) {
        fetch(`{{ url('api/pagos/cuotas') }}/${id}`)
            .then(r => r.json())
            .then(data => {
                estudianteData = data.estudiante;
                resultados.style.display = 'none';
                buscarInput.value = '';

                // Programa asignado - Convertido a Selector
                if (data.inscripciones && data.inscripciones.length > 0) {
                    const progSelect = document.getElementById('programaAsignado');
                    progSelect.disabled = false;
                    progSelect.innerHTML = data.inscripciones.map((ins, index) => `
                        <option value="${ins.inscripcion_id}" ${index === 0 ? 'selected' : ''}>
                            ${ins.curso} (${ins.tipo})
                        </option>
                    `).join('');

                    // Guardar todas las cuotas y filtrar por la primera inscripción seleccionada
                    allCuotasData = data.cuotas;
                    filtrarCuotasPorInscripcion(data.inscripciones[0].inscripcion_id);
                }

                // Habilitar campos
                [tipoPago, nroComprobante, fechaPago, observacion].forEach(el => el.disabled = false);

                // Actualizar paneles
                actualizarEstadoCuenta();
                actualizarResumen();
                validarFormulario();
            });
    }

    // Nueva función para filtrar cuotas cuando cambia el curso
    document.getElementById('programaAsignado').addEventListener('change', function() {
        filtrarCuotasPorInscripcion(parseInt(this.value));
        buscarYAsignarCuota();
        actualizarEstadoCuenta();
        actualizarResumen();
    });

    function filtrarCuotasPorInscripcion(inscripcionId) {
        cuotasData = allCuotasData.filter(c => c.inscripcion_id === inscripcionId);
    }

    let allCuotasData = []; // Variable global para guardar todas las cuotas de todos los cursos

    function limpiarEstudiante() {
        estudianteData = null;
        cuotasData = [];
        allCuotasData = [];
        const progSelect = document.getElementById('programaAsignado');
        progSelect.innerHTML = '<option value="">Seleccione estudiante primero...</option>';
        progSelect.disabled = true;
        detalleInput.value = '';
        tipoPago.value = '';
        tipoPago.disabled = true;
        wrapNroModulo.style.display = 'none';
        nroModulo.value = '';
        nroComprobante.value = '';
        nroComprobante.disabled = true;
        montoPago.value = '';
        fechaPago.disabled = true;
        observacion.value = '';
        observacion.disabled = true;
        saldoHint.textContent = '';
        btnRegistrar.disabled = true;

        estadoCuentaContainer.innerHTML = `
            <i class="fas fa-user-clock" style="font-size:40px; color:#334155; margin-bottom:12px;"></i>
            <p style="color:#64748b; font-size:13px;">Seleccione un estudiante para ver su estado</p>`;
        resumenContainer.innerHTML = `
            <div style="text-align:center; padding:40px 0;">
                <p style="color:#64748b; font-size:13px;">Ingrese el detalle del cobro</p>
            </div>`;
    }

    tipoPago.addEventListener('change', function() {
        if (this.value === 'Deposito') {
            wrapNroModulo.style.display = 'block';
            nroModulo.focus();
            buscarYAsignarCuota();
        } else {
            wrapNroModulo.style.display = 'none';
            buscarYAsignarCuota();
        }
        actualizarResumen();
        validarFormulario();
    });

    nroModulo.addEventListener('input', () => {
        buscarYAsignarCuota();
        actualizarResumen();
        validarFormulario();
    });

    nroComprobante.addEventListener('input', () => {
        actualizarResumen();
        validarFormulario();
    });

    observacion.addEventListener('input', () => {
        actualizarResumen();
    });

    function buscarYAsignarCuota() {
        const categoria = tipoPago.value;
        const numero = parseInt(nroModulo.value);
        let cuotaEncontrada = null;
        let mensajeBloqueo = null;
        let esPagoCompleto = false;

        if (categoria === 'Matricula') {
            // Regex para buscar el concepto de matricula exacto o que contenga "Matricula"
            const regexMatricula = /Matricula/i;

            // Buscar si existe alguna cuota con ese nombre que esté pendiente
            const pendiente = cuotasData.find(c => regexMatricula.test(c.concepto) && c.saldo > 0);
            // Buscar si existe alguna cuota con ese nombre que ya esté pagada
            const pagada = cuotasData.find(c => regexMatricula.test(c.concepto) && c.saldo === 0);

            if (pendiente) {
                cuotaEncontrada = pendiente;
            } else if (pagada) {
                mensajeBloqueo = "La Matrícula ya se encuentra pagada.";
                esPagoCompleto = true;
            } else {
                mensajeBloqueo = "La Matrícula ya se encuentra pagada.";
                esPagoCompleto = true;
            }
        } else if (categoria === 'Deposito') {
            if (numero > 0) {
                // Verificar si la matrícula está pagada
                const matriculaPendiente = cuotasData.find(c =>
                    (c.concepto === 'Matricula' || c.concepto.toLowerCase().includes('matricula')) && c.saldo > 0
                );

                if (matriculaPendiente) {
                    mensajeBloqueo = 'paga la matricula';
                    esPagoCompleto = false;
                } else {
                    const ordenFases = { 'Diplomado': 1, 'Especialidad': 2, 'Maestria': 3 };

                    // 1. Obtener todos los módulos con ese número, ordenados por fase
                    const cuotasConEseNumero = cuotasData.filter(c => {
                        const match = c.concepto.match(/\d+$/);
                        const nConcepto = match ? parseInt(match[0]) : null;
                        return (c.nro_modulo === numero || nConcepto === numero) &&
                               (c.concepto.toLowerCase().includes('deposito') || c.concepto.toLowerCase().includes('modulo'));
                    }).sort((a, b) => (ordenFases[a.fase] || 0) - (ordenFases[b.fase] || 0));

                    if (cuotasConEseNumero.length > 0) {
                        // 2. Buscar la primera que tenga saldo pendiente
                        let indexPendiente = cuotasConEseNumero.findIndex(c => c.saldo > 0);

                        if (indexPendiente !== -1) {
                            const cuotaPendiente = cuotasConEseNumero[indexPendiente];
                            const faseActualOrden = ordenFases[cuotaPendiente.fase] || 0;

                            // Verificar correlatividad (bloqueo por módulos anteriores)
                            const anteriorPendiente = cuotasData.find(c => {
                                if ((!c.concepto.toLowerCase().includes('deposito') && !c.concepto.toLowerCase().includes('modulo')) || c.saldo <= 0) return false;

                                const match = c.concepto.match(/\d+$/);
                                const nConcepto = match ? parseInt(match[0]) : null;
                                const n = c.nro_modulo || nConcepto;
                                if (!n) return false;

                                const faseOrden = ordenFases[c.fase] || 0;
                                if (faseOrden < faseActualOrden) return true;
                                if (faseOrden === faseActualOrden && n < numero) return true;
                                return false;
                            });

                            if (anteriorPendiente) {
                                // SI ESTÁ BLOQUEADO:
                                // ¿Existe una cuota con el mismo número en una fase anterior que YA ESTÉ PAGADA?
                                const pagadaAnterior = cuotasConEseNumero.slice(0, indexPendiente).find(c => c.saldo === 0);

                                if (pagadaAnterior) {
                                    mensajeBloqueo = `El módulo ${numero} ya se encuentra pagado.`;
                                    esPagoCompleto = true;
                                    // No asignamos cuotaEncontrada para que no intente cobrar
                                } else {
                                    mensajeBloqueo = `Debe pagar el módulo anterior (${anteriorPendiente.concepto}) primero.`;
                                }
                            } else {
                                cuotaEncontrada = cuotaPendiente;
                            }
                        } else {
                            // Todas las cuotas con ese número están pagadas
                            mensajeBloqueo = `El módulo ${numero} ya se encuentra pagado.`;
                            esPagoCompleto = true;
                        }
                    } else {
                        mensajeBloqueo = `El módulo ${numero} no existe en el plan de pagos.`;
                    }
                }
            }
        } else if (categoria === 'Defensa Diplomado' || categoria === 'Defensa Especialidad' || categoria === 'Defensa Maestria') {
            const faseNombre = categoria.replace('Defensa ', '');
            const pendiente = cuotasData.find(c => c.concepto === categoria && c.saldo > 0);
            const pagada = cuotasData.find(c => c.concepto === categoria && c.saldo === 0);

            if (pendiente) {
                // Verificar si hay módulos pendientes en esta misma fase
                const modulosPendientes = cuotasData.find(c =>
                    c.fase === faseNombre &&
                    (c.concepto.toLowerCase().includes('deposito') || c.concepto.toLowerCase().includes('modulo')) &&
                    c.saldo > 0
                );

                if (modulosPendientes) {
                    mensajeBloqueo = `Debe completar el pago de todos los módulos de ${faseNombre} antes de pagar la Defensa.`;
                } else {
                    cuotaEncontrada = pendiente;
                }
            } else if (pagada) {
                mensajeBloqueo = `La ${categoria} ya se encuentra pagada.`;
                esPagoCompleto = true;
            } else {
                mensajeBloqueo = `No se encontró deuda pendiente para ${categoria}.`;
                esPagoCompleto = true;
            }
        }

        if (cuotaEncontrada) {
            detalleInput.value = cuotaEncontrada.id;
            montoPago.value = parseFloat(cuotaEncontrada.saldo).toFixed(2);
            saldoHint.textContent = `Saldo pendiente: Bs ${parseFloat(cuotaEncontrada.saldo).toFixed(2)}`;
            saldoHint.style.color = '#4ade80';
        } else {
            detalleInput.value = '';
            montoPago.value = '';
            saldoHint.textContent = mensajeBloqueo || (categoria ? 'No se encontró deuda pendiente para este concepto' : '');
            saldoHint.style.color = esPagoCompleto ? '#3b82f6' : '#f87171';
        }
    }

    function actualizarEstadoCuenta() {
        if (!estudianteData) return;

        const totalDeuda = cuotasData.reduce((acc, c) => acc + c.saldo, 0);
        const iniciales = (estudianteData.nombre_completo.split(' ')[0][0] + (estudianteData.paterno ? estudianteData.paterno[0] : '')).toUpperCase();

        // Definir estado estándar
        const tieneVencidos = cuotasData.some(c => c.estado === 'Vencido' && c.saldo > 0);
        let situacionLabel = 'Al día';
        let situacionColor = '#22c55e';

        if (totalDeuda > 0) {
            situacionLabel = tieneVencidos ? 'En Mora' : 'Pendiente';
            situacionColor = tieneVencidos ? '#ef4444' : '#fbbf24';
        }

        estadoCuentaContainer.innerHTML = `
            <div style="width:100%; text-align:center;">
                <div style="display:flex; flex-direction:column; align-items:center; margin-bottom:16px;">
                    <div style="width:50px; height:50px; border-radius:50%; background:linear-gradient(135deg, #3b82f6, #6366f1); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:18px; margin-bottom:10px;">
                        ${iniciales}
                    </div>
                    <strong style="color:#f1f5f9; font-size:15px; display:block;">${estudianteData.nombre_completo}</strong>
                    <span style="color:#64748b; font-size:12px;">CI: ${estudianteData.cedula} · Reg: ${estudianteData.registro}</span>
                </div>

                <div style="margin-bottom:16px;">
                    <span style="color:#64748b; font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">Deuda Total Pendiente</span>
                    <div style="font-size:24px; font-weight:700; color:${totalDeuda > 0 ? '#ef4444' : '#22c55e'};">Bs ${totalDeuda.toLocaleString('es-BO', {minimumFractionDigits:2})}</div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; text-align:left;">
                    <div style="background:rgba(15,23,42,0.3); padding:10px; border-radius:10px; border:1px solid rgba(255,255,255,0.03);">
                        <span style="color:#64748b; font-size:10px; display:block; text-transform:uppercase;">Cuotas</span>
                        <strong style="color:#f1f5f9; font-size:14px;">${cuotasData.filter(c => c.saldo > 0).length}</strong>
                    </div>
                    <div style="background:rgba(15,23,42,0.3); padding:10px; border-radius:10px; border:1px solid rgba(255,255,255,0.03);">
                        <span style="color:#64748b; font-size:10px; display:block; text-transform:uppercase;">Situación</span>
                        <strong style="color:${situacionColor}; font-size:14px;">${situacionLabel}</strong>
                    </div>
                </div>
            </div>`;
    }

    function actualizarResumen() {
        if (!estudianteData) return;

        let conceptoText = tipoPago.options[tipoPago.selectedIndex].text;
        if (tipoPago.value === 'Deposito') {
            conceptoText += nroModulo.value ? ` (nº ${nroModulo.value})` : ' (Sin nº)';
        }

        const monto = montoPago.value ? parseFloat(montoPago.value).toFixed(2) : '0.00';
        const fecha = fechaPago.value ? new Date(fechaPago.value + 'T12:00:00').toLocaleDateString('es-BO') : '—';
        const ref = nroComprobante.value || '—';
        const obs = observacion.value || '—';

        // Detectar si está pagado para el resumen
        const mensajeYaPagado = saldoHint.textContent.includes("ya se encuentra pagada") ||
                                saldoHint.textContent.includes("ya se encuentra pagado");

        resumenContainer.innerHTML = `
            <div class="resumen-data">
                <div class="resumen-row">
                    <span class="resumen-label">Concepto:</span>
                    <strong class="resumen-value">${conceptoText}</strong>
                </div>
                <div class="resumen-row">
                    <span class="resumen-label">Estudiante:</span>
                    <span class="resumen-value">${estudianteData.nombre_completo}</span>
                </div>
                <div class="resumen-row">
                    <span class="resumen-label">Comprobante:</span>
                    <span class="resumen-value">${ref}</span>
                </div>
                <div class="resumen-row">
                    <span class="resumen-label">Fecha:</span>
                    <span class="resumen-value">${fecha}</span>
                </div>
                <div class="resumen-row">
                    <span class="resumen-label">Observación:</span>
                    <span class="resumen-value">${obs}</span>
                </div>

                <div class="resumen-total">
                    <strong style="color:#f8fafc; font-size:14px; text-transform:uppercase;">TOTAL A PAGAR:</strong>
                    <strong style="color:${mensajeYaPagado ? '#4ade80' : '#f59e0b'}; font-size:24px;">
                        ${mensajeYaPagado ? 'YA PAGADO' : 'Bs ' + monto}
                    </strong>
                </div>
            </div>`;
    }

    function validarFormulario() {
        // Bloquear si el concepto ya está pagado o si no hay deuda
        const estaPagado = saldoHint.textContent.includes("ya se encuentra pagada") ||
                           saldoHint.textContent.includes("ya se encuentra pagado");

        btnRegistrar.disabled = estaPagado || !(detalleInput.value && nroComprobante.value.trim() && parseFloat(montoPago.value) > 0);
    }

    // Prevenir cambios manuales en monto, nroModulo y nroComprobante con rueda del mouse
    [montoPago, nroModulo, nroComprobante].forEach(el => {
        el.addEventListener('wheel', e => e.preventDefault());
    });

    // Solo lectura para monto (bloquea teclado)
    montoPago.addEventListener('keydown', e => e.preventDefault());

    document.addEventListener('click', e => {
        if (!e.target.closest('#buscarEstudiante') && !e.target.closest('#resultadosEstudiante')) {
            resultados.style.display = 'none';
        }
    });

    // Auto-hide success message after 3 seconds
    const successMessage = document.getElementById('successMessage');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 300);
        }, 3000);
    }
</script>
@endsection

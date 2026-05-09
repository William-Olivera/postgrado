<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistema de Gestion Contable</title>
    <style>
        :root {
            --bg-primary: #f3f6fb;
            --bg-card: #ffffff;
            --text-primary: #12243a;
            --text-secondary: #4b5b72;
            --accent: #2f6fed;
            --accent-hover: #265bc2;
            --shadow: 0 18px 40px rgba(18, 36, 58, 0.12);
        }

        body.dark {
            --bg-primary: #0f1726;
            --bg-card: #172235;
            --text-primary: #e8eef9;
            --text-secondary: #a9b8cd;
            --accent: #5d8dff;
            --accent-hover: #7ca4ff;
            --shadow: 0 18px 40px rgba(3, 8, 17, 0.45);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", "Roboto", sans-serif;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--bg-primary), #dce6f5);
            color: var(--text-primary);
            transition: background 0.25s ease, color 0.25s ease;
            padding: 24px;
        }

        body.dark {
            background: linear-gradient(135deg, var(--bg-primary), #0a101c);
        }

        .container {
            width: min(900px, 100%);
            background: var(--bg-card);
            border-radius: 22px;
            box-shadow: var(--shadow);
            padding: 32px 24px;
            text-align: center;
            position: relative;
        }

        h1 {
            margin: 24px auto 36px;
            font-size: clamp(1.3rem, 2vw, 2rem);
            line-height: 1.4;
            max-width: 760px;
        }

        .actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
            width: min(520px, 100%);
            margin: 0 auto;
        }

        .action-btn--admin {
            grid-column: 1 / -1;
            justify-self: center;
        }

        .action-btn {
            border: none;
            border-radius: 12px;
            background: var(--accent);
            color: #fff;
            font-weight: 600;
            font-size: 1rem;
            padding: 14px 18px;
            cursor: pointer;
            transition: transform 0.16s ease, background 0.16s ease;
        }

        .action-btn:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
        }

        .theme-toggle {
            position: absolute;
            top: 18px;
            right: 20px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .switch {
            appearance: none;
            width: 48px;
            height: 26px;
            border-radius: 26px;
            background: #b8c6db;
            position: relative;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .switch::before {
            content: "";
            position: absolute;
            top: 3px;
            left: 3px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #ffffff;
            transition: transform 0.2s ease;
        }

        .switch:checked {
            background: var(--accent);
        }

        .switch:checked::before {
            transform: translateX(22px);
        }

        @media (max-width: 640px) {
            .container {
                padding-top: 58px;
            }
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 1000;
            background: rgba(18, 36, 58, 0.55);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            backdrop-filter: blur(4px);
        }

        .modal-overlay[hidden] {
            display: none;
        }

        .modal-dialog {
            width: min(720px, 100%);
            max-height: min(92vh, 900px);
            overflow: auto;
            background: var(--bg-card);
            color: var(--text-primary);
            border-radius: 18px;
            box-shadow: var(--shadow);
            text-align: left;
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 20px 22px 12px;
            border-bottom: 1px solid rgba(75, 91, 114, 0.2);
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.25rem;
        }

        .modal-close {
            border: none;
            background: transparent;
            color: var(--text-secondary);
            font-size: 1.75rem;
            line-height: 1;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 8px;
        }

        .modal-close:hover {
            background: rgba(75, 91, 114, 0.12);
            color: var(--text-primary);
        }

        .modal-steps {
            display: flex;
            gap: 10px;
            padding: 14px 22px;
            flex-wrap: wrap;
        }

        .step-badge {
            font-size: 0.85rem;
            font-weight: 600;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(47, 111, 237, 0.12);
            color: var(--text-secondary);
        }

        body.dark .step-badge {
            background: rgba(93, 141, 255, 0.15);
        }

        .step-badge.active {
            background: var(--accent);
            color: #fff;
        }

        .modal-body {
            padding: 8px 22px 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .field label {
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .field input,
        .field select {
            border: 1px solid rgba(75, 91, 114, 0.35);
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 1rem;
            background: var(--bg-card);
            color: var(--text-primary);
        }

        body.dark .field input,
        body.dark .field select {
            border-color: rgba(169, 184, 205, 0.35);
        }

        .field-row {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 10px;
            align-items: end;
        }

        @media (max-width: 560px) {
            .field-row {
                grid-template-columns: 1fr;
            }
        }

        .btn-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 8px;
        }

        .btn-secondary {
            border-radius: 10px;
            border: 1px solid rgba(75, 91, 114, 0.35);
            background: transparent;
            color: var(--text-primary);
            font-weight: 600;
            padding: 12px 16px;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background: rgba(75, 91, 114, 0.08);
        }

        .btn-primary {
            border: none;
            border-radius: 10px;
            background: var(--accent);
            color: #fff;
            font-weight: 600;
            padding: 12px 18px;
            cursor: pointer;
        }

        .btn-primary:hover {
            background: var(--accent-hover);
        }

        .btn-primary:disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }

        .search-results {
            border: 1px solid rgba(75, 91, 114, 0.25);
            border-radius: 12px;
            max-height: 220px;
            overflow: auto;
        }

        .search-result-item {
            width: 100%;
            text-align: left;
            padding: 12px 14px;
            border: none;
            border-bottom: 1px solid rgba(75, 91, 114, 0.12);
            background: transparent;
            color: var(--text-primary);
            cursor: pointer;
            font-size: 0.95rem;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        .search-result-item:hover {
            background: rgba(47, 111, 237, 0.08);
        }

        .search-result-item.selected {
            background: rgba(47, 111, 237, 0.18);
            font-weight: 600;
        }

        .hint {
            font-size: 0.82rem;
            color: var(--text-secondary);
            margin: 0;
        }

        .form-error {
            color: #c02626;
            font-size: 0.85rem;
            margin: 0;
        }

        body.dark .form-error {
            color: #f87171;
        }

        .radio-group {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
        }

        .radio-group label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            color: var(--text-primary);
            cursor: pointer;
        }

        .radio-group input {
            width: auto;
            accent-color: var(--accent);
        }

        .modal-dialog--wide {
            width: min(880px, 100%);
        }

        .montos-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-top: 12px;
        }

        @media (max-width: 600px) {
            .montos-grid {
                grid-template-columns: 1fr;
            }
        }

        .monto-card {
            border: 1px solid rgba(75, 91, 114, 0.22);
            border-radius: 12px;
            padding: 14px 16px;
            background: rgba(47, 111, 237, 0.06);
        }

        body.dark .monto-card {
            background: rgba(93, 141, 255, 0.08);
        }

        .monto-card .label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin: 0 0 6px;
        }

        .monto-card .valor {
            font-size: 1.35rem;
            font-weight: 700;
            margin: 0;
            color: var(--text-primary);
        }

        .saldos-table-wrap {
            overflow-x: auto;
            margin-top: 10px;
            border: 1px solid rgba(75, 91, 114, 0.22);
            border-radius: 12px;
        }

        .saldos-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.92rem;
        }

        .saldos-table th,
        .saldos-table td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid rgba(75, 91, 114, 0.15);
        }

        .saldos-table th {
            background: rgba(47, 111, 237, 0.08);
            font-weight: 600;
            color: var(--text-secondary);
        }

        .saldos-table tr:last-child td {
            border-bottom: none;
        }

        .saldos-table td.num {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        .panel-modo {
            margin-top: 8px;
        }

        .panel-modo[hidden] {
            display: none !important;
        }
    </style>
</head>
<body>
    <main class="container">
        <label class="theme-toggle">
            Tema oscuro
            <input id="themeSwitch" class="switch" type="checkbox" aria-label="Cambiar tema">
        </label>

        <h1>Sistema de gestion contable de la escuela de Post-grado de la F.I.N.O.R</h1>

        <section class="actions">
            <button class="action-btn" type="button" id="btnAbrirInscripcion">Iniciar inscripcion</button>
            <button class="action-btn" type="button" id="btnAbrirRegistrarPago">Registrar pago</button>
            <button class="action-btn" type="button" id="btnAbrirControlSaldos">Control de saldos</button>
            <button class="action-btn" type="button">Buscar pago</button>
            <button class="action-btn action-btn--admin" type="button" id="btnAbrirAdminDatos">Administrar datos</button>
        </section>
    </main>

    <div id="modalInscripcion" class="modal-overlay" hidden>
        <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="modalInscripcionTitle">
            <header class="modal-header">
                <h2 id="modalInscripcionTitle">Iniciar inscripción</h2>
                <button type="button" class="modal-close" id="btnCerrarModalInscripcion" aria-label="Cerrar">&times;</button>
            </header>
            <div class="modal-body">
                <div class="field">
                    <label for="selectCursoInscripcion">Curso <span aria-hidden="true">*</span></label>
                    <select id="selectCursoInscripcion" required>
                        <option value="">Cargando cursos…</option>
                    </select>
                </div>

                <div class="field">
                    <span>Tipo de estudiante <span aria-hidden="true">*</span></span>
                    <div class="radio-group">
                        <label><input type="radio" name="tipo_estudiante_ins" value="antiguo" checked> Antiguo</label>
                        <label><input type="radio" name="tipo_estudiante_ins" value="nuevo"> Nuevo</label>
                    </div>
                </div>

                <div id="panelInscripcionAntiguo" class="panel-modo">
                    <div class="field-row">
                        <div class="field">
                            <label for="campoBusquedaInsAnt">Buscar por</label>
                            <select id="campoBusquedaInsAnt">
                                <option value="nombre">Nombre (nombre / apellidos)</option>
                                <option value="registro">Número de registro</option>
                                <option value="cedula">Cédula</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="textoBusquedaInsAnt">Término</label>
                            <input type="text" id="textoBusquedaInsAnt" autocomplete="off" placeholder="Escriba y pulse Buscar">
                        </div>
                    </div>
                    <div class="btn-row">
                        <button type="button" class="btn-secondary" id="btnBuscarInscripcionAnt">Buscar</button>
                    </div>
                    <div class="field">
                        <label>Estudiante</label>
                        <div id="listaResultadosInsAnt" class="search-results" hidden></div>
                        <p id="msgInscripcionAnt" class="hint">Busque y seleccione un estudiante registrado.</p>
                    </div>
                </div>

                <form id="formInscripcionNuevo" class="panel-modo" hidden novalidate>
                    <div class="field">
                        <label for="insNombreE">Nombre(s) <span aria-hidden="true">*</span></label>
                        <input type="text" id="insNombreE" maxlength="50" required>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label for="insPaternoE">Apellido paterno <span aria-hidden="true">*</span></label>
                            <input type="text" id="insPaternoE" maxlength="20" required>
                        </div>
                        <div class="field">
                            <label for="insMaternoE">Apellido materno</label>
                            <input type="text" id="insMaternoE" maxlength="20">
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label for="insRegistroE">Registro (ID universitario) <span aria-hidden="true">*</span></label>
                            <input type="number" id="insRegistroE" min="1" step="1" required>
                        </div>
                        <div class="field">
                            <label for="insCedulaE">C.I. (cédula de identidad) <span aria-hidden="true">*</span></label>
                            <input type="text" id="insCedulaE" maxlength="20" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label for="insTelefonoE">Teléfono <span aria-hidden="true">*</span></label>
                            <input type="text" id="insTelefonoE" maxlength="20" required>
                        </div>
                        <div class="field">
                            <label for="insDescuentoE">Descuento (%) <span aria-hidden="true">*</span></label>
                            <select id="insDescuentoE" required>
                                <option value="0">0</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="30">30</option>
                                <option value="40">40</option>
                                <option value="50">50</option>
                                <option value="60">60</option>
                                <option value="70">70</option>
                                <option value="80">80</option>
                                <option value="90">90</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                </form>

                <p id="errorInscripcion" class="form-error" hidden></p>
                <div class="btn-row">
                    <button type="button" class="btn-primary" id="btnConfirmarInscripcion">Confirmar inscripción</button>
                </div>
            </div>
        </div>
    </div>

    <div id="modalRegistroPago" class="modal-overlay" hidden>
        <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="modalPagoTitle">
            <header class="modal-header">
                <h2 id="modalPagoTitle">Registrar pago</h2>
                <button type="button" class="modal-close" id="btnCerrarModalPago" aria-label="Cerrar">&times;</button>
            </header>
            <div class="modal-steps" id="modalStepsBar">
                <span class="step-badge active" data-step-indicator="1">1. Selección de datos</span>
                <span class="step-badge" data-step-indicator="2">2. Registrar datos de pago</span>
            </div>
            <div class="modal-body">
                <div id="panelPaso1">
                    <div class="field">
                        <label for="selectCursoPago">Curso <span aria-hidden="true">*</span></label>
                        <select id="selectCursoPago" required>
                            <option value="">Cargando cursos…</option>
                        </select>
                        <p class="hint">Seleccione el curso antes de buscar estudiantes inscritos.</p>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label for="campoBusquedaEst">Buscar por <span aria-hidden="true">*</span></label>
                            <select id="campoBusquedaEst" required>
                                <option value="nombre">Nombre (nombre / apellidos)</option>
                                <option value="registro">Número de registro</option>
                                <option value="cedula">Cédula</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="textoBusquedaEst">Término <span aria-hidden="true">*</span></label>
                            <input type="text" id="textoBusquedaEst" autocomplete="off" required placeholder="Escriba y pulse Buscar">
                        </div>
                    </div>
                    <div class="btn-row">
                        <button type="button" class="btn-secondary" id="btnBuscarEstudiante">Buscar</button>
                    </div>
                    <div class="field">
                        <label>Estudiante <span aria-hidden="true">*</span></label>
                        <div id="listaResultadosEst" class="search-results" hidden></div>
                        <p id="msgSinEstudiante" class="hint">Busque y seleccione un estudiante de la lista.</p>
                    </div>
                    <p id="errorPaso1" class="form-error" hidden></p>
                    <div class="btn-row">
                        <button type="button" class="btn-primary" id="btnContinuarPaso1">Continuar</button>
                    </div>
                </div>

                <div id="panelPaso2" hidden>
                    <p class="hint" id="resumenEstudianteCurso"></p>
                    <form id="formDatosPago" novalidate>
                        <div class="field">
                            <label for="montoPago">Monto pagado <span aria-hidden="true">*</span></label>
                            <input type="number" id="montoPago" name="MontoP" step="0.01" min="0.01" required placeholder="0.00">
                        </div>
                        <div class="field">
                            <span>Tipo de pago <span aria-hidden="true">*</span></span>
                            <div class="radio-group">
                                <label><input type="radio" name="TipoP" value="Matricula" required> Matrícula</label>
                                <label><input type="radio" name="TipoP" value="Cuota"> Cuota</label>
                                <label><input type="radio" name="TipoP" value="Defensa"> Defensa</label>
                            </div>
                        </div>
                        <div class="field" id="bloqueNroCuota" hidden>
                            <label for="nroCuotaSelect">Número de cuota <span aria-hidden="true">*</span></label>
                            <select id="nroCuotaSelect">
                                <option value="">Seleccione…</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="fechaPago">Fecha <span aria-hidden="true">*</span></label>
                            <input type="date" id="fechaPago" name="FechaP" required>
                        </div>
                        <div class="field">
                            <label for="nroComprobante">Número de comprobante <span aria-hidden="true">*</span></label>
                            <input type="number" id="nroComprobante" name="NroCompP" min="1" step="1" required placeholder="Ej. 21649751">
                        </div>
                        <div class="field">
                            <label for="cuentaBancaria">Número de cuenta bancaria <span aria-hidden="true">*</span></label>
                            <input type="text" id="cuentaBancaria" name="CuentaTransfP" maxlength="50" required placeholder="Cuenta desde la que transfirió">
                        </div>
                        <p id="errorPaso2" class="form-error" hidden></p>
                        <div class="btn-row">
                            <button type="button" class="btn-secondary" id="btnVolverPaso1">Atrás</button>
                            <button type="submit" class="btn-primary" id="btnRegistrarPagoEnvio">Registrar pago</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="modalControlSaldos" class="modal-overlay" hidden>
        <div class="modal-dialog modal-dialog--wide" role="dialog" aria-modal="true" aria-labelledby="modalSaldosTitle">
            <header class="modal-header">
                <h2 id="modalSaldosTitle">Control de saldos</h2>
                <button type="button" class="modal-close" id="btnCerrarModalSaldos" aria-label="Cerrar">&times;</button>
            </header>
            <div class="modal-body">
                <div class="field">
                    <span>Vista <span aria-hidden="true">*</span></span>
                    <div class="radio-group" id="saldosModoGroup">
                        <label><input type="radio" name="saldos_modo" value="estudiante" checked> Por estudiante</label>
                        <label><input type="radio" name="saldos_modo" value="curso"> Por curso</label>
                        <label><input type="radio" name="saldos_modo" value="gestion"> Por gestión</label>
                    </div>
                </div>

                <div id="saldosPanelEstudiante" class="panel-modo">
                    <div class="field-row">
                        <div class="field">
                            <label for="saldosCampoBusqueda">Buscar por</label>
                            <select id="saldosCampoBusqueda">
                                <option value="nombre">Nombre (nombre / apellidos)</option>
                                <option value="registro">Número de registro</option>
                                <option value="cedula">Cédula</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="saldosTextoBusqueda">Término</label>
                            <input type="text" id="saldosTextoBusqueda" autocomplete="off" placeholder="Escriba y pulse Buscar">
                        </div>
                    </div>
                    <div class="btn-row">
                        <button type="button" class="btn-secondary" id="btnSaldosBuscarEst">Buscar</button>
                    </div>
                    <div class="field">
                        <label>Resultados</label>
                        <div id="saldosListaEst" class="search-results" hidden></div>
                        <p id="saldosMsgEst" class="hint">Busque un estudiante con inscripción activa.</p>
                    </div>
                    <p id="saldosErrorEst" class="form-error" hidden></p>
                    <div id="saldosMontosEstudiante" hidden>
                        <p class="hint" id="saldosNombreEstudiante"></p>
                        <div class="montos-grid">
                            <div class="monto-card">
                                <p class="label">Monto pagado</p>
                                <p class="valor" id="saldosEstMontoPagado">—</p>
                            </div>
                            <div class="monto-card">
                                <p class="label">Monto en mora</p>
                                <p class="valor" id="saldosEstMontoMora">—</p>
                            </div>
                            <div class="monto-card">
                                <p class="label">Monto total (plan)</p>
                                <p class="valor" id="saldosEstMontoTotal">—</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="saldosPanelCurso" class="panel-modo" hidden>
                    <p class="hint">Cursos con al menos un estudiante inscrito.</p>
                    <div id="saldosCursoLoading" class="hint" hidden>Cargando…</div>
                    <p id="saldosErrorCurso" class="form-error" hidden></p>
                    <div id="saldosTablaCursoWrap" class="saldos-table-wrap" hidden>
                        <table class="saldos-table">
                            <thead>
                                <tr>
                                    <th>Curso</th>
                                    <th class="num">Estudiantes</th>
                                    <th class="num">Monto pagado</th>
                                    <th class="num">Monto en mora</th>
                                    <th class="num">Monto total</th>
                                </tr>
                            </thead>
                            <tbody id="saldosTablaCursoBody"></tbody>
                        </table>
                    </div>
                </div>

                <div id="saldosPanelGestion" class="panel-modo" hidden>
                    <div class="field-row">
                        <div class="field">
                            <label for="saldosAnioGestion">Año de gestión</label>
                            <input type="number" id="saldosAnioGestion" min="1990" max="2100" step="1">
                        </div>
                        <div class="field" style="align-self: end">
                            <button type="button" class="btn-primary" id="btnSaldosConsultarGestion">Consultar</button>
                        </div>
                    </div>
                    <p class="hint">Se consideran las inscripciones cuya fecha pertenece al año elegido.</p>
                    <p id="saldosErrorGestion" class="form-error" hidden></p>
                    <div id="saldosMontosGestion" class="montos-grid" hidden style="margin-top: 14px">
                        <div class="monto-card">
                            <p class="label">Monto pagado</p>
                            <p class="valor" id="saldosGesMontoPagado">—</p>
                        </div>
                        <div class="monto-card">
                            <p class="label">Monto en mora</p>
                            <p class="valor" id="saldosGesMontoMora">—</p>
                        </div>
                        <div class="monto-card">
                            <p class="label">Monto total</p>
                            <p class="valor" id="saldosGesMontoTotal">—</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalAdminDatos" class="modal-overlay" hidden>
        <div class="modal-dialog modal-dialog--wide" role="dialog" aria-modal="true" aria-labelledby="modalAdminTitle">
            <header class="modal-header">
                <h2 id="modalAdminTitle">Administrar datos</h2>
                <button type="button" class="modal-close" id="btnCerrarAdminDatos" aria-label="Cerrar">&times;</button>
            </header>
            <div class="modal-body">
                <div id="adminPanelMenu">
                    <p class="hint">Seleccione una acción.</p>
                    <div class="btn-row" style="flex-wrap: wrap; gap: 10px">
                        <button type="button" class="btn-primary" id="adminBtnIrCrearCurso">Crear curso</button>
                        <button type="button" class="btn-primary" id="adminBtnIrEditarCurso">Editar curso</button>
                        <button type="button" class="btn-primary" id="adminBtnIrEditarEst">Editar estudiante</button>
                        <button type="button" class="btn-primary" id="adminBtnIrEditarPago">Editar pago</button>
                    </div>
                </div>

                <div id="adminPanelCrearCurso" class="panel-modo" hidden>
                    <p class="hint">Complete los datos del nuevo curso.</p>
                    <div class="field">
                        <label for="adminNombreCurNuevo">Nombre del curso <span aria-hidden="true">*</span></label>
                        <input type="text" id="adminNombreCurNuevo" maxlength="100" autocomplete="off">
                    </div>
                    <div class="field">
                        <label for="adminTipoCurNuevo">Tipo <span aria-hidden="true">*</span></label>
                        <select id="adminTipoCurNuevo">
                            <option value="">Seleccione…</option>
                            <option value="Maestria">Maestría</option>
                            <option value="Diplomado">Diplomado</option>
                        </select>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label for="adminEdicionCurNuevo">Edición <span aria-hidden="true">*</span></label>
                            <input type="number" id="adminEdicionCurNuevo" min="1" step="1" value="1">
                        </div>
                        <div class="field">
                            <label for="adminVersionCurNuevo">Versión <span aria-hidden="true">*</span></label>
                            <input type="number" id="adminVersionCurNuevo" min="1" step="1" value="1">
                        </div>
                    </div>
                    <p id="adminErrorCrearCurso" class="form-error" hidden></p>
                    <div class="btn-row">
                        <button type="button" class="btn-secondary" id="adminBtnVolverDesdeCrearCurso">Volver</button>
                        <button type="button" class="btn-primary" id="adminBtnGuardarCrearCurso">Guardar curso</button>
                    </div>
                </div>

                <div id="adminPanelEditarCurso" class="panel-modo" hidden>
                    <div class="field">
                        <label for="adminSelectCursoEdit">Curso <span aria-hidden="true">*</span></label>
                        <select id="adminSelectCursoEdit">
                            <option value="">Cargando…</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="adminNombreCurEdit">Nombre del curso <span aria-hidden="true">*</span></label>
                        <input type="text" id="adminNombreCurEdit" maxlength="100" autocomplete="off">
                    </div>
                    <div class="field">
                        <label for="adminTipoCurEdit">Tipo <span aria-hidden="true">*</span></label>
                        <select id="adminTipoCurEdit">
                            <option value="">Seleccione…</option>
                            <option value="Maestria">Maestría</option>
                            <option value="Diplomado">Diplomado</option>
                        </select>
                    </div>
                    <div class="field-row">
                        <div class="field">
                            <label for="adminEdicionCurEdit">Edición <span aria-hidden="true">*</span></label>
                            <input type="number" id="adminEdicionCurEdit" min="1" step="1">
                        </div>
                        <div class="field">
                            <label for="adminVersionCurEdit">Versión <span aria-hidden="true">*</span></label>
                            <input type="number" id="adminVersionCurEdit" min="1" step="1">
                        </div>
                    </div>
                    <p id="adminErrorEditarCurso" class="form-error" hidden></p>
                    <div class="btn-row">
                        <button type="button" class="btn-secondary" id="adminBtnVolverDesdeEditarCurso">Volver</button>
                        <button type="button" class="btn-primary" id="adminBtnGuardarEditarCurso">Guardar cambios</button>
                    </div>
                </div>

                <div id="adminPanelEditarEst" class="panel-modo" hidden>
                    <div class="field-row">
                        <div class="field">
                            <label for="adminCampoBusqEst">Buscar por</label>
                            <select id="adminCampoBusqEst">
                                <option value="nombre">Nombre (nombre / apellidos)</option>
                                <option value="registro">Número de registro</option>
                                <option value="cedula">Cédula</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="adminTextoBusqEst">Término</label>
                            <input type="text" id="adminTextoBusqEst" autocomplete="off" placeholder="Escriba y pulse Buscar">
                        </div>
                    </div>
                    <div class="btn-row">
                        <button type="button" class="btn-secondary" id="adminBtnBuscarEst">Buscar</button>
                    </div>
                    <div class="field">
                        <label>Estudiante</label>
                        <div id="adminListaResultadosEst" class="search-results" hidden></div>
                        <p id="adminMsgEstBusqueda" class="hint">Busque y seleccione un estudiante.</p>
                    </div>
                    <hr style="margin: 16px 0; border: 0; border-top: 1px solid var(--border, #ccc)">
                    <div id="adminBloqueFormEst" hidden>
                        <div class="field">
                            <label for="adminEditNombreE">Nombre(s) <span aria-hidden="true">*</span></label>
                            <input type="text" id="adminEditNombreE" maxlength="50">
                        </div>
                        <div class="field-row">
                            <div class="field">
                                <label for="adminEditPaternoE">Apellido paterno <span aria-hidden="true">*</span></label>
                                <input type="text" id="adminEditPaternoE" maxlength="20">
                            </div>
                            <div class="field">
                                <label for="adminEditMaternoE">Apellido materno</label>
                                <input type="text" id="adminEditMaternoE" maxlength="20">
                            </div>
                        </div>
                        <div class="field-row">
                            <div class="field">
                                <label for="adminEditRegistroE">Registro (ID universitario) <span aria-hidden="true">*</span></label>
                                <input type="number" id="adminEditRegistroE" min="1" step="1">
                            </div>
                            <div class="field">
                                <label for="adminEditCedulaE">C.I. (cédula de identidad) <span aria-hidden="true">*</span></label>
                                <input type="text" id="adminEditCedulaE" maxlength="20">
                            </div>
                        </div>
                        <div class="field-row">
                            <div class="field">
                                <label for="adminEditTelefonoE">Teléfono <span aria-hidden="true">*</span></label>
                                <input type="text" id="adminEditTelefonoE" maxlength="20">
                            </div>
                            <div class="field">
                                <label for="adminEditDescuentoE">Descuento (%) <span aria-hidden="true">*</span></label>
                                <select id="adminEditDescuentoE">
                                    <option value="0">0</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="30">30</option>
                                    <option value="40">40</option>
                                    <option value="50">50</option>
                                    <option value="60">60</option>
                                    <option value="70">70</option>
                                    <option value="80">80</option>
                                    <option value="90">90</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <p id="adminErrorEditarEst" class="form-error" hidden></p>
                    <div class="btn-row">
                        <button type="button" class="btn-secondary" id="adminBtnVolverDesdeEditarEst">Volver</button>
                        <button type="button" class="btn-primary" id="adminBtnGuardarEst" disabled>Guardar estudiante</button>
                    </div>
                </div>

                <div id="adminPanelEditarPago" class="panel-modo" hidden>
                    <div class="field-row">
                        <div class="field">
                            <label for="adminCampoBusqPago">Buscar por</label>
                            <select id="adminCampoBusqPago">
                                <option value="nombre">Nombre (nombre / apellidos)</option>
                                <option value="registro">Número de registro</option>
                                <option value="cedula">Cédula</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="adminTextoBusqPago">Término</label>
                            <input type="text" id="adminTextoBusqPago" autocomplete="off" placeholder="Escriba y pulse Buscar">
                        </div>
                    </div>
                    <div class="btn-row">
                        <button type="button" class="btn-secondary" id="adminBtnBuscarEstPago">Buscar</button>
                    </div>
                    <div class="field">
                        <label>Estudiante</label>
                        <div id="adminListaEstPago" class="search-results" hidden></div>
                        <p id="adminMsgEstPago" class="hint">Busque y seleccione un estudiante.</p>
                    </div>
                    <div id="adminBloqueListaPagos" class="field" hidden>
                        <label>Pagos registrados</label>
                        <div id="adminListaPagosItems" class="search-results"></div>
                    </div>
                    <div id="adminBloqueFormPago" hidden>
                        <p class="hint" id="adminResumenPagoEdit"></p>
                        <div class="field">
                            <label for="adminMontoPagoEdit">Monto pagado <span aria-hidden="true">*</span></label>
                            <input type="number" id="adminMontoPagoEdit" step="0.01" min="0.01" placeholder="0.00">
                        </div>
                        <div class="field">
                            <span>Tipo de pago <span aria-hidden="true">*</span></span>
                            <div class="radio-group">
                                <label><input type="radio" name="adminTipoPagoEdit" value="Matricula"> Matrícula</label>
                                <label><input type="radio" name="adminTipoPagoEdit" value="Cuota"> Cuota</label>
                                <label><input type="radio" name="adminTipoPagoEdit" value="Defensa"> Defensa</label>
                            </div>
                        </div>
                        <div class="field" id="adminBloqueNroCuotaEdit" hidden>
                            <label for="adminNroCuotaEdit">Número de cuota <span aria-hidden="true">*</span></label>
                            <select id="adminNroCuotaEdit">
                                <option value="">Seleccione…</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="adminFechaPagoEdit">Fecha <span aria-hidden="true">*</span></label>
                            <input type="date" id="adminFechaPagoEdit">
                        </div>
                        <div class="field">
                            <label for="adminNroCompEdit">Número de comprobante <span aria-hidden="true">*</span></label>
                            <input type="number" id="adminNroCompEdit" min="1" step="1" placeholder="Ej. 21649751">
                        </div>
                        <div class="field">
                            <label for="adminCuentaEdit">Número de cuenta bancaria <span aria-hidden="true">*</span></label>
                            <input type="text" id="adminCuentaEdit" maxlength="50" placeholder="Cuenta desde la que transfirió">
                        </div>
                    </div>
                    <p id="adminErrorEditarPago" class="form-error" hidden></p>
                    <div class="btn-row">
                        <button type="button" class="btn-secondary" id="adminBtnVolverDesdeEditarPago">Volver</button>
                        <button type="button" class="btn-primary" id="adminBtnGuardarPago" disabled>Guardar pago</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const themeSwitch = document.getElementById('themeSwitch');
        const storedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const startDark = storedTheme ? storedTheme === 'dark' : prefersDark;

        if (startDark) {
            document.body.classList.add('dark');
            themeSwitch.checked = true;
        }

        themeSwitch.addEventListener('change', () => {
            const isDark = themeSwitch.checked;
            document.body.classList.toggle('dark', isDark);
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });

        (function () {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const modal = document.getElementById('modalInscripcion');
            const btnAbrir = document.getElementById('btnAbrirInscripcion');
            const btnCerrar = document.getElementById('btnCerrarModalInscripcion');
            const selectCurso = document.getElementById('selectCursoInscripcion');
            const panelAntiguo = document.getElementById('panelInscripcionAntiguo');
            const panelNuevo = document.getElementById('formInscripcionNuevo');
            const campoBusquedaAnt = document.getElementById('campoBusquedaInsAnt');
            const textoBusquedaAnt = document.getElementById('textoBusquedaInsAnt');
            const btnBuscarAnt = document.getElementById('btnBuscarInscripcionAnt');
            const listaAnt = document.getElementById('listaResultadosInsAnt');
            const msgAnt = document.getElementById('msgInscripcionAnt');
            const error = document.getElementById('errorInscripcion');
            const btnConfirmar = document.getElementById('btnConfirmarInscripcion');
            const radiosTipo = document.querySelectorAll('input[name="tipo_estudiante_ins"]');
            const insNombreE = document.getElementById('insNombreE');
            const insPaternoE = document.getElementById('insPaternoE');
            const insMaternoE = document.getElementById('insMaternoE');
            const insRegistroE = document.getElementById('insRegistroE');
            const insCedulaE = document.getElementById('insCedulaE');
            const insTelefonoE = document.getElementById('insTelefonoE');
            const insDescuentoE = document.getElementById('insDescuentoE');

            let selectedOldStudent = null;

            function tipoActual() {
                return document.querySelector('input[name="tipo_estudiante_ins"]:checked')?.value || 'antiguo';
            }

            function syncTipoUi() {
                const t = tipoActual();
                panelAntiguo.hidden = t !== 'antiguo';
                panelNuevo.hidden = t !== 'nuevo';
                error.hidden = true;
            }

            function showError(msg) {
                error.textContent = msg;
                error.hidden = false;
            }

            function resetForm() {
                selectedOldStudent = null;
                error.hidden = true;
                listaAnt.innerHTML = '';
                listaAnt.hidden = true;
                msgAnt.hidden = false;
                textoBusquedaAnt.value = '';
                panelNuevo.reset();
                document.querySelector('input[name="tipo_estudiante_ins"][value="antiguo"]').checked = true;
                syncTipoUi();
            }

            async function cargarCursos() {
                selectCurso.innerHTML = '<option value="">Cargando…</option>';
                try {
                    const r = await fetch('/api/cursos', { headers: { Accept: 'application/json' } });
                    const j = await r.json();
                    if (!r.ok) throw new Error(j.message || 'Error al cargar cursos');
                    selectCurso.innerHTML = '<option value="">Seleccione un curso…</option>';
                    (j.data || []).forEach((c) => {
                        const opt = document.createElement('option');
                        opt.value = c.Id_Cur;
                        opt.textContent = c.NombreCur + ' — ' + c.TipoCur + ' v' + c.VersionCur + ' (Ed. ' + c.EdicionCur + ')';
                        selectCurso.appendChild(opt);
                    });
                } catch (e) {
                    selectCurso.innerHTML = '<option value="">Error al cargar cursos</option>';
                }
            }

            function openModal() {
                resetForm();
                modal.hidden = false;
                document.body.style.overflow = 'hidden';
                cargarCursos();
            }

            function closeModal() {
                modal.hidden = true;
                document.body.style.overflow = '';
            }

            radiosTipo.forEach((r) => r.addEventListener('change', syncTipoUi));

            btnAbrir.addEventListener('click', openModal);
            btnCerrar.addEventListener('click', closeModal);

            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.hidden) closeModal();
            });

            btnBuscarAnt.addEventListener('click', async () => {
                error.hidden = true;
                const q = textoBusquedaAnt.value.trim();
                if (!q) {
                    showError('Ingrese un término de búsqueda.');
                    return;
                }
                listaAnt.innerHTML = '<p class="hint" style="padding:12px">Buscando…</p>';
                listaAnt.hidden = false;
                msgAnt.hidden = true;
                selectedOldStudent = null;
                try {
                    const params = new URLSearchParams({ campo: campoBusquedaAnt.value, q });
                    const r = await fetch('/api/estudiantes/base/buscar?' + params.toString(), {
                        headers: { Accept: 'application/json' },
                    });
                    const j = await r.json();
                    if (!r.ok) {
                        const err = j.errors ? Object.values(j.errors).flat().join(' ') : (j.message || 'Error en la búsqueda');
                        listaAnt.innerHTML = '';
                        listaAnt.hidden = true;
                        msgAnt.hidden = false;
                        showError(err);
                        return;
                    }
                    const rows = j.data || [];
                    listaAnt.innerHTML = '';
                    if (!rows.length) {
                        listaAnt.innerHTML = '<p class="hint" style="padding:12px">No hay resultados.</p>';
                        return;
                    }
                    rows.forEach((row) => {
                        const b = document.createElement('button');
                        b.type = 'button';
                        b.className = 'search-result-item';
                        b.textContent = row.nombre_completo + ' — Reg. ' + row.RegistroE + ' — CI ' + row.CedulaE;
                        b.addEventListener('click', () => {
                            listaAnt.querySelectorAll('.search-result-item').forEach((x) => x.classList.remove('selected'));
                            b.classList.add('selected');
                            selectedOldStudent = row;
                        });
                        listaAnt.appendChild(b);
                    });
                } catch (err) {
                    listaAnt.innerHTML = '';
                    listaAnt.hidden = true;
                    msgAnt.hidden = false;
                    showError('No se pudo completar la búsqueda.');
                }
            });

            btnConfirmar.addEventListener('click', async () => {
                error.hidden = true;
                const idCur = Number(selectCurso.value);
                if (!idCur) {
                    showError('Seleccione un curso.');
                    return;
                }

                const tipo = tipoActual();
                let payload = {
                    tipo_estudiante: tipo,
                    Id_Cur: idCur,
                };

                if (tipo === 'antiguo') {
                    if (!selectedOldStudent?.Id_E) {
                        showError('Seleccione un estudiante antiguo de la lista.');
                        return;
                    }
                    payload.Id_E = Number(selectedOldStudent.Id_E);
                } else {
                    const nombreE = insNombreE.value.trim();
                    const paternoE = insPaternoE.value.trim();
                    const maternoE = insMaternoE.value.trim();
                    const registroE = Number(insRegistroE.value);
                    const cedulaE = insCedulaE.value.trim();
                    const telefonoE = insTelefonoE.value.trim();
                    const descuentoE = Number(insDescuentoE.value);

                    if (!nombreE || !paternoE || !registroE || !cedulaE || !telefonoE) {
                        showError('Complete todos los campos obligatorios del estudiante nuevo.');
                        return;
                    }
                    if (descuentoE < 0 || descuentoE > 100 || descuentoE % 10 !== 0) {
                        showError('Seleccione un descuento válido (0 a 100, de 10 en 10).');
                        return;
                    }

                    payload.estudiante = {
                        nombreE: nombreE,
                        paternoE: paternoE,
                        maternoE: maternoE || null,
                        RegistroE: registroE,
                        CedulaE: cedulaE,
                        TelefonoE: telefonoE,
                        DescuentoE: descuentoE,
                    };
                }

                btnConfirmar.disabled = true;
                try {
                    const r = await fetch('/api/inscripciones', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                        body: JSON.stringify(payload),
                    });
                    const j = await r.json();
                    if (!r.ok) {
                        const msg = j.errors ? Object.values(j.errors).flat().join(' ') : (j.message || 'No se pudo registrar la inscripción.');
                        showError(msg);
                        return;
                    }
                    window.alert(j.message || 'Inscripción registrada correctamente.');
                    closeModal();
                    resetForm();
                } catch (e) {
                    showError('Error de red o del servidor.');
                } finally {
                    btnConfirmar.disabled = false;
                }
            });
        })();

        (function () {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const modal = document.getElementById('modalRegistroPago');
            const btnAbrir = document.getElementById('btnAbrirRegistrarPago');
            const btnCerrar = document.getElementById('btnCerrarModalPago');
            const panel1 = document.getElementById('panelPaso1');
            const panel2 = document.getElementById('panelPaso2');
            const selectCurso = document.getElementById('selectCursoPago');
            const campoBusqueda = document.getElementById('campoBusquedaEst');
            const textoBusqueda = document.getElementById('textoBusquedaEst');
            const btnBuscar = document.getElementById('btnBuscarEstudiante');
            const listaResultados = document.getElementById('listaResultadosEst');
            const msgSinEst = document.getElementById('msgSinEstudiante');
            const errorPaso1 = document.getElementById('errorPaso1');
            const btnContinuar1 = document.getElementById('btnContinuarPaso1');
            const btnVolver1 = document.getElementById('btnVolverPaso1');
            const resumen = document.getElementById('resumenEstudianteCurso');
            const formPago = document.getElementById('formDatosPago');
            const bloqueNroCuota = document.getElementById('bloqueNroCuota');
            const nroCuotaSelect = document.getElementById('nroCuotaSelect');
            const errorPaso2 = document.getElementById('errorPaso2');
            const btnRegistrar = document.getElementById('btnRegistrarPagoEnvio');
            const stepBadges = document.querySelectorAll('[data-step-indicator]');

            let selectedStudent = null;
            let selectedCursoLabel = '';

            function setStep(step) {
                const s = Number(step);
                panel1.hidden = s !== 1;
                panel2.hidden = s !== 2;
                stepBadges.forEach((el) => {
                    el.classList.toggle('active', Number(el.getAttribute('data-step-indicator')) === s);
                });
            }

            function resetModal() {
                setStep(1);
                selectedStudent = null;
                selectedCursoLabel = '';
                textoBusqueda.value = '';
                listaResultados.innerHTML = '';
                listaResultados.hidden = true;
                msgSinEst.hidden = false;
                errorPaso1.hidden = true;
                errorPaso2.hidden = true;
                formPago.reset();
                bloqueNroCuota.hidden = true;
                nroCuotaSelect.removeAttribute('required');
                const hoy = new Date().toISOString().slice(0, 10);
                document.getElementById('fechaPago').value = hoy;
            }

            function openModal() {
                resetModal();
                modal.hidden = false;
                document.body.style.overflow = 'hidden';
                cargarCursos();
            }

            function closeModal() {
                modal.hidden = true;
                document.body.style.overflow = '';
            }

            async function cargarCursos() {
                selectCurso.innerHTML = '<option value="">Cargando…</option>';
                try {
                    const r = await fetch('/api/cursos', { headers: { Accept: 'application/json' } });
                    const j = await r.json();
                    if (!r.ok) throw new Error(j.message || 'Error al cargar cursos');
                    selectCurso.innerHTML = '<option value="">Seleccione un curso…</option>';
                    (j.data || []).forEach((c) => {
                        const opt = document.createElement('option');
                        opt.value = c.Id_Cur;
                        opt.textContent = c.NombreCur + ' — ' + c.TipoCur + ' v' + c.VersionCur + ' (Ed. ' + c.EdicionCur + ')';
                        selectCurso.appendChild(opt);
                    });
                } catch (e) {
                    selectCurso.innerHTML = '<option value="">Error al cargar cursos</option>';
                }
            }

            function showErrorPaso1(msg) {
                errorPaso1.textContent = msg;
                errorPaso1.hidden = false;
            }

            function showErrorPaso2(msg) {
                errorPaso2.textContent = msg;
                errorPaso2.hidden = false;
            }

            btnAbrir.addEventListener('click', openModal);
            btnCerrar.addEventListener('click', closeModal);

            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.hidden) closeModal();
            });

            function syncTipoPagoUi() {
                const tipo = formPago.querySelector('input[name="TipoP"]:checked')?.value;
                if (tipo === 'Cuota') {
                    bloqueNroCuota.hidden = false;
                    nroCuotaSelect.setAttribute('required', 'required');
                } else {
                    bloqueNroCuota.hidden = true;
                    nroCuotaSelect.removeAttribute('required');
                    nroCuotaSelect.value = '';
                }
            }

            formPago.querySelectorAll('input[name="TipoP"]').forEach((r) => {
                r.addEventListener('change', syncTipoPagoUi);
            });

            btnBuscar.addEventListener('click', async () => {
                errorPaso1.hidden = true;
                const idCur = selectCurso.value;
                if (!idCur) {
                    showErrorPaso1('Seleccione un curso.');
                    return;
                }
                const q = textoBusqueda.value.trim();
                if (!q) {
                    showErrorPaso1('Ingrese un término de búsqueda.');
                    return;
                }
                listaResultados.innerHTML = '<p class="hint" style="padding:12px">Buscando…</p>';
                listaResultados.hidden = false;
                msgSinEst.hidden = true;
                selectedStudent = null;
                try {
                    const params = new URLSearchParams({
                        Id_Cur: idCur,
                        campo: campoBusqueda.value,
                        q,
                    });
                    const r = await fetch('/api/estudiantes/buscar?' + params.toString(), {
                        headers: { Accept: 'application/json' },
                    });
                    const j = await r.json();
                    if (!r.ok) {
                        const err = j.errors ? Object.values(j.errors).flat().join(' ') : (j.message || 'Error en la búsqueda');
                        listaResultados.innerHTML = '';
                        showErrorPaso1(err);
                        listaResultados.hidden = true;
                        msgSinEst.hidden = false;
                        return;
                    }
                    const rows = j.data || [];
                    listaResultados.innerHTML = '';
                    if (!rows.length) {
                        listaResultados.innerHTML = '<p class="hint" style="padding:12px">No hay resultados.</p>';
                        return;
                    }
                    rows.forEach((row) => {
                        const b = document.createElement('button');
                        b.type = 'button';
                        b.className = 'search-result-item';
                        b.dataset.id = row.Id_E;
                        b.dataset.nombre = row.nombre_completo;
                        b.dataset.registro = row.RegistroE;
                        b.dataset.cedula = row.CedulaE;
                        b.textContent = row.nombre_completo + ' — Reg. ' + row.RegistroE + ' — CI ' + row.CedulaE;
                        b.addEventListener('click', () => {
                            listaResultados.querySelectorAll('.search-result-item').forEach((x) => x.classList.remove('selected'));
                            b.classList.add('selected');
                            selectedStudent = {
                                Id_E: Number(b.dataset.id),
                                nombre_completo: b.dataset.nombre,
                            };
                        });
                        listaResultados.appendChild(b);
                    });
                } catch (e) {
                    listaResultados.innerHTML = '';
                    showErrorPaso1('No se pudo completar la búsqueda.');
                    listaResultados.hidden = true;
                    msgSinEst.hidden = false;
                }
            });

            btnContinuar1.addEventListener('click', () => {
                errorPaso1.hidden = true;
                const idCur = selectCurso.value;
                if (!idCur) {
                    showErrorPaso1('Seleccione un curso.');
                    return;
                }
                if (!selectedStudent) {
                    showErrorPaso1('Seleccione un estudiante de la lista de resultados.');
                    return;
                }
                selectedCursoLabel = selectCurso.options[selectCurso.selectedIndex]?.text || '';
                resumen.textContent =
                    'Estudiante: ' + selectedStudent.nombre_completo + ' — Curso: ' + selectedCursoLabel;
                setStep(2);
                syncTipoPagoUi();
                const hoy = new Date().toISOString().slice(0, 10);
                if (!document.getElementById('fechaPago').value) {
                    document.getElementById('fechaPago').value = hoy;
                }
            });

            btnVolver1.addEventListener('click', () => {
                errorPaso2.hidden = true;
                setStep(1);
            });

            function resolverNroP() {
                const tipo = formPago.querySelector('input[name="TipoP"]:checked')?.value;
                if (tipo === 'Matricula') return 0;
                if (tipo === 'Defensa') return 6;
                if (tipo === 'Cuota') return Number(nroCuotaSelect.value);
                return null;
            }

            formPago.addEventListener('submit', async (e) => {
                e.preventDefault();
                errorPaso2.hidden = true;
                const idCur = selectCurso.value;
                const tipo = formPago.querySelector('input[name="TipoP"]:checked')?.value;
                if (!tipo) {
                    showErrorPaso2('Seleccione el tipo de pago.');
                    return;
                }
                if (tipo === 'Cuota' && (!nroCuotaSelect.value || Number(nroCuotaSelect.value) < 1)) {
                    showErrorPaso2('Seleccione el número de cuota (1 a 5).');
                    return;
                }
                const monto = document.getElementById('montoPago').value;
                const fecha = document.getElementById('fechaPago').value;
                const nroComp = document.getElementById('nroComprobante').value;
                const cuenta = document.getElementById('cuentaBancaria').value.trim();
                if (!monto || Number(monto) <= 0) {
                    showErrorPaso2('Ingrese un monto válido.');
                    return;
                }
                if (!fecha) {
                    showErrorPaso2('Seleccione la fecha.');
                    return;
                }
                if (!nroComp || Number(nroComp) < 1) {
                    showErrorPaso2('Ingrese el número de comprobante.');
                    return;
                }
                if (!cuenta) {
                    showErrorPaso2('Ingrese el número de cuenta bancaria.');
                    return;
                }
                const nroP = resolverNroP();
                if (nroP === null || (tipo === 'Cuota' && (nroP < 1 || nroP > 5))) {
                    showErrorPaso2('Datos de cuota incompletos.');
                    return;
                }
                const payload = {
                    Id_E: selectedStudent.Id_E,
                    Id_Cur: Number(idCur),
                    MontoP: Number(monto),
                    TipoP: tipo,
                    NroP: nroP,
                    FechaP: fecha,
                    NroCompP: Number(nroComp),
                    CuentaTransfP: cuenta,
                };
                btnRegistrar.disabled = true;
                try {
                    const r = await fetch('/api/pagos', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                        body: JSON.stringify(payload),
                    });
                    const j = await r.json();
                    if (!r.ok) {
                        const msg = j.errors ? Object.values(j.errors).flat().join(' ') : (j.message || 'No se pudo registrar.');
                        showErrorPaso2(msg);
                        return;
                    }
                    window.alert(j.message || 'Pago registrado correctamente.');
                    closeModal();
                    resetModal();
                } catch (err) {
                    showErrorPaso2('Error de red o del servidor.');
                } finally {
                    btnRegistrar.disabled = false;
                }
            });
        })();

        (function () {
            const modal = document.getElementById('modalControlSaldos');
            const btnAbrir = document.getElementById('btnAbrirControlSaldos');
            const btnCerrar = document.getElementById('btnCerrarModalSaldos');
            const radios = document.querySelectorAll('input[name="saldos_modo"]');
            const panelEst = document.getElementById('saldosPanelEstudiante');
            const panelCur = document.getElementById('saldosPanelCurso');
            const panelGes = document.getElementById('saldosPanelGestion');
            const campoEst = document.getElementById('saldosCampoBusqueda');
            const textoEst = document.getElementById('saldosTextoBusqueda');
            const btnBuscarEst = document.getElementById('btnSaldosBuscarEst');
            const listaEst = document.getElementById('saldosListaEst');
            const msgEst = document.getElementById('saldosMsgEst');
            const errEst = document.getElementById('saldosErrorEst');
            const montosEstWrap = document.getElementById('saldosMontosEstudiante');
            const nombreEst = document.getElementById('saldosNombreEstudiante');
            const curLoading = document.getElementById('saldosCursoLoading');
            const errCur = document.getElementById('saldosErrorCurso');
            const tblWrap = document.getElementById('saldosTablaCursoWrap');
            const tblBody = document.getElementById('saldosTablaCursoBody');
            const anioGes = document.getElementById('saldosAnioGestion');
            const btnConsGes = document.getElementById('btnSaldosConsultarGestion');
            const errGes = document.getElementById('saldosErrorGestion');
            const montosGes = document.getElementById('saldosMontosGestion');

            function fmtMonto(n) {
                return Number(n).toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function closeModal() {
                modal.hidden = true;
                document.body.style.overflow = '';
            }

            function openModal() {
                cursosCargados = false;
                modal.hidden = false;
                document.body.style.overflow = 'hidden';
                const y = new Date().getFullYear();
                anioGes.value = y;
                textoEst.value = '';
                listaEst.innerHTML = '';
                listaEst.hidden = true;
                msgEst.hidden = false;
                errEst.hidden = true;
                errCur.hidden = true;
                errGes.hidden = true;
                montosEstWrap.hidden = true;
                montosGes.hidden = true;
                tblWrap.hidden = true;
                document.querySelector('input[name="saldos_modo"][value="estudiante"]').checked = true;
                syncPanels();
            }

            function syncPanels() {
                const modo = document.querySelector('input[name="saldos_modo"]:checked')?.value || 'estudiante';
                panelEst.hidden = modo !== 'estudiante';
                panelCur.hidden = modo !== 'curso';
                panelGes.hidden = modo !== 'gestion';
                if (modo === 'curso') {
                    cargarSaldosCursos();
                }
            }

            radios.forEach((r) => r.addEventListener('change', syncPanels));

            btnAbrir.addEventListener('click', openModal);
            btnCerrar.addEventListener('click', closeModal);
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.hidden) closeModal();
            });

            btnBuscarEst.addEventListener('click', async () => {
                errEst.hidden = true;
                montosEstWrap.hidden = true;
                const q = textoEst.value.trim();
                if (!q) {
                    errEst.textContent = 'Ingrese un término de búsqueda.';
                    errEst.hidden = false;
                    return;
                }
                listaEst.innerHTML = '<p class="hint" style="padding:12px">Buscando…</p>';
                listaEst.hidden = false;
                msgEst.hidden = true;
                try {
                    const params = new URLSearchParams({ campo: campoEst.value, q });
                    const r = await fetch('/api/saldos/estudiantes/buscar?' + params.toString(), {
                        headers: { Accept: 'application/json' },
                    });
                    const j = await r.json();
                    if (!r.ok) {
                        errEst.textContent = j.errors ? Object.values(j.errors).flat().join(' ') : (j.message || 'Error');
                        errEst.hidden = false;
                        listaEst.hidden = true;
                        msgEst.hidden = false;
                        return;
                    }
                    const rows = j.data || [];
                    listaEst.innerHTML = '';
                    if (!rows.length) {
                        listaEst.innerHTML = '<p class="hint" style="padding:12px">No hay resultados.</p>';
                        return;
                    }
                    rows.forEach((row) => {
                        const b = document.createElement('button');
                        b.type = 'button';
                        b.className = 'search-result-item';
                        b.textContent = row.nombre_completo + ' — Reg. ' + row.RegistroE + ' — CI ' + row.CedulaE;
                        b.addEventListener('click', () => cargarSaldoEstudiante(row));
                        listaEst.appendChild(b);
                    });
                } catch (err) {
                    listaEst.innerHTML = '';
                    errEst.textContent = 'No se pudo completar la búsqueda.';
                    errEst.hidden = false;
                    listaEst.hidden = true;
                    msgEst.hidden = false;
                }
            });

            async function cargarSaldoEstudiante(row) {
                errEst.hidden = true;
                listaEst.querySelectorAll('.search-result-item').forEach((x) => x.classList.remove('selected'));
                try {
                    const r = await fetch('/api/saldos/estudiante/' + row.Id_E, { headers: { Accept: 'application/json' } });
                    const j = await r.json();
                    if (!r.ok) {
                        errEst.textContent = j.message || 'No se pudo obtener el saldo.';
                        errEst.hidden = false;
                        montosEstWrap.hidden = true;
                        return;
                    }
                    const d = j.data;
                    nombreEst.textContent = d.nombre_completo;
                    document.getElementById('saldosEstMontoPagado').textContent = fmtMonto(d.monto_pagado);
                    document.getElementById('saldosEstMontoMora').textContent = fmtMonto(d.monto_mora);
                    document.getElementById('saldosEstMontoTotal').textContent = fmtMonto(d.monto_total);
                    montosEstWrap.hidden = false;
                } catch (e) {
                    errEst.textContent = 'Error de red o del servidor.';
                    errEst.hidden = false;
                    montosEstWrap.hidden = true;
                }
            }

            let cursosCargados = false;

            async function cargarSaldosCursos() {
                if (cursosCargados) return;
                curLoading.hidden = false;
                errCur.hidden = true;
                tblWrap.hidden = true;
                try {
                    const r = await fetch('/api/saldos/cursos', { headers: { Accept: 'application/json' } });
                    const j = await r.json();
                    curLoading.hidden = true;
                    if (!r.ok) {
                        errCur.textContent = j.message || 'Error al cargar cursos.';
                        errCur.hidden = false;
                        return;
                    }
                    tblBody.innerHTML = '';
                    (j.data || []).forEach((c) => {
                        const tr = document.createElement('tr');
                        tr.innerHTML =
                            '<td>' +
                            escapeHtml(c.nombre_curso) +
                            '</td><td class="num">' +
                            c.cantidad_estudiantes +
                            '</td><td class="num">' +
                            fmtMonto(c.monto_pagado) +
                            '</td><td class="num">' +
                            fmtMonto(c.monto_mora) +
                            '</td><td class="num">' +
                            fmtMonto(c.monto_total) +
                            '</td>';
                        tblBody.appendChild(tr);
                    });
                    tblWrap.hidden = false;
                    cursosCargados = true;
                } catch (e) {
                    curLoading.hidden = true;
                    errCur.textContent = 'No se pudo cargar la información.';
                    errCur.hidden = false;
                }
            }

            function escapeHtml(s) {
                const d = document.createElement('div');
                d.textContent = s;
                return d.innerHTML;
            }

            btnConsGes.addEventListener('click', async () => {
                errGes.hidden = true;
                montosGes.hidden = true;
                const anio = parseInt(anioGes.value, 10);
                if (!anio || anio < 1990 || anio > 2100) {
                    errGes.textContent = 'Indique un año válido (1990–2100).';
                    errGes.hidden = false;
                    return;
                }
                try {
                    const r = await fetch('/api/saldos/gestion?anio=' + encodeURIComponent(anio), {
                        headers: { Accept: 'application/json' },
                    });
                    const j = await r.json();
                    if (!r.ok) {
                        errGes.textContent = j.errors ? Object.values(j.errors).flat().join(' ') : (j.message || 'Error');
                        errGes.hidden = false;
                        return;
                    }
                    const d = j.data;
                    document.getElementById('saldosGesMontoPagado').textContent = fmtMonto(d.monto_pagado);
                    document.getElementById('saldosGesMontoMora').textContent = fmtMonto(d.monto_mora);
                    document.getElementById('saldosGesMontoTotal').textContent = fmtMonto(d.monto_total);
                    montosGes.hidden = false;
                } catch (e) {
                    errGes.textContent = 'Error de red o del servidor.';
                    errGes.hidden = false;
                }
            });
        })();

        (function () {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const modal = document.getElementById('modalAdminDatos');
            const btnAbrir = document.getElementById('btnAbrirAdminDatos');
            const btnCerrar = document.getElementById('btnCerrarAdminDatos');
            const panelMenu = document.getElementById('adminPanelMenu');
            const panelCrear = document.getElementById('adminPanelCrearCurso');
            const panelEditCur = document.getElementById('adminPanelEditarCurso');
            const panelEditEst = document.getElementById('adminPanelEditarEst');
            const panelEditPago = document.getElementById('adminPanelEditarPago');

            let adminCursosCache = [];
            let adminSelectedEstId = null;
            let adminSelectedPagoId = null;
            let adminSelectedEstPagoNombre = '';

            function showAdminPanel(which) {
                panelMenu.hidden = which !== 'menu';
                panelCrear.hidden = which !== 'crear';
                panelEditCur.hidden = which !== 'edit_cur';
                panelEditEst.hidden = which !== 'edit_est';
                panelEditPago.hidden = which !== 'edit_pago';
            }

            function resetAdminModal() {
                showAdminPanel('menu');
                document.getElementById('adminErrorCrearCurso').hidden = true;
                document.getElementById('adminErrorEditarCurso').hidden = true;
                document.getElementById('adminErrorEditarEst').hidden = true;
                document.getElementById('adminErrorEditarPago').hidden = true;
                document.getElementById('adminNombreCurNuevo').value = '';
                document.getElementById('adminTipoCurNuevo').value = '';
                document.getElementById('adminEdicionCurNuevo').value = '1';
                document.getElementById('adminVersionCurNuevo').value = '1';
                adminCursosCache = [];
                adminSelectedEstId = null;
                adminSelectedPagoId = null;
                adminSelectedEstPagoNombre = '';
                document.getElementById('adminListaResultadosEst').innerHTML = '';
                document.getElementById('adminListaResultadosEst').hidden = true;
                document.getElementById('adminMsgEstBusqueda').hidden = false;
                document.getElementById('adminTextoBusqEst').value = '';
                document.getElementById('adminBloqueFormEst').hidden = true;
                document.getElementById('adminBtnGuardarEst').disabled = true;
                document.getElementById('adminListaEstPago').innerHTML = '';
                document.getElementById('adminListaEstPago').hidden = true;
                document.getElementById('adminMsgEstPago').hidden = false;
                document.getElementById('adminTextoBusqPago').value = '';
                document.getElementById('adminBloqueListaPagos').hidden = true;
                document.getElementById('adminListaPagosItems').innerHTML = '';
                document.getElementById('adminBloqueFormPago').hidden = true;
                document.getElementById('adminBtnGuardarPago').disabled = true;
                document.querySelectorAll('input[name="adminTipoPagoEdit"]').forEach((r) => {
                    r.checked = false;
                });
                document.getElementById('adminBloqueNroCuotaEdit').hidden = true;
                document.getElementById('adminNroCuotaEdit').value = '';
                document.getElementById('adminNroCuotaEdit').removeAttribute('required');
            }

            function openModal() {
                resetAdminModal();
                modal.hidden = false;
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                modal.hidden = true;
                document.body.style.overflow = '';
            }

            btnAbrir.addEventListener('click', openModal);
            btnCerrar.addEventListener('click', closeModal);
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.hidden) closeModal();
            });

            document.getElementById('adminBtnIrCrearCurso').addEventListener('click', () => {
                showAdminPanel('crear');
                document.getElementById('adminErrorCrearCurso').hidden = true;
            });
            document.getElementById('adminBtnVolverDesdeCrearCurso').addEventListener('click', () => showAdminPanel('menu'));

            document.getElementById('adminBtnIrEditarCurso').addEventListener('click', async () => {
                showAdminPanel('edit_cur');
                document.getElementById('adminErrorEditarCurso').hidden = true;
                const sel = document.getElementById('adminSelectCursoEdit');
                sel.innerHTML = '<option value="">Cargando…</option>';
                try {
                    const r = await fetch('/api/cursos', { headers: { Accept: 'application/json' } });
                    const j = await r.json();
                    if (!r.ok) throw new Error();
                    adminCursosCache = j.data || [];
                    sel.innerHTML = '<option value="">Seleccione un curso…</option>';
                    adminCursosCache.forEach((c) => {
                        const opt = document.createElement('option');
                        opt.value = c.Id_Cur;
                        opt.textContent = c.NombreCur + ' — ' + c.TipoCur + ' v' + c.VersionCur + ' (Ed. ' + c.EdicionCur + ')';
                        sel.appendChild(opt);
                    });
                    sel.value = '';
                    document.getElementById('adminNombreCurEdit').value = '';
                    document.getElementById('adminTipoCurEdit').value = '';
                    document.getElementById('adminEdicionCurEdit').value = '';
                    document.getElementById('adminVersionCurEdit').value = '';
                } catch (e) {
                    sel.innerHTML = '<option value="">Error al cargar</option>';
                }
            });

            document.getElementById('adminSelectCursoEdit').addEventListener('change', () => {
                const id = Number(document.getElementById('adminSelectCursoEdit').value);
                const c = adminCursosCache.find((x) => Number(x.Id_Cur) === id);
                if (!c) {
                    document.getElementById('adminNombreCurEdit').value = '';
                    document.getElementById('adminTipoCurEdit').value = '';
                    document.getElementById('adminEdicionCurEdit').value = '';
                    document.getElementById('adminVersionCurEdit').value = '';
                    return;
                }
                document.getElementById('adminNombreCurEdit').value = c.NombreCur;
                document.getElementById('adminTipoCurEdit').value = c.TipoCur;
                document.getElementById('adminEdicionCurEdit').value = c.EdicionCur;
                document.getElementById('adminVersionCurEdit').value = c.VersionCur;
            });

            document.getElementById('adminBtnVolverDesdeEditarCurso').addEventListener('click', () => showAdminPanel('menu'));

            document.getElementById('adminBtnGuardarCrearCurso').addEventListener('click', async () => {
                const errEl = document.getElementById('adminErrorCrearCurso');
                errEl.hidden = true;
                const NombreCur = document.getElementById('adminNombreCurNuevo').value.trim();
                const TipoCur = document.getElementById('adminTipoCurNuevo').value;
                const EdicionCur = Number(document.getElementById('adminEdicionCurNuevo').value);
                const VersionCur = Number(document.getElementById('adminVersionCurNuevo').value);
                if (!NombreCur || !TipoCur || !EdicionCur || !VersionCur) {
                    errEl.textContent = 'Complete todos los campos obligatorios.';
                    errEl.hidden = false;
                    return;
                }
                const btn = document.getElementById('adminBtnGuardarCrearCurso');
                btn.disabled = true;
                try {
                    const r = await fetch('/api/admin/cursos', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                        body: JSON.stringify({ NombreCur, TipoCur, EdicionCur, VersionCur }),
                    });
                    const j = await r.json();
                    if (!r.ok) {
                        errEl.textContent = j.errors ? Object.values(j.errors).flat().join(' ') : (j.message || 'Error');
                        errEl.hidden = false;
                        return;
                    }
                    window.alert(j.message || 'Curso creado.');
                    resetAdminModal();
                } catch (e) {
                    errEl.textContent = 'Error de red o del servidor.';
                    errEl.hidden = false;
                } finally {
                    btn.disabled = false;
                }
            });

            document.getElementById('adminBtnGuardarEditarCurso').addEventListener('click', async () => {
                const errEl = document.getElementById('adminErrorEditarCurso');
                errEl.hidden = true;
                const id = Number(document.getElementById('adminSelectCursoEdit').value);
                if (!id) {
                    errEl.textContent = 'Seleccione un curso.';
                    errEl.hidden = false;
                    return;
                }
                const NombreCur = document.getElementById('adminNombreCurEdit').value.trim();
                const TipoCur = document.getElementById('adminTipoCurEdit').value;
                const EdicionCur = Number(document.getElementById('adminEdicionCurEdit').value);
                const VersionCur = Number(document.getElementById('adminVersionCurEdit').value);
                if (!NombreCur || !TipoCur || !EdicionCur || !VersionCur) {
                    errEl.textContent = 'Complete todos los campos obligatorios.';
                    errEl.hidden = false;
                    return;
                }
                const btn = document.getElementById('adminBtnGuardarEditarCurso');
                btn.disabled = true;
                try {
                    const r = await fetch('/api/admin/cursos/' + id, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                        body: JSON.stringify({ NombreCur, TipoCur, EdicionCur, VersionCur }),
                    });
                    const j = await r.json();
                    if (!r.ok) {
                        errEl.textContent = j.errors ? Object.values(j.errors).flat().join(' ') : (j.message || 'Error');
                        errEl.hidden = false;
                        return;
                    }
                    window.alert(j.message || 'Curso actualizado.');
                    resetAdminModal();
                } catch (e) {
                    errEl.textContent = 'Error de red o del servidor.';
                    errEl.hidden = false;
                } finally {
                    btn.disabled = false;
                }
            });

            document.getElementById('adminBtnIrEditarEst').addEventListener('click', () => {
                showAdminPanel('edit_est');
                document.getElementById('adminErrorEditarEst').hidden = true;
            });
            document.getElementById('adminBtnVolverDesdeEditarEst').addEventListener('click', () => showAdminPanel('menu'));

            document.getElementById('adminBtnBuscarEst').addEventListener('click', async () => {
                const errEl = document.getElementById('adminErrorEditarEst');
                errEl.hidden = true;
                const q = document.getElementById('adminTextoBusqEst').value.trim();
                if (!q) {
                    errEl.textContent = 'Ingrese un término de búsqueda.';
                    errEl.hidden = false;
                    return;
                }
                const lista = document.getElementById('adminListaResultadosEst');
                const msg = document.getElementById('adminMsgEstBusqueda');
                lista.innerHTML = '<p class="hint" style="padding:12px">Buscando…</p>';
                lista.hidden = false;
                msg.hidden = true;
                adminSelectedEstId = null;
                document.getElementById('adminBloqueFormEst').hidden = true;
                document.getElementById('adminBtnGuardarEst').disabled = true;
                try {
                    const params = new URLSearchParams({
                        campo: document.getElementById('adminCampoBusqEst').value,
                        q,
                    });
                    const r = await fetch('/api/estudiantes/base/buscar?' + params.toString(), {
                        headers: { Accept: 'application/json' },
                    });
                    const j = await r.json();
                    if (!r.ok) {
                        lista.innerHTML = '';
                        lista.hidden = true;
                        msg.hidden = false;
                        errEl.textContent = j.errors ? Object.values(j.errors).flat().join(' ') : (j.message || 'Error');
                        errEl.hidden = false;
                        return;
                    }
                    const rows = j.data || [];
                    lista.innerHTML = '';
                    if (!rows.length) {
                        lista.innerHTML = '<p class="hint" style="padding:12px">No hay resultados.</p>';
                        return;
                    }
                    rows.forEach((row) => {
                        const b = document.createElement('button');
                        b.type = 'button';
                        b.className = 'search-result-item';
                        b.textContent = row.nombre_completo + ' — Reg. ' + row.RegistroE + ' — CI ' + row.CedulaE;
                        b.addEventListener('click', async () => {
                            lista.querySelectorAll('.search-result-item').forEach((x) => x.classList.remove('selected'));
                            b.classList.add('selected');
                            errEl.hidden = true;
                            try {
                                const r2 = await fetch('/api/admin/estudiantes/' + row.Id_E, {
                                    headers: { Accept: 'application/json' },
                                });
                                const j2 = await r2.json();
                                if (!r2.ok) {
                                    errEl.textContent = j2.message || 'No se pudo cargar el estudiante.';
                                    errEl.hidden = false;
                                    return;
                                }
                                const d = j2.data;
                                adminSelectedEstId = d.Id_E;
                                document.getElementById('adminEditNombreE').value = d.nombreE;
                                document.getElementById('adminEditPaternoE').value = d.paternoE;
                                document.getElementById('adminEditMaternoE').value = d.maternoE || '';
                                document.getElementById('adminEditRegistroE').value = d.RegistroE;
                                document.getElementById('adminEditCedulaE').value = d.CedulaE;
                                document.getElementById('adminEditTelefonoE').value = d.TelefonoE;
                                document.getElementById('adminEditDescuentoE').value = String(d.DescuentoE);
                                document.getElementById('adminBloqueFormEst').hidden = false;
                                document.getElementById('adminBtnGuardarEst').disabled = false;
                            } catch (e2) {
                                errEl.textContent = 'Error al cargar datos del estudiante.';
                                errEl.hidden = false;
                            }
                        });
                        lista.appendChild(b);
                    });
                } catch (e) {
                    lista.innerHTML = '';
                    lista.hidden = true;
                    msg.hidden = false;
                    errEl.textContent = 'No se pudo completar la búsqueda.';
                    errEl.hidden = false;
                }
            });

            document.getElementById('adminBtnGuardarEst').addEventListener('click', async () => {
                const errEl = document.getElementById('adminErrorEditarEst');
                errEl.hidden = true;
                if (!adminSelectedEstId) return;
                const payload = {
                    nombreE: document.getElementById('adminEditNombreE').value.trim(),
                    paternoE: document.getElementById('adminEditPaternoE').value.trim(),
                    maternoE: document.getElementById('adminEditMaternoE').value.trim() || null,
                    RegistroE: Number(document.getElementById('adminEditRegistroE').value),
                    CedulaE: document.getElementById('adminEditCedulaE').value.trim(),
                    TelefonoE: document.getElementById('adminEditTelefonoE').value.trim(),
                    DescuentoE: Number(document.getElementById('adminEditDescuentoE').value),
                };
                if (!payload.nombreE || !payload.paternoE || !payload.RegistroE || !payload.CedulaE || !payload.TelefonoE) {
                    errEl.textContent = 'Complete todos los campos obligatorios.';
                    errEl.hidden = false;
                    return;
                }
                const btn = document.getElementById('adminBtnGuardarEst');
                btn.disabled = true;
                try {
                    const r = await fetch('/api/admin/estudiantes/' + adminSelectedEstId, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                        body: JSON.stringify(payload),
                    });
                    const j = await r.json();
                    if (!r.ok) {
                        errEl.textContent = j.errors ? Object.values(j.errors).flat().join(' ') : (j.message || 'Error');
                        errEl.hidden = false;
                        return;
                    }
                    window.alert(j.message || 'Estudiante actualizado.');
                    resetAdminModal();
                } catch (e) {
                    errEl.textContent = 'Error de red o del servidor.';
                    errEl.hidden = false;
                } finally {
                    btn.disabled = false;
                }
            });

            function syncAdminTipoPagoEdit() {
                const tipo = document.querySelector('input[name="adminTipoPagoEdit"]:checked')?.value;
                const bloque = document.getElementById('adminBloqueNroCuotaEdit');
                const sel = document.getElementById('adminNroCuotaEdit');
                if (tipo === 'Cuota') {
                    bloque.hidden = false;
                    sel.setAttribute('required', 'required');
                } else {
                    bloque.hidden = true;
                    sel.removeAttribute('required');
                    sel.value = '';
                }
            }

            document.querySelectorAll('input[name="adminTipoPagoEdit"]').forEach((r) => {
                r.addEventListener('change', syncAdminTipoPagoEdit);
            });

            document.getElementById('adminBtnIrEditarPago').addEventListener('click', () => {
                showAdminPanel('edit_pago');
                document.getElementById('adminErrorEditarPago').hidden = true;
            });
            document.getElementById('adminBtnVolverDesdeEditarPago').addEventListener('click', () => showAdminPanel('menu'));

            document.getElementById('adminBtnBuscarEstPago').addEventListener('click', async () => {
                const errEl = document.getElementById('adminErrorEditarPago');
                errEl.hidden = true;
                const q = document.getElementById('adminTextoBusqPago').value.trim();
                if (!q) {
                    errEl.textContent = 'Ingrese un término de búsqueda.';
                    errEl.hidden = false;
                    return;
                }
                const lista = document.getElementById('adminListaEstPago');
                const msg = document.getElementById('adminMsgEstPago');
                lista.innerHTML = '<p class="hint" style="padding:12px">Buscando…</p>';
                lista.hidden = false;
                msg.hidden = true;
                adminSelectedPagoId = null;
                adminSelectedEstPagoNombre = '';
                document.getElementById('adminBloqueListaPagos').hidden = true;
                document.getElementById('adminBloqueFormPago').hidden = true;
                document.getElementById('adminBtnGuardarPago').disabled = true;
                try {
                    const params = new URLSearchParams({
                        campo: document.getElementById('adminCampoBusqPago').value,
                        q,
                    });
                    const r = await fetch('/api/estudiantes/base/buscar?' + params.toString(), {
                        headers: { Accept: 'application/json' },
                    });
                    const j = await r.json();
                    if (!r.ok) {
                        lista.innerHTML = '';
                        lista.hidden = true;
                        msg.hidden = false;
                        errEl.textContent = j.errors ? Object.values(j.errors).flat().join(' ') : (j.message || 'Error');
                        errEl.hidden = false;
                        return;
                    }
                    const rows = j.data || [];
                    lista.innerHTML = '';
                    if (!rows.length) {
                        lista.innerHTML = '<p class="hint" style="padding:12px">No hay resultados.</p>';
                        return;
                    }
                    rows.forEach((row) => {
                        const b = document.createElement('button');
                        b.type = 'button';
                        b.className = 'search-result-item';
                        b.textContent = row.nombre_completo + ' — Reg. ' + row.RegistroE + ' — CI ' + row.CedulaE;
                        b.addEventListener('click', async () => {
                            lista.querySelectorAll('.search-result-item').forEach((x) => x.classList.remove('selected'));
                            b.classList.add('selected');
                            adminSelectedEstPagoNombre = row.nombre_completo;
                            errEl.hidden = true;
                            const wrap = document.getElementById('adminBloqueListaPagos');
                            const items = document.getElementById('adminListaPagosItems');
                            items.innerHTML = '<p class="hint" style="padding:12px">Cargando pagos…</p>';
                            wrap.hidden = false;
                            document.getElementById('adminBloqueFormPago').hidden = true;
                            adminSelectedPagoId = null;
                            document.getElementById('adminBtnGuardarPago').disabled = true;
                            try {
                                const r2 = await fetch('/api/admin/estudiantes/' + row.Id_E + '/pagos', {
                                    headers: { Accept: 'application/json' },
                                });
                                const j2 = await r2.json();
                                if (!r2.ok) {
                                    items.innerHTML = '';
                                    wrap.hidden = true;
                                    errEl.textContent = j2.message || 'No se pudieron cargar los pagos.';
                                    errEl.hidden = false;
                                    return;
                                }
                                const pagos = j2.data || [];
                                items.innerHTML = '';
                                if (!pagos.length) {
                                    items.innerHTML = '<p class="hint" style="padding:12px">Este estudiante no tiene pagos registrados.</p>';
                                    return;
                                }
                                pagos.forEach((p) => {
                                    const pb = document.createElement('button');
                                    pb.type = 'button';
                                    pb.className = 'search-result-item';
                                    const tipoLabel =
                                        p.TipoP === 'Cuota' ? 'Cuota ' + p.NroP : p.TipoP === 'Matricula' ? 'Matrícula' : 'Defensa';
                                    pb.textContent =
                                        p.curso_etiqueta +
                                        ' — ' +
                                        tipoLabel +
                                        ' — Bs ' +
                                        Number(p.MontoP).toFixed(2) +
                                        ' — ' +
                                        p.FechaP;
                                    pb.addEventListener('click', () => {
                                        items.querySelectorAll('.search-result-item').forEach((x) => x.classList.remove('selected'));
                                        pb.classList.add('selected');
                                        adminSelectedPagoId = p.Id_P;
                                        document.getElementById('adminResumenPagoEdit').textContent =
                                            'Estudiante: ' + adminSelectedEstPagoNombre + ' — ' + p.curso_etiqueta;
                                        document.getElementById('adminMontoPagoEdit').value = p.MontoP;
                                        document.querySelectorAll('input[name="adminTipoPagoEdit"]').forEach((radio) => {
                                            radio.checked = radio.value === p.TipoP;
                                        });
                                        if (p.TipoP === 'Cuota') {
                                            document.getElementById('adminNroCuotaEdit').value = String(p.NroP);
                                        } else {
                                            document.getElementById('adminNroCuotaEdit').value = '';
                                        }
                                        document.getElementById('adminFechaPagoEdit').value = p.FechaP;
                                        document.getElementById('adminNroCompEdit').value = p.NroCompP;
                                        document.getElementById('adminCuentaEdit').value = p.CuentaTransfP;
                                        syncAdminTipoPagoEdit();
                                        document.getElementById('adminBloqueFormPago').hidden = false;
                                        document.getElementById('adminBtnGuardarPago').disabled = false;
                                        errEl.hidden = true;
                                    });
                                    items.appendChild(pb);
                                });
                            } catch (e2) {
                                items.innerHTML = '';
                                wrap.hidden = true;
                                errEl.textContent = 'Error al cargar los pagos.';
                                errEl.hidden = false;
                            }
                        });
                        lista.appendChild(b);
                    });
                } catch (e) {
                    lista.innerHTML = '';
                    lista.hidden = true;
                    msg.hidden = false;
                    errEl.textContent = 'No se pudo completar la búsqueda.';
                    errEl.hidden = false;
                }
            });

            function resolverAdminNroP() {
                const tipo = document.querySelector('input[name="adminTipoPagoEdit"]:checked')?.value;
                if (tipo === 'Matricula') return 0;
                if (tipo === 'Defensa') return 6;
                if (tipo === 'Cuota') return Number(document.getElementById('adminNroCuotaEdit').value);
                return null;
            }

            document.getElementById('adminBtnGuardarPago').addEventListener('click', async () => {
                const errEl = document.getElementById('adminErrorEditarPago');
                errEl.hidden = true;
                if (!adminSelectedPagoId) return;
                const tipo = document.querySelector('input[name="adminTipoPagoEdit"]:checked')?.value;
                if (!tipo) {
                    errEl.textContent = 'Seleccione el tipo de pago.';
                    errEl.hidden = false;
                    return;
                }
                if (tipo === 'Cuota' && !document.getElementById('adminNroCuotaEdit').value) {
                    errEl.textContent = 'Seleccione el número de cuota (1 a 5).';
                    errEl.hidden = false;
                    return;
                }
                const monto = document.getElementById('adminMontoPagoEdit').value;
                const fecha = document.getElementById('adminFechaPagoEdit').value;
                const nroComp = document.getElementById('adminNroCompEdit').value;
                const cuenta = document.getElementById('adminCuentaEdit').value.trim();
                if (!monto || Number(monto) <= 0) {
                    errEl.textContent = 'Ingrese un monto válido.';
                    errEl.hidden = false;
                    return;
                }
                if (!fecha) {
                    errEl.textContent = 'Seleccione la fecha.';
                    errEl.hidden = false;
                    return;
                }
                if (!nroComp || Number(nroComp) < 1) {
                    errEl.textContent = 'Ingrese el número de comprobante.';
                    errEl.hidden = false;
                    return;
                }
                if (!cuenta) {
                    errEl.textContent = 'Ingrese el número de cuenta bancaria.';
                    errEl.hidden = false;
                    return;
                }
                const NroP = resolverAdminNroP();
                if (NroP === null || (tipo === 'Cuota' && (NroP < 1 || NroP > 5))) {
                    errEl.textContent = 'Datos de cuota incompletos.';
                    errEl.hidden = false;
                    return;
                }
                const btn = document.getElementById('adminBtnGuardarPago');
                btn.disabled = true;
                try {
                    const r = await fetch('/api/admin/pagos/' + adminSelectedPagoId, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                        body: JSON.stringify({
                            MontoP: Number(monto),
                            TipoP: tipo,
                            NroP,
                            FechaP: fecha,
                            NroCompP: Number(nroComp),
                            CuentaTransfP: cuenta,
                        }),
                    });
                    const j = await r.json();
                    if (!r.ok) {
                        errEl.textContent = j.errors ? Object.values(j.errors).flat().join(' ') : (j.message || 'Error');
                        errEl.hidden = false;
                        return;
                    }
                    window.alert(j.message || 'Pago actualizado.');
                    resetAdminModal();
                } catch (e) {
                    errEl.textContent = 'Error de red o del servidor.';
                    errEl.hidden = false;
                } finally {
                    btn.disabled = false;
                }
            });
        })();
    </script>
</body>
</html>

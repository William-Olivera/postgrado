@extends('layouts.app')

@section('title', 'Expediente Digital')
@section('page-title', 'Expediente Digital del Estudiante')
@section('page-subtitle', 'Gestión y control de documentación para postgrados')

@section('content')
    <div style="max-width: 1200px; margin: 0 auto;">

        <div style="background: rgba(30, 41, 59, 0.4); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.06); padding: 24px; border-radius: 16px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; position: relative;">
            <div>
                <span style="background: rgba(234, 179, 8, 0.15); color: #facc15; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Expediente Activo</span>
                <h2 style="color: #f1f5f9; margin: 8px 0 4px 0; font-size: 22px; font-weight: 600;">{{ $estudiante->paterno }} {{ $estudiante->materno }} {{ $estudiante->nombres }}</h2>
                <p style="color: #94a3b8; font-size: 14px; margin: 0;">
                    <i class="fas fa-id-card" style="margin-right: 6px;"></i> <strong>C.I.:</strong> {{ $estudiante->cedula }} &nbsp;|&nbsp;
                    <i class="fas fa-hashtag" style="margin-right: 6px;"></i> <strong>Registro:</strong> {{ $estudiante->registro }}
                </p>
            </div>

            @if(session('success'))
                <div id="alerta-flotante" style="background: rgba(34, 197, 94, 0.2); backdrop-filter: blur(8px); border: 1px solid rgba(34, 197, 94, 0.4); color: #4ade80; padding: 12px 24px; border-radius: 12px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 10px; transition: opacity 0.5s ease; z-index: 10;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div id="alerta-flotante" style="background: rgba(239, 68, 68, 0.2); backdrop-filter: blur(8px); border: 1px solid rgba(239, 68, 68, 0.4); color: #f87171; padding: 12px 24px; border-radius: 12px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 10px; transition: opacity 0.5s ease; z-index: 10;">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <a href="{{ route('estudiantes.index') }}" class="btn-primary" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); color: #e2e8f0; text-decoration: none; padding: 10px 18px; border-radius: 12px; font-size: 13px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Volver a Estudiantes
            </a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 15fr 25fr)); gap: 24px;">

            <div>
                <div style="background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(255,255,255,0.06); border-radius: 16px; overflow: hidden;">
                    <div style="background: rgba(255,255,255,0.02); padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.06);">
                        <h3 style="font-size: 15px; color: #f1f5f9; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-cloud-upload-alt" style="color: #38bdf8;"></i> Cargar / Actualizar Archivo
                        </h3>
                    </div>

                    <div style="padding: 20px;">
                        <form action="{{ route('documentos.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="estudiante_id" value="{{ $estudiante->id }}">

                            <div style="margin-bottom: 20px;">
                                <label style="display: block; color: #94a3b8; font-size: 13px; margin-bottom: 8px; font-weight: 500;">Tipo de Requisito</label>
                                <select name="tipo" style="width: 100%; padding: 12px; background: #0f172a; border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; color: #f1f5f9; font-size: 14px; outline: none;" required>
                                    <option value="CI">Fotocopia de Cédula de Identidad</option>
                                    <option value="Diploma">Diploma Académico</option>
                                    <option value="Titulo">Título en Provisión Nacional</option>
                                    <option value="Certificado">Certificado de Calificaciones</option>
                                    <option value="Otro">Otros Adjuntos</option>
                                </select>
                            </div>

                            <div style="margin-bottom: 24px;">
                                <label style="display: block; color: #94a3b8; font-size: 13px; margin-bottom: 8px; font-weight: 500;">Archivo Digital (PDF, JPG, PNG)</label>
                                <input type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png" style="width: 100%; padding: 10px; background: rgba(15,23,42,0.3); border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; color: #94a3b8; font-size: 13px;" required>
                                <span style="display: block; color: #64748b; font-size: 11px; margin-top: 6px;"><i class="fas fa-info-circle"></i> Tamaño máximo recomendado: 5MB.</span>
                            </div>

                            <button type="submit" style="width: 100%; justify-content: center; padding: 14px; border-radius: 12px; font-weight: 600; font-size: 14px; background: #3b82f6; color: #fff; cursor: pointer; border: none; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-save"></i> Guardar en Expediente
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div>
                <div style="background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(255,255,255,0.06); border-radius: 16px; overflow: hidden;">
                    <div style="background: rgba(255,255,255,0.02); padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.06);">
                        <h3 style="font-size: 15px; color: #f1f5f9; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-file-invoice" style="color: #eab308;"></i> Estado de la Documentación
                        </h3>
                    </div>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                            <thead>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.06); background: rgba(255,255,255,0.01);">
                                <th style="padding: 14px 20px; color: #94a3b8; font-weight: 500; font-size: 13px;">Requisito Requerido</th>
                                <th style="padding: 14px 20px; color: #94a3b8; font-weight: 500; font-size: 13px;">Estado</th>
                                <th style="padding: 14px 20px; color: #94a3b8; font-weight: 500; font-size: 13px; text-align: center;">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $requisitosLegales = [
                                    'CI' => 'Fotocopia de Cédula de Identidad',
                                    'Diploma' => 'Diploma Académico',
                                    'Titulo' => 'Título en Provisión Nacional',
                                    'Certificado' => 'Certificado de Calificaciones',
                                    'Otro' => 'Otros Adjuntos'
                                ];
                            @endphp

                            @foreach($requisitosLegales as $key => $label)
                                @php
                                    $documento = $documentos->where('tipo', $key)->first();
                                @endphp
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                                    <td style="padding: 16px 20px;">
                                        <div style="color: #e2e8f0; font-weight: 500;">{{ $label }}</div>
                                        @if($documento)
                                            <div style="color: #64748b; font-size: 11px; margin-top: 2px; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                {{ $documento->nombre_archivo }} ({{ round($documento->tamanio_kb, 2) }} KB)
                                            </div>
                                        @endif
                                    </td>
                                    <td style="padding: 16px 20px;">
                                        @if($documento)
                                            <span style="background: rgba(34, 197, 94, 0.15); color: #4ade80; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fas fa-check"></i> Presentado
                                            </span>
                                        @else
                                            <span style="background: rgba(239, 68, 68, 0.15); color: #f87171; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fas fa-times"></i> Faltante
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 16px 20px; text-align: center;">
                                        @if($documento)
                                            <div style="display: inline-flex; gap: 8px; justify-content: center; align-items: center;">

                                                @if(in_array(strtolower($documento->extension), ['jpg', 'jpeg', 'png']))
                                                    <button type="button" onclick="mostrarImagenModal('{{ asset('storage/' . $documento->ruta_archivo) }}', '{{ $label }}')" title="Visualizar imagen" style="color: #38bdf8; background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.2); padding: 6px 10px; border-radius: 8px; font-size: 13px; cursor: pointer; outline: none;">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                @else
                                                    <a href="{{ asset('storage/' . $documento->ruta_archivo) }}" target="_blank" title="Visualizar PDF original" style="color: #38bdf8; background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.2); padding: 6px 10px; border-radius: 8px; font-size: 13px; text-decoration: none; display: inline-flex;">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif

                                                <a href="{{ route('documentos.download', $documento->id) }}" title="Descargar archivo" style="color: #e2e8f0; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 6px 10px; border-radius: 8px; font-size: 13px; text-decoration: none; display: inline-flex;">
                                                    <i class="fas fa-download"></i>
                                                </a>

                                                <button type="button" onclick="confirmarEliminacion('{{ route('documentos.destroy', $documento->id) }}', '{{ $label }}')" title="Quitar archivo" style="color: #f87171; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); padding: 6px 10px; border-radius: 8px; font-size: 13px; cursor: pointer; border: none; outline: none;">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        @else
                                            <span style="color: #475569; font-size: 12px; font-style: italic;">Sin registro</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="imagenVisorModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(12px); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
        <div style="background: #1e293b; border: 1px solid rgba(255,255,255,0.1); max-width: 750px; width: 100%; border-radius: 16px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
            <div style="padding: 16px 20px; background: rgba(255,255,255,0.02); border-bottom: 1px solid rgba(255,255,255,0.08); display: flex; justify-content: space-between; align-items: center;">
                <h4 id="modalTituloDocumento" style="margin: 0; color: #f1f5f9; font-size: 16px; font-weight: 600;">Visualizador de Requisito</h4>
                <button type="button" onclick="cerrarImagenModal()" style="background: none; border: none; color: #94a3b8; font-size: 20px; cursor: pointer; outline: none;"><i class="fas fa-times"></i></button>
            </div>
            <div style="padding: 20px; text-align: center; max-height: 75vh; overflow-y: auto; background: #0f172a; display: flex; justify-content: center; align-items: center;">
                <img id="modalImagenSrc" src="" alt="Documento" style="max-width: 100%; max-height: 65vh; object-fit: contain; border-radius: 8px;">
            </div>
        </div>
    </div>

    <div id="eliminarConfirmModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(8px); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
        <div style="background: #1e293b; border: 1px solid rgba(239, 68, 68, 0.2); max-width: 450px; width: 100%; border-radius: 16px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.6);">
            <div style="padding: 24px; text-align: center; background: #0f172a;">
                <div style="width: 56px; height: 56px; background: rgba(239, 68, 68, 0.15); color: #ef4444; border-radius: 50%; display: flex; justify-content: center; align-items: center; margin: 0 auto 16px auto; font-size: 24px;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h4 style="margin: 0 0 8px 0; color: #f1f5f9; font-size: 18px; font-weight: 600;">¿Eliminar requisito?</h4>
                <p style="margin: 0 0 24px 0; color: #94a3b8; font-size: 14px; line-height: 1.5;">
                    Estás a punto de quitar el archivo de <strong id="nombreDocEliminar" style="color: #f1f5f9;"></strong>. Esta acción no se puede deshacer.
                </p>

                <form id="formConfirmarEliminar" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <div style="display: flex; gap: 12px; justify-content: center;">
                        <button type="button" onclick="cerrarEliminarModal()" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); color: #e2e8f0; padding: 10px 20px; border-radius: 10px; font-size: 14px; font-weight: 500; cursor: pointer; outline: none;">
                            Cancelar
                        </button>
                        <button type="submit" style="background: #ef4444; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; outline: none;">
                            Sí, eliminar archivo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const alerta = document.getElementById('alerta-flotante');
            if (alerta) {
                setTimeout(function() {
                    alerta.style.opacity = '0';
                    setTimeout(function() { alerta.remove(); }, 500);
                }, 3500);
            }
        });

        // Funciones del Visor de Imágenes
        function mostrarImagenModal(url, titulo) {
            document.getElementById('modalTituloDocumento').innerText = titulo;
            document.getElementById('modalImagenSrc').src = url;
            document.getElementById('imagenVisorModal').style.display = 'flex';
        }

        function cerrarImagenModal() {
            document.getElementById('imagenVisorModal').style.display = 'none';
            document.getElementById('modalImagenSrc').src = '';
        }

        // Funciones de la Modal de Eliminación Estilizada
        function confirmarEliminacion(urlAccion, nombreDocumento) {
            document.getElementById('nombreDocEliminar').innerText = nombreDocumento;
            document.getElementById('formConfirmarEliminar').action = urlAccion;
            document.getElementById('eliminarConfirmModal').style.display = 'flex';
        }

        function cerrarEliminarModal() {
            document.getElementById('eliminarConfirmModal').style.display = 'none';
            document.getElementById('formConfirmarEliminar').action = '';
        }
    </script>
@endsection

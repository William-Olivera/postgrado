@extends('layouts.app')

@section('title', 'Ver Estudiante')
@section('page-title', 'Ver Estudiante')
@section('page-subtitle', 'Detalles del estudiante')

@section('content')
<div class="card" style="max-width: 700px;">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
        <div>
            <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 4px;">Nombre Completo</p>
            <p style="font-weight: 600; font-size: 1.1rem;">{{ $estudiante->nombreCompleto() }}</p>
        </div>
        <div>
            <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 4px;">Cedula de Identidad</p>
            <p style="font-weight: 600;">{{ $estudiante->CedulaE }}</p>
        </div>
        <div>
            <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 4px;">Numero de Registro</p>
            <p style="font-weight: 600;">{{ $estudiante->RegistroE }}</p>
        </div>
        <div>
            <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 4px;">Telefono</p>
            <p style="font-weight: 600;">{{ $estudiante->TelefonoE ?: '-' }}</p>
        </div>
        <div>
            <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 4px;">Direccion</p>
            <p style="font-weight: 600;">{{ $estudiante->DireccionE ?: '-' }}</p>
        </div>
        <div>
            <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 4px;">Descuento</p>
            <p style="font-weight: 600;">{{ $estudiante->DescuentoE }}%</p>
        </div>
        <div>
            <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 4px;">Observacion</p>
            <p style="font-weight: 600;">{{ $estudiante->ObservacionE ?: '-' }}</p>
        </div>
        <div>
            <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 4px;">Estado</p>
            <p>
                @if($estudiante->ActivoE)
                    <span class="badge badge-success">Activo</span>
                @else
                    <span class="badge badge-danger">Inactivo</span>
                @endif
            </p>
        </div>
    </div>

    <div style="border-top: 1px solid var(--border-color); padding-top: 20px;">
        <h3 style="font-size: 1rem; margin-bottom: 16px; color: var(--text-primary);">Inscripciones</h3>
        @if($estudiante->cursos->count() > 0)
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>CURSO</th>
                        <th>TIPO</th>
                        <th>VERSION</th>
                        <th>EDICION</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($estudiante->cursos as $curso)
                    <tr>
                        <td>{{ $curso->NombreCur }}</td>
                        <td>{{ $curso->TipoCur }}</td>
                        <td>{{ $curso->VersionCur }}</td>
                        <td>{{ $curso->EdicionCur }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p style="color: var(--text-muted);">No tiene inscripciones registradas.</p>
        @endif
    </div>

    <div class="form-actions" style="margin-top: 24px;">
        <a href="{{ route('estudiantes.index') }}" class="btn-secondary">Volver</a>
        <a href="{{ route('estudiantes.edit', $estudiante->Id_E) }}" class="btn-primary">Editar</a>
    </div>
</div>
@endsection
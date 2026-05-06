@extends('layouts.app')

@section('title', 'Editar Estudiante')
@section('page-title', 'Editar Estudiante')
@section('page-subtitle', 'Modificar datos del estudiante')

@section('content')
<div class="card" style="max-width: 700px;">
    <form action="{{ route('estudiantes.update', $estudiante->Id_E) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-row">
            <div class="form-group">
                <label>Nombre <span class="required">*</span></label>
                <input type="text" name="nombreE" class="form-control" value="{{ old('nombreE', $estudiante->nombreE) }}" required maxlength="50">
                @error('nombreE')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Apellido Paterno <span class="required">*</span></label>
                <input type="text" name="paternoE" class="form-control" value="{{ old('paternoE', $estudiante->paternoE) }}" required maxlength="50">
                @error('paternoE')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Apellido Materno</label>
                <input type="text" name="maternoE" class="form-control" value="{{ old('maternoE', $estudiante->maternoE) }}" maxlength="50">
                @error('maternoE')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Cedula de Identidad <span class="required">*</span></label>
                <input type="text" name="CedulaE" class="form-control" value="{{ old('CedulaE', $estudiante->CedulaE) }}" required maxlength="20">
                @error('CedulaE')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Numero de Registro <span class="required">*</span></label>
                <input type="number" name="RegistroE" class="form-control" value="{{ old('RegistroE', $estudiante->RegistroE) }}" required min="1">
                @error('RegistroE')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Telefono</label>
                <input type="text" name="TelefonoE" class="form-control" value="{{ old('TelefonoE', $estudiante->TelefonoE) }}" maxlength="20">
                @error('TelefonoE')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Direccion</label>
                <input type="text" name="DireccionE" class="form-control" value="{{ old('DireccionE', $estudiante->DireccionE) }}" maxlength="100">
                @error('DireccionE')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Descuento (%)</label>
                <input type="number" name="DescuentoE" class="form-control" value="{{ old('DescuentoE', $estudiante->DescuentoE) }}" min="0" max="100">
                @error('DescuentoE')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-group">
            <label>Observacion</label>
            <input type="text" name="ObservacionE" class="form-control" value="{{ old('ObservacionE', $estudiante->ObservacionE) }}" maxlength="50">
            @error('ObservacionE')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label>Estado</label>
            <div class="radio-group">
                <label class="radio-label">
                    <input type="radio" name="ActivoE" value="1" {{ old('ActivoE', $estudiante->ActivoE) ? 'checked' : '' }}> Activo
                </label>
                <label class="radio-label">
                    <input type="radio" name="ActivoE" value="0" {{ !old('ActivoE', $estudiante->ActivoE) ? 'checked' : '' }}> Inactivo
                </label>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('estudiantes.index') }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary">Actualizar Estudiante</button>
        </div>
    </form>
</div>
@endsection
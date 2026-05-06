@extends('layouts.app')

@section('title', 'Nuevo Estudiante')
@section('page-title', 'Nuevo Estudiante')
@section('page-subtitle', 'Registrar datos del estudiante')

@section('content')
<div class="card" style="max-width: 700px;">
    <form action="{{ route('estudiantes.store') }}" method="POST">
        @csrf
        
        <div class="form-row">
            <div class="form-group">
                <label>Nombre <span class="required">*</span></label>
                <input type="text" name="nombreE" class="form-control" value="{{ old('nombreE') }}" required maxlength="50">
                @error('nombreE')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Apellido Paterno <span class="required">*</span></label>
                <input type="text" name="paternoE" class="form-control" value="{{ old('paternoE') }}" required maxlength="50">
                @error('paternoE')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Apellido Materno</label>
                <input type="text" name="maternoE" class="form-control" value="{{ old('maternoE') }}" maxlength="50">
                @error('maternoE')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Cedula de Identidad <span class="required">*</span></label>
                <input type="text" name="CedulaE" class="form-control" value="{{ old('CedulaE') }}" required maxlength="20">
                @error('CedulaE')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Numero de Registro <span class="required">*</span></label>
                <input type="number" name="RegistroE" class="form-control" value="{{ old('RegistroE') }}" required min="1">
                @error('RegistroE')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Telefono</label>
                <input type="text" name="TelefonoE" class="form-control" value="{{ old('TelefonoE') }}" maxlength="20">
                @error('TelefonoE')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Direccion</label>
                <input type="text" name="DireccionE" class="form-control" value="{{ old('DireccionE') }}" maxlength="100">
                @error('DireccionE')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Descuento (%)</label>
                <input type="number" name="DescuentoE" class="form-control" value="{{ old('DescuentoE', 0) }}" min="0" max="100">
                @error('DescuentoE')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-group">
            <label>Observacion</label>
            <input type="text" name="ObservacionE" class="form-control" value="{{ old('ObservacionE') }}" maxlength="50">
            @error('ObservacionE')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-actions">
            <a href="{{ route('estudiantes.index') }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary">Guardar Estudiante</button>
        </div>
    </form>
</div>
@endsection
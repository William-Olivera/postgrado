@extends('layouts.app')

@section('title', 'Nuevo Curso')
@section('page-title', 'Nuevo Curso')
@section('page-subtitle', 'Registrar nuevo curso')

@section('content')
<div class="card" style="max-width: 700px;">
    <form action="{{ route('cursos.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label>Nombre del Curso <span class="required">*</span></label>
            <input type="text" name="NombreCur" class="form-control" value="{{ old('NombreCur') }}" required maxlength="100">
            @error('NombreCur')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Tipo <span class="required">*</span></label>
                <select name="TipoCur" class="form-control" required>
                    <option value="">Seleccione...</option>
                    <option value="Diplomado" {{ old('TipoCur') == 'Diplomado' ? 'selected' : '' }}>Diplomado</option>
                    <option value="Especialidad" {{ old('TipoCur') == 'Especialidad' ? 'selected' : '' }}>Especialidad</option>
                    <option value="Maestria" {{ old('TipoCur') == 'Maestria' ? 'selected' : '' }}>Maestria</option>
                    <option value="Doctorado" {{ old('TipoCur') == 'Doctorado' ? 'selected' : '' }}>Doctorado</option>
                </select>
                @error('TipoCur')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Version <span class="required">*</span></label>
                <input type="text" name="VersionCur" class="form-control" value="{{ old('VersionCur', 1) }}" required pattern="\d+" title="Solo números">
                @error('VersionCur')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Edicion <span class="required">*</span></label>
                <input type="text" name="EdicionCur" class="form-control" value="{{ old('EdicionCur') }}" required maxlength="20">
                @error('EdicionCur')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Duracion (horas) <span class="required">*</span></label>
                <input type="text" name="DuracionCur" class="form-control" value="{{ old('DuracionCur') }}" required pattern="\d+" title="Solo números">
                @error('DuracionCur')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Cupo <span class="required">*</span></label>
                <input type="text" name="CupoCur" class="form-control" value="{{ old('CupoCur') }}" required pattern="\d+" title="Solo números">
                @error('CupoCur')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Periodo <span class="required">*</span></label>
                <input type="text" name="PeriodoCur" class="form-control" value="{{ old('PeriodoCur') }}" required maxlength="50">
                @error('PeriodoCur')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Costo Total (Bs) <span class="required">*</span></label>
                <input type="text" name="CostoCur" step="0.01" class="form-control" value="{{ old('CostoCur') }}" required pattern="\d+(\.\d{1,2})?" title="Solo números y decimales">
                @error('CostoCur')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-group">
            <label>Descripcion</label>
            <textarea name="DescripcionCur" class="form-control" rows="3" maxlength="500">{{ old('DescripcionCur') }}</textarea>
            @error('DescripcionCur')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <h3 style="font-size: 1rem; margin: 24px 0 16px; color: var(--text-primary); border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">Plan de Pago</h3>

        <div class="form-row">
            <div class="form-group">
                <label>Monto Matricula (Bs) <span class="required">*</span></label>
                <input type="text" name="MontoMatriculaPP" step="0.01" min="0.01" class="form-control" value="{{ old('MontoMatriculaPP') }}" required pattern="\d+(\.\d{1,2})?" title="Solo números y decimales">
                @error('MontoMatriculaPP')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Monto por Cuota (Bs) <span class="required">*</span></label>
                <input type="text" name="MontoCuotaPP" step="0.01" min="0.01" class="form-control" value="{{ old('MontoCuotaPP') }}" required pattern="\d+(\.\d{1,2})?" title="Solo números y decimales">
                @error('MontoCuotaPP')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Numero de Cuotas <span class="required">*</span></label>
                <input type="text" name="NroCuotasPP" class="form-control" value="{{ old('NroCuotasPP', 5) }}" required pattern="\d+" title="Solo números">
                @error('NroCuotasPP')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('cursos.index') }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary" onclick="console.log('Form submission clicked')">Guardar Curso</button>
        </div>
    </form>

    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            console.log('Form submitting...');
            console.log('Form data:', new FormData(this));
        });
    </script>
</div>
@endsection
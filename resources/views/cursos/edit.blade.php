@extends('layouts.app')

@section('title', 'Editar Curso')
@section('page-title', 'Editar Curso')
@section('page-subtitle', 'Modificar datos del curso')

@section('content')
<div class="card" style="max-width: 700px;">
    <form action="{{ route('cursos.update', $curso->Id_Cur) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label>Nombre del Curso <span class="required">*</span></label>
            <input type="text" name="NombreCur" class="form-control" value="{{ old('NombreCur', $curso->NombreCur) }}" required maxlength="100">
            @error('NombreCur')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Tipo <span class="required">*</span></label>
                <select name="TipoCur" class="form-control" required>
                    <option value="">Seleccione...</option>
                    <option value="Diplomado" {{ old('TipoCur', $curso->TipoCur) == 'Diplomado' ? 'selected' : '' }}>Diplomado</option>
                    <option value="Especialidad" {{ old('TipoCur', $curso->TipoCur) == 'Especialidad' ? 'selected' : '' }}>Especialidad</option>
                    <option value="Maestria" {{ old('TipoCur', $curso->TipoCur) == 'Maestria' ? 'selected' : '' }}>Maestria</option>
                    <option value="Doctorado" {{ old('TipoCur', $curso->TipoCur) == 'Doctorado' ? 'selected' : '' }}>Doctorado</option>
                </select>
                @error('TipoCur')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Version <span class="required">*</span></label>
                <input type="text" name="VersionCur" class="form-control" value="{{ old('VersionCur', $curso->VersionCur) }}" required pattern="\d+" title="Solo números">
                @error('VersionCur')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Edicion <span class="required">*</span></label>
                <input type="text" name="EdicionCur" class="form-control" value="{{ old('EdicionCur', $curso->EdicionCur) }}" required maxlength="20">
                @error('EdicionCur')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Duracion (horas) <span class="required">*</span></label>
                <input type="text" name="DuracionCur" class="form-control" value="{{ old('DuracionCur', $curso->DuracionCur) }}" required pattern="\d+" title="Solo números">
                @error('DuracionCur')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Cupo <span class="required">*</span></label>
                <input type="text" name="CupoCur" class="form-control" value="{{ old('CupoCur', $curso->CupoCur) }}" required pattern="\d+" title="Solo números">
                @error('CupoCur')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Periodo <span class="required">*</span></label>
                <input type="text" name="PeriodoCur" class="form-control" value="{{ old('PeriodoCur', $curso->PeriodoCur) }}" required maxlength="50">
                @error('PeriodoCur')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Costo Total (Bs) <span class="required">*</span></label>
                <input type="text" name="CostoCur" step="0.01" class="form-control" value="{{ old('CostoCur', $curso->CostoCur) }}" required pattern="\d+(\.\d{1,2})?" title="Solo números y decimales">
                @error('CostoCur')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-group">
            <label>Descripcion</label>
            <textarea name="DescripcionCur" class="form-control" rows="3" maxlength="500">{{ old('DescripcionCur', $curso->DescripcionCur) }}</textarea>
            @error('DescripcionCur')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <h3 style="font-size: 1rem; margin: 24px 0 16px; color: var(--text-primary); border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">Plan de Pago</h3>

        <div class="form-row">
            <div class="form-group">
                <label>Monto Total (Bs) <span class="required">*</span></label>
                <input type="text" name="MontoTotalPP" step="0.01" min="0.01" class="form-control" value="{{ old('MontoTotalPP', $plan->MontoTotalPP) }}" required pattern="\d+(\.\d{1,2})?" title="Solo números y decimales">
                @error('MontoTotalPP')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Monto Matricula (Bs) <span class="required">*</span></label>
                <input type="text" name="MontoMatriculaPP" step="0.01" min="0.01" class="form-control" value="{{ old('MontoMatriculaPP', $plan->MontoMatriculaPP) }}" required pattern="\d+(\.\d{1,2})?" title="Solo números y decimales">
                @error('MontoMatriculaPP')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Monto por Cuota (Bs) <span class="required">*</span></label>
                <input type="text" name="MontoCuotaPP" step="0.01" min="0.01" class="form-control" value="{{ old('MontoCuotaPP', $plan->MontoCuotaPP) }}" required pattern="\d+(\.\d{1,2})?" title="Solo números y decimales">
                @error('MontoCuotaPP')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>Numero de Cuotas <span class="required">*</span></label>
                <input type="text" name="NroCuotasPP" class="form-control" value="{{ old('NroCuotasPP', $plan->NroCuotasPP) }}" required pattern="\d+" title="Solo números">
                @error('NroCuotasPP')<div class="form-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('cursos.index') }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary">Actualizar Curso</button>
        </div>
    </form>
</div>
@endsection
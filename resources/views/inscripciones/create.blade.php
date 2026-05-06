@extends('layouts.app')

@section('title', 'Nueva Inscripcion')
@section('page-title', 'Nueva Inscripcion')
@section('page-subtitle', 'Inscribir estudiante a un curso')

@section('content')
<div class="card" style="max-width: 600px;">
    <form action="{{ route('inscripciones.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label>Período académico activo</label>
            <input type="text" class="form-control" value="{{ $activePeriod }}" readonly>
        </div>

        <div class="form-group">
            <label>Estudiante <span class="required">*</span></label>
            <input type="text" id="estudianteSearch" class="form-control" placeholder="Buscar por nombre, CI o registro..." autocomplete="off"
                value="{{ old('Id_E') ? optional($estudiantes->firstWhere('Id_E', old('Id_E')))->nombreCompleto() . ' — CI: ' . optional($estudiantes->firstWhere('Id_E', old('Id_E')))->CedulaE . ' — Reg: ' . optional($estudiantes->firstWhere('Id_E', old('Id_E')))->RegistroE : '' }}">
            <input type="hidden" name="Id_E" id="Id_E" value="{{ old('Id_E') }}">
            <div id="estudianteSuggestions" class="suggestions"></div>
            @error('Id_E')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label>Curso <span class="required">*</span></label>
            <select name="Id_Cur" class="form-control" required {{ $cursos->isEmpty() ? 'disabled' : '' }}>
                <option value="">Seleccione un curso...</option>
                @foreach($cursos as $curso)
                    <option value="{{ $curso->Id_Cur }}" {{ old('Id_Cur') == $curso->Id_Cur ? 'selected' : '' }}>
                        {{ $curso->NombreCur }}
                    </option>
                @endforeach
            </select>
            @error('Id_Cur')<div class="form-error">{{ $message }}</div>@enderror
            @if($cursos->isEmpty())
                <div class="form-error">No hay cursos disponibles en el período activo {{ $activePeriod }}.</div>
            @endif
        </div>

        <div class="form-group">
            <label>Fecha de Inscripcion <span class="required">*</span></label>
            <input type="date" name="FechaIns" class="form-control" value="{{ old('FechaIns', date('Y-m-d')) }}" required>
            @error('FechaIns')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label>Estado</label>
            <div class="radio-group">
                <label class="radio-label">
                    <input type="radio" name="EstadoIns" value="Completada" {{ old('EstadoIns', 'Completada') == 'Completada' ? 'checked' : '' }}> Completada
                </label>
                <label class="radio-label">
                    <input type="radio" name="EstadoIns" value="Pendiente" {{ old('EstadoIns') == 'Pendiente' ? 'checked' : '' }}> Pendiente
                </label>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('inscripciones.index') }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary" {{ $cursos->isEmpty() ? 'disabled' : '' }}>Inscribir Estudiante</button>
        </div>
    </form>
</div>

<style>
    #estudianteSearch {
        margin-bottom: 10px;
    }
    .suggestions {
        margin-top: 4px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        background: var(--card-bg);
        max-height: 220px;
        overflow-y: auto;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        z-index: 10;
    }
    .suggestion-item {
        width: 100%;
        text-align: left;
        padding: 10px 12px;
        border: none;
        background: transparent;
        cursor: pointer;
        color: var(--text-primary);
        font-size: 14px;
    }
    .suggestion-item:hover,
    .suggestion-item:focus {
        background: rgba(34, 197, 94, 0.08);
    }
    .suggestion-empty {
        padding: 10px 12px;
        color: var(--text-secondary);
        font-size: 14px;
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const busqueda = document.getElementById('estudianteSearch');
        const hiddenId = document.getElementById('Id_E');
        const suggestions = document.getElementById('estudianteSuggestions');
        if (!busqueda || !hiddenId || !suggestions) {
            return;
        }

        @php
            $estudiantesData = $estudiantes->map(function ($estudiante) {
                return [
                    'id' => $estudiante->Id_E,
                    'label' => $estudiante->nombreCompleto(),
                    'ci' => $estudiante->CedulaE,
                    'registro' => $estudiante->RegistroE,
                ];
            })->toArray();
        @endphp

        const estudiantes = @json($estudiantesData);

        function renderSuggestions(term) {
            const query = term.trim().toLowerCase();
            suggestions.innerHTML = '';
            if (query === '') {
                return;
            }

            const matches = estudiantes.filter(est =>
                est.label.toLowerCase().includes(query) ||
                String(est.ci).toLowerCase().includes(query) ||
                String(est.registro).toLowerCase().includes(query)
            );

            if (matches.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'suggestion-empty';
                empty.textContent = 'No hay resultados.';
                suggestions.appendChild(empty);
                return;
            }

            matches.slice(0, 8).forEach(est => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'suggestion-item';
                item.textContent = `${est.label} — CI ${est.ci} — Reg. ${est.registro}`;
                item.addEventListener('click', function () {
                    busqueda.value = item.textContent;
                    hiddenId.value = est.id;
                    suggestions.innerHTML = '';
                });
                suggestions.appendChild(item);
            });
        }

        busqueda.addEventListener('input', function () {
            hiddenId.value = '';
            renderSuggestions(this.value);
        });

        busqueda.addEventListener('focus', function () {
            renderSuggestions(this.value);
        });

        document.addEventListener('click', function (event) {
            if (!suggestions.contains(event.target) && event.target !== busqueda) {
                suggestions.innerHTML = '';
            }
        });
    });
</script>
@endpush
@endsection
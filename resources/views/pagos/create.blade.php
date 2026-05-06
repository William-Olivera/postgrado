@extends('layouts.app')

@section('page-title', 'Nuevo Pago')
@section('page-subtitle', 'Registrar un nuevo pago')

@section('content')
<div class="page-container">
    <div class="form-container">
        <form action="{{ route('pagos.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="estudianteSearch">Estudiante</label>
                <input type="text" id="estudianteSearch" class="form-control" placeholder="Buscar por nombre, CI o registro..." autocomplete="off">
                <input type="hidden" name="Id_E" id="Id_E" value="{{ old('Id_E') }}">
                <div id="estudianteSuggestions" class="suggestions"></div>
            </div>

            <div class="form-group">
                <label for="curso">Curso</label>
                <select name="Id_Cur" id="curso" required class="form-control">
                    <option value="">Seleccionar curso...</option>
                    @foreach($cursos as $curso)
                        <option value="{{ $curso->Id_Cur }}">{{ $curso->NombreCur }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="monto">Monto (Bs)</label>
                <input type="number" name="MontoP" id="monto" required step="0.01" class="form-control" placeholder="0.00">
            </div>

            <div class="form-group">
                <label for="tipo">Tipo de Pago</label>
                <select name="TipoP" id="tipo" required class="form-control">
                    <option value="Matricula" {{ old('TipoP') === 'Matricula' ? 'selected' : '' }}>Matrícula</option>
                    <option value="Cuota" {{ old('TipoP') === 'Cuota' ? 'selected' : '' }}>Cuota</option>
                    <option value="Defensa" {{ old('TipoP') === 'Defensa' ? 'selected' : '' }}>Defensa</option>
                </select>
                @error('TipoP')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="nrop">Número de Pago</label>
                <input type="number" name="NroP" id="nrop" required class="form-control" min="0" max="6" value="{{ old('NroP') }}" placeholder="0">
                @error('NroP')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="comprobante">Número de comprobante</label>
                <input type="number" name="NroCompP" id="comprobante" required class="form-control" min="1" value="{{ old('NroCompP') }}" placeholder="12345">
                @error('NroCompP')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="cuenta">Cuenta de transferencia</label>
                <input type="text" name="CuentaTransfP" id="cuenta" required class="form-control" value="{{ old('CuentaTransfP') }}" placeholder="Número o banco">
                @error('CuentaTransfP')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="fecha">Fecha</label>
                <input type="date" name="FechaP" id="fecha" required class="form-control" value="{{ old('FechaP') }}">
                @error('FechaP')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar Pago</button>
                <a href="{{ route('pagos.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<style>
    .page-container {
        padding: 20px;
    }
    .form-container {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 30px;
        max-width: 600px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--text-primary);
    }
    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        background: var(--card-bg);
        color: var(--text-primary);
        font-size: 14px;
    }
    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
    }
    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }
    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        flex: 1;
    }
    .btn-primary {
        background: var(--primary);
        color: white;
    }
    .btn-secondary {
        background: var(--border-color);
        color: var(--text-primary);
    }
    .error-text {
        display: block;
        margin-top: 6px;
        font-size: 12px;
        color: var(--danger);
    }
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
                    busqueda.value = est.label;
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
            if (!busqueda.contains(event.target) && !suggestions.contains(event.target)) {
                suggestions.innerHTML = '';
            }
        });
    });
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Estudiantes')
@section('page-title', 'Estudiantes')
@section('page-subtitle', 'Gestion de estudiantes del post-grado')

@section('content')
<div class="page-header">
    <div class="filters">
        <form action="{{ route('estudiantes.index') }}" method="GET" class="filter-form" onsubmit="return false;">
            <input type="text" id="estudianteSearch" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre, CI, registro..." class="filter-input" autocomplete="off">
            <button type="submit" class="btn-filter" onclick="applyEstudianteFilter();">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            </button>
        </form>
    </div>
    <a href="{{ route('estudiantes.create') }}" class="btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nuevo Estudiante
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>REGISTRO</th>
                    <th>CI</th>
                    <th>NOMBRE COMPLETO</th>
                    <th>TELEFONO</th>
                    <th>DIRECCION</th>
                    <th>DESCUENTO</th>
                    <th>OBSERVACION</th>
                    <th>ESTADO</th>
                    <th>INSCRIPCIONES</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse($estudiantes as $estudiante)
                <tr>
                    <td>{{ $estudiante->RegistroE }}</td>
                    <td>{{ $estudiante->CedulaE }}</td>
                    <td class="font-medium">{{ $estudiante->nombreCompleto() }}</td>
                    <td>{{ $estudiante->TelefonoE ?: '-' }}</td>
                    <td>{{ $estudiante->DireccionE ?: '-' }}</td>
                    <td>{{ $estudiante->DescuentoE }}%</td>
                    <td>{{ $estudiante->ObservacionE ?: '-' }}</td>
                    <td>
                        @if($estudiante->ActivoE)
                            <span class="badge badge-success">Activo</span>
                        @else
                            <span class="badge badge-danger">Inactivo</span>
                        @endif
                    </td>
                    <td>{{ $estudiante->cursos->count() }}</td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('estudiantes.show', $estudiante->Id_E) }}" class="btn-icon" title="Ver">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <a href="{{ route('estudiantes.edit', $estudiante->Id_E) }}" class="btn-icon" title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <form action="{{ route('estudiantes.destroy', $estudiante->Id_E) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Esta seguro de dar de baja a este estudiante?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon btn-danger" title="Dar de baja">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center">No hay estudiantes registrados</td>
                </tr>
                @endforelse
                <tr id="no-results-row" style="display:none;">
                    <td colspan="10" class="text-center">No se encontraron estudiantes que coincidan con la búsqueda.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="pagination">
        {{ $estudiantes->links() }}
    </div>
</div>

<script>
    function applyEstudianteFilter() {
        const searchInput = document.getElementById('estudianteSearch');
        const query = searchInput.value.trim().toLowerCase();
        const rows = document.querySelectorAll('.data-table tbody tr');
        let visibleCount = 0;

        rows.forEach(row => {
            if (row.id === 'no-results-row') {
                return;
            }

            const text = row.textContent.toLowerCase();
            const match = query === '' || text.includes(query);
            row.style.display = match ? '' : 'none';

            if (match) {
                visibleCount++;
            }
        });

        const noResultsRow = document.getElementById('no-results-row');
        noResultsRow.style.display = visibleCount === 0 ? '' : 'none';
    }

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('estudianteSearch');
        if (searchInput) {
            searchInput.addEventListener('input', applyEstudianteFilter);
            if (searchInput.value.trim() !== '') {
                applyEstudianteFilter();
            }
        }
    });
</script>
@endsection
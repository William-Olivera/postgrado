@extends('layouts.app')

@section('title', 'Cursos')
@section('page-title', 'Cursos')
@section('page-subtitle', 'Catalogo de cursos activos')

@section('content')
<div class="page-header">
    <div class="filters">
        <form action="{{ route('cursos.index') }}" method="GET" class="filter-form">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar curso..." class="filter-input">
            <button type="submit" class="btn-filter">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            </button>
        </form>
    </div>
    <a href="{{ route('cursos.create') }}" class="btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nuevo Curso
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>NOMBRE</th>
                    <th>PERIODO</th>
                    <th>DURACION</th>
                    <th>COSTO</th>
                    <th>CUPO</th>
                    <th>DISPONIBLES</th>
                    <th>INSCRITOS</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cursos as $curso)
                <tr>
                    <td class="font-medium">{{ $curso->NombreCur }}</td>
                    <td>{{ $curso->PeriodoCur }}</td>
                    <td>{{ $curso->DuracionCur }} hrs</td>
                    <td>Bs {{ number_format($curso->CostoCur, 2) }}</td>
                    <td>{{ $curso->CupoCur }}</td>
                    <td>{{ max($curso->CupoCur - ($curso->inscripciones_count ?? 0), 0) }}</td>
                    <td>{{ $curso->inscripciones_count ?? 0 }}</td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('cursos.edit', $curso->Id_Cur) }}" class="btn-icon" title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <form action="{{ route('cursos.destroy', $curso->Id_Cur) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Esta seguro de eliminar este curso?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon btn-danger" title="Eliminar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No hay cursos registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">
        {{ $cursos->links() }}
    </div>
</div>
@endsection

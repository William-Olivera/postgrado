@extends('layouts.app')

@section('title', 'Inscripciones')
@section('page-title', 'Inscripciones')
@section('page-subtitle', 'Gestion de inscripciones por periodo')

@section('content')
<div class="page-header">
    <div class="filters">
        <form action="{{ route('inscripciones.index') }}" method="GET" class="filter-form">
            <select name="periodo" class="filter-select">
                <option value="">Período activo</option>
                @foreach($periodos as $periodo)
                    <option value="{{ $periodo }}" {{ request('periodo', $activePeriod) == $periodo ? 'selected' : '' }}>{{ $periodo }}</option>
                @endforeach
            </select>
            <select name="curso" class="filter-select">
                <option value="">Todos los cursos</option>
                @foreach($cursos as $curso)
                    <option value="{{ $curso->Id_Cur }}" {{ request('curso') == $curso->Id_Cur ? 'selected' : '' }}>{{ $curso->NombreCur }}</option>
                @endforeach
            </select>
            <select name="estado" class="filter-select">
                <option value="">Todos los estados</option>
                <option value="Completada" {{ request('estado') == 'Completada' ? 'selected' : '' }}>Completada</option>
                <option value="Pendiente" {{ request('estado') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
            </select>
            <button type="submit" class="btn-filter">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            </button>
        </form>
    </div>
    <a href="{{ route('inscripciones.create') }}" class="btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nueva Inscripcion
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>N. INSCRIPCION</th>
                    <th>ESTUDIANTE</th>
                    <th>CURSO</th>
                    <th>PERIODO</th>
                    <th>FECHA</th>
                    <th>ESTADO</th>
                    <th>ESTADO PAGO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inscripciones as $inscripcion)
                <tr>
                    <td>INS-{{ date('Y', strtotime($inscripcion->FechaIns)) }}-{{ str_pad($inscripcion->Id_E, 3, '0', STR_PAD_LEFT) }}</td>
                    <td class="font-medium">{{ $inscripcion->nombreE }} {{ $inscripcion->paternoE }}</td>
                    <td>{{ $inscripcion->NombreCur }}</td>
                    <td><span class="badge badge-info">{{ $inscripcion->PeriodoCur }}</span></td>
                    <td>{{ date('d/m/Y', strtotime($inscripcion->FechaIns)) }}</td>
                    <td>
                        @if($inscripcion->EstadoIns == 'Completada')
                            <span class="badge badge-success">Completada</span>
                        @else
                            <span class="badge badge-warning">Pendiente</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $pagado = \App\Models\Pago::where('Id_E', $inscripcion->Id_E)->sum('MontoP');
                            $plan = \App\Models\PlanPago::where('Id_Cur', $inscripcion->Id_Cur)->first();
                            $total = $plan ? $plan->MontoTotalPP : 0;
                        @endphp
                        @if($pagado >= $total)
                            <span class="badge badge-success">Pagado</span>
                        @elseif($pagado > 0)
                            <span class="badge badge-warning">Parcial</span>
                        @else
                            <span class="badge badge-danger">Pendiente</span>
                        @endif
                    </td>
                    <td>
                        <div class="actions">
                            <form action="{{ route('inscripciones.destroy', ['idE' => $inscripcion->Id_E, 'idCur' => $inscripcion->Id_Cur]) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Esta seguro de eliminar esta inscripcion?');">
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
                    <td colspan="8" class="text-center">No hay inscripciones registradas</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">
        {{ $inscripciones->links() }}
    </div>
</div>
@endsection
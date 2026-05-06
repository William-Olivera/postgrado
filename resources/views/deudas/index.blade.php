@extends('layouts.app')

@section('page-title', 'Deudas y Saldos')
@section('page-subtitle', 'Control de deudas de estudiantes')

@section('content')
<div class="page-container">
    <div class="page-header">
        <div class="search-box">
            <input type="text" id="deudaSearch" class="form-control" placeholder="Buscar por nombre, CI o registro..." autocomplete="off">
        </div>
    </div>

    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-value">Bs {{ number_format($deudaTotal, 2) }}</div>
            <div class="stat-label">Deuda Total</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $estudiantesConDeuda }}</div>
            <div class="stat-label">Estudiantes con Deuda</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">Bs {{ number_format($deudaPromedio ?? 0, 2) }}</div>
            <div class="stat-label">Deuda Promedio</div>
        </div>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Curso</th>
                    <th>Monto Total</th>
                    <th>Pagado</th>
                    <th>Saldo</th>
                    <th>Progreso</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deudas as $deuda)
                    <tr data-search-name="{{ strtolower($deuda['estudiante']->paternoE . ' ' . $deuda['estudiante']->nombreE) }}" data-search-ci="{{ strtolower($deuda['estudiante']->CedulaE) }}" data-search-registro="{{ strtolower((string) $deuda['estudiante']->RegistroE) }}">
                        <td>{{ $deuda['estudiante']->paternoE }} {{ $deuda['estudiante']->nombreE }}</td>
                        <td>{{ $deuda['curso'] }}</td>
                        <td>Bs {{ number_format($deuda['costoTotal'], 2) }}</td>
                        <td>Bs {{ number_format($deuda['pagado'], 2) }}</td>
                        <td><strong>Bs {{ number_format($deuda['saldoPendiente'], 2) }}</strong></td>
                        <td>
                            <div class="progress-bar">
                                <div class="progress-fill {{ $deuda['progreso'] >= 100 ? 'bg-success' : ($deuda['progreso'] >= 50 ? 'bg-warning' : 'bg-danger') }}" style="width: {{ $deuda['progreso'] }}%"></div>
                            </div>
                            <span class="progress-text">{{ $deuda['progreso'] }}%</span>
                        </td>
                        <td>
                            @if($deuda['saldoPendiente'] > 0)
                                <span class="badge badge-danger">Con Deuda</span>
                            @else
                                <span class="badge badge-success">Pagado</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No hay registros</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    .page-container {
        padding: 20px;
    }
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .search-box {
        min-width: 280px;
    }
    .search-box .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background: var(--card-bg);
        color: var(--text-primary);
        font-size: 0.95rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.3s ease, color 0.3s ease;
    }
    .search-box .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }
    .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 20px;
        text-align: center;
    }
    .stat-value {
        font-size: 28px;
        font-weight: bold;
        color: var(--danger);
    }
    .stat-label {
        font-size: 12px;
        color: var(--text-secondary);
        margin-top: 8px;
    }
    .table-container {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        overflow: auto;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    .table th {
        background: var(--primary);
        color: white;
        padding: 12px;
        text-align: left;
        font-weight: 600;
    }
    .table td {
        padding: 12px;
        border-bottom: 1px solid var(--border-color);
    }
    .table tr:hover {
        background: var(--bg-hover);
    }
    .badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        color: white;
    }
    .badge-success {
        background: var(--success);
    }
    .badge-danger {
        background: var(--danger);
    }
    .text-center {
        text-align: center;
    }
    .progress-bar {
        width: 100%;
        height: 8px;
        background: var(--bg-hover);
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 4px;
    }
    .progress-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.3s ease;
    }
    .progress-fill.bg-success {
        background: var(--success);
    }
    .progress-fill.bg-warning {
        background: var(--warning);
    }
    .progress-fill.bg-danger {
        background: var(--danger);
    }
    .progress-text {
        font-size: 12px;
        color: var(--text-secondary);
    }
    .no-results-row {
        display: none;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('deudaSearch');
        const rows = document.querySelectorAll('.table tbody tr');
        const noResultsRow = document.createElement('tr');
        noResultsRow.classList.add('no-results-row');
        noResultsRow.innerHTML = '<td colspan="7" class="text-center">No se encontraron deudas que coincidan con la búsqueda.</td>';
        document.querySelector('.table tbody').appendChild(noResultsRow);

        function filterDeudas() {
            const query = searchInput.value.trim().toLowerCase();
            let visibleCount = 0;

            rows.forEach(row => {
                if (row.classList.contains('no-results-row')) {
                    return;
                }

                const name = row.dataset.searchName || '';
                const ci = row.dataset.searchCi || '';
                const registro = row.dataset.searchRegistro || '';
                const course = row.cells[1]?.textContent.toLowerCase() || '';
                const text = [name, ci, registro, course].join(' ');
                const match = query === '' || text.includes(query);

                row.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });

            noResultsRow.style.display = visibleCount === 0 ? '' : 'none';
        }

        searchInput.addEventListener('input', filterDeudas);
    });
</script>
@endsection

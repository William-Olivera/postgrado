@extends('layouts.app')

@section('page-title', 'Pagos')
@section('page-subtitle', 'Listado de pagos')

@section('content')
<div class="page-container">
    <div class="page-header">
        <div class="search-box">
            <input type="text" id="pagoSearch" class="form-control" placeholder="Buscar por nombre, CI o registro..." autocomplete="off">
        </div>
        <div class="header-actions">
            <form action="{{ route('pagos.index') }}" method="GET" class="per-page-form">
                <label for="perPage">Mostrar</label>
                <select name="perPage" id="perPage" class="form-control" onchange="this.form.submit()">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                </select>
            </form>
            <a href="{{ route('pagos.create') }}" class="btn btn-primary btn-lg">+ Nuevo Pago</a>
        </div>
    </div>

    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-value">Bs {{ number_format($totalRecaudado, 2) }}</div>
            <div class="stat-label">Total Recaudado</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $pagosParciales }}</div>
            <div class="stat-label">Cuotas Pagadas</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $pagosTotales }}</div>
            <div class="stat-label">Pagos Totales</div>
        </div>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Estudiante</th>
                    <th>Curso</th>
                    <th>Tipo</th>
                    <th>Monto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pagos as $pago)
                    <tr data-search-name="{{ strtolower($pago->estudiante->paternoE . ' ' . $pago->estudiante->nombreE) }}" data-search-ci="{{ strtolower($pago->estudiante->CedulaE) }}" data-search-registro="{{ strtolower((string) $pago->estudiante->RegistroE) }}">
                        <td>{{ \Carbon\Carbon::parse($pago->FechaP)->format('d/m/Y') }}</td>
                        <td>{{ $pago->estudiante->paternoE }} {{ $pago->estudiante->nombreE }}</td>
                        <td>{{ $pago->curso->NombreCur ?? 'N/A' }}</td>
                        <td><span class="badge">{{ $pago->TipoP }}</span></td>
                        <td>Bs {{ number_format($pago->MontoP, 2) }}</td>
                        <td>
                            <button class="btn btn-sm btn-info">Ver</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No hay pagos</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pagos->hasPages())
        <div class="pagination">
            {{ $pagos->links() }}
        </div>
    @endif
</div>

<style>
    .page-container {
        padding: 20px;
    }
    .page-header {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .header-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }
    .per-page-form {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .per-page-form label {
        font-size: 14px;
        color: var(--text-secondary);
    }
    .per-page-form .form-control {
        width: auto;
    }
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
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
        color: var(--primary);
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
        background: var(--info);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
    }
    .btn {
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
    }
    .btn-primary {
        background: var(--primary);
        color: white;
    }
    .btn-lg {
        padding: 10px 18px;
        font-size: 14px;
    }
    .btn-sm {
        padding: 4px 8px;
        font-size: 11px;
    }
    .btn-info {
        background: var(--info);
        color: white;
    }

    .text-center {
        text-align: center;
    }
    .no-results-row {
        display: none;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('pagoSearch');
        const rows = document.querySelectorAll('.table tbody tr');
        const noResultsRow = document.createElement('tr');
        noResultsRow.classList.add('no-results-row');
        noResultsRow.innerHTML = '<td colspan="6" class="text-center">No se encontraron pagos que coincidan con la búsqueda.</td>';
        document.querySelector('.table tbody').appendChild(noResultsRow);

        function filterPagos() {
            const query = searchInput.value.trim().toLowerCase();
            let visibleCount = 0;

            rows.forEach(row => {
                if (row.classList.contains('no-results-row')) {
                    return;
                }

                const name = row.dataset.searchName || '';
                const ci = row.dataset.searchCi || '';
                const registro = row.dataset.searchRegistro || '';
                const course = row.cells[2]?.textContent.toLowerCase() || '';
                const text = [name, ci, registro, course].join(' ');
                const match = query === '' || text.includes(query);

                row.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });

            noResultsRow.style.display = visibleCount === 0 ? '' : 'none';
        }

        searchInput.addEventListener('input', filterPagos);
    });
</script>
@endsection

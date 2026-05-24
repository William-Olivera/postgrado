@extends('layouts.app')
@section('titulo', 'Dashboard')
@section('contenido')
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card card-stats p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted">Estudiantes</h6>
                    <h3>{{ $estadisticas['total_estudiantes'] }}</h3>
                </div>
                <i class="bi bi-people fs-2 text-primary"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stats success p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted">Inscripciones Activas</h6>
                    <h3>{{ $estadisticas['total_inscripciones'] }}</h3>
                </div>
                <i class="bi bi-journal-check fs-2 text-success"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stats warning p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted">Pagos Hoy</h6>
                    <h3>Bs. {{ number_format($estadisticas['pagos_hoy'], 2) }}</h3>
                </div>
                <i class="bi bi-cash-coin fs-2 text-warning"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stats danger p-3">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-muted">Saldo Pendiente Total</h6>
                    <h3>Bs. {{ number_format($estadisticas['saldo_pendiente_total'], 2) }}</h3>
                </div>
                <i class="bi bi-exclamation-triangle fs-2 text-danger"></i>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <h5 class="m-0">Bienvenido al Sistema de Postgrado</h5>
    </div>
    <div class="card-body">
        <p class="text-muted">Use el menú lateral para gestionar estudiantes, inscripciones, pagos y documentos.</p>
        <div class="row mt-4">
            <div class="col-md-6">
                <a href="{{ route('inscripciones.index') }}" class="btn btn-success w-100 mb-2">+ Nueva Inscripción</a>
            </div>
            <div class="col-md-6">
                <a href="{{ route('deudas.index') }}" class="btn btn-warning w-100 mb-2">Ver Deudas</a>
            </div>
        </div>
    </div>
</div>
@endsection

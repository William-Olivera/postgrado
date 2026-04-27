<?php

use App\Http\Controllers\ControlSaldosController;
use App\Http\Controllers\PagoRegistroController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/cursos', [PagoRegistroController::class, 'cursos']);
Route::get('/api/estudiantes/buscar', [PagoRegistroController::class, 'buscarEstudiantes']);
Route::post('/api/pagos', [PagoRegistroController::class, 'store']);

Route::get('/api/saldos/estudiantes/buscar', [ControlSaldosController::class, 'buscarEstudiantes']);
Route::get('/api/saldos/estudiante/{id}', [ControlSaldosController::class, 'saldoEstudiante'])->whereNumber('id');
Route::get('/api/saldos/cursos', [ControlSaldosController::class, 'saldosPorCurso']);
Route::get('/api/saldos/gestion', [ControlSaldosController::class, 'saldosGestion']);

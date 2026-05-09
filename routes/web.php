<?php

use App\Http\Controllers\AdminDatosController;
use App\Http\Controllers\ControlSaldosController;
use App\Http\Controllers\PagoRegistroController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/cursos', [PagoRegistroController::class, 'cursos']);
Route::get('/api/estudiantes/buscar', [PagoRegistroController::class, 'buscarEstudiantes']);
Route::get('/api/estudiantes/base/buscar', [PagoRegistroController::class, 'buscarEstudiantesBase']);
Route::post('/api/inscripciones', [PagoRegistroController::class, 'storeInscripcion']);
Route::post('/api/pagos', [PagoRegistroController::class, 'store']);

Route::post('/api/admin/cursos', [AdminDatosController::class, 'storeCurso']);
Route::put('/api/admin/cursos/{id}', [AdminDatosController::class, 'updateCurso'])->whereNumber('id');
Route::get('/api/admin/estudiantes/{id}', [AdminDatosController::class, 'showEstudiante'])->whereNumber('id');
Route::patch('/api/admin/estudiantes/{id}', [AdminDatosController::class, 'updateEstudiante'])->whereNumber('id');
Route::get('/api/admin/estudiantes/{id}/pagos', [AdminDatosController::class, 'pagosEstudiante'])->whereNumber('id');
Route::patch('/api/admin/pagos/{id}', [AdminDatosController::class, 'updatePago'])->whereNumber('id');

Route::get('/api/saldos/estudiantes/buscar', [ControlSaldosController::class, 'buscarEstudiantes']);
Route::get('/api/saldos/estudiante/{id}', [ControlSaldosController::class, 'saldoEstudiante'])->whereNumber('id');
Route::get('/api/saldos/cursos', [ControlSaldosController::class, 'saldosPorCurso']);
Route::get('/api/saldos/gestion', [ControlSaldosController::class, 'saldosGestion']);

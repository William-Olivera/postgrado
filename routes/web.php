<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\DeudaController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RespaldoController;
use App\Http\Controllers\PagoRegistroController;
use App\Http\Controllers\ControlSaldosController;

// Login
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas
Route::middleware([])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Estudiantes (HU-03)
    Route::resource('estudiantes', EstudianteController::class);

    // Pagos
    Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
    Route::get('/pagos/create', [PagoController::class, 'create'])->name('pagos.create');
    Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');

    // Deudas y Saldos
    Route::get('/deudas', [DeudaController::class, 'index'])->name('deudas.index');

    // Inscripciones (HU-04)
    Route::get('/inscripciones', [InscripcionController::class, 'index'])->name('inscripciones.index');
    Route::get('/inscripciones/create', [InscripcionController::class, 'create'])->name('inscripciones.create');
    Route::post('/inscripciones', [InscripcionController::class, 'store'])->name('inscripciones.store');
    Route::delete('/inscripciones/{idE}/{idCur}', [InscripcionController::class, 'destroy'])->name('inscripciones.destroy');

    // Cursos (HU-05)
    Route::resource('cursos', CursoController::class);

    // Documentos
    Route::get('/documentos', [DocumentoController::class, 'index'])->name('documentos.index');

    // Usuarios
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');

    // Reportes
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');

    // Respaldos
    Route::get('/respaldos', [RespaldoController::class, 'index'])->name('respaldos.index');
});

// APIs JSON
Route::post('/api/registrar-pago', [PagoRegistroController::class, 'registrarPago']);
Route::get('/api/control-saldos', [ControlSaldosController::class, 'controlSaldos']);
Route::get('/api/estudiantes/buscar', [PagoRegistroController::class, 'buscarEstudiante']);
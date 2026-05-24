<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\UsuarioController;
use App\Http\Controllers\Web\EstudianteController;
use App\Http\Controllers\Web\InscripcionController;
use App\Http\Controllers\Web\PagoController;
use App\Http\Controllers\Web\DocumentoController;
use App\Http\Controllers\Web\ConfiguracionController;
use App\Http\Controllers\Web\DeudaController;
use App\Http\Controllers\Web\ReporteController;
use App\Http\Controllers\Web\CursoController;
use App\Http\Controllers\Web\RespaldoController;

// =============================================================================
// AUTH PÚBLICO
// =============================================================================
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Redirect raíz a login
Route::get('/', function () {
    return redirect()->route('login');
});

// =============================================================================
// RUTAS PROTEGIDAS POR AUTH
// =============================================================================
Route::middleware('auth')->group(function () {

    // --- DASHBOARD ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- ESTUDIANTES ---
    Route::get('estudiantes', [EstudianteController::class, 'index'])->name('estudiantes.index');
    Route::post('estudiantes', [EstudianteController::class, 'store'])->name('estudiantes.store');
    Route::put('estudiantes/{estudiante}', [EstudianteController::class, 'update'])->name('estudiantes.update');
    Route::put('estudiantes/{estudiante}/baja', [EstudianteController::class, 'darBaja'])->name('estudiantes.baja');
    Route::put('estudiantes/{estudiante}/alta', [EstudianteController::class, 'darAlta'])->name('estudiantes.alta');

    // --- INSCRIPCIONES (FLUJO COMPLETO) ---
    // 1. Tarjetas de cursos disponibles para inscribir
    Route::get('inscripciones', [InscripcionController::class, 'index'])->name('inscripciones.index');

    // 2. Detalle del curso: lista de estudiantes inscritos + modal nueva inscripción
    Route::get('inscripciones/curso/{curso}', [InscripcionController::class, 'show'])->name('inscripciones.show');

    // 3. Procesar nueva inscripción (desde modal)
    Route::post('inscripciones', [InscripcionController::class, 'store'])->name('inscripciones.store');

    // 4. API AJAX: búsqueda de estudiantes para autocompletado
    Route::get('api/buscar-estudiante', [InscripcionController::class, 'buscarEstudiante'])->name('estudiantes.buscar');

    // 5. Rutas legacy para compatibilidad (editar/actualizar/eliminar inscripción)
    Route::get('inscripciones/{inscripcion}/editar', [InscripcionController::class, 'edit'])->name('inscripciones.edit');
    Route::put('inscripciones/{inscripcion}', [InscripcionController::class, 'update'])->name('inscripciones.update');
    Route::delete('inscripciones/{inscripcion}', [InscripcionController::class, 'destroy'])->name('inscripciones.destroy');

    // --- PAGOS (REGISTRO) ---
    Route::get('pagos', [PagoController::class, 'index'])->name('pagos.index');
    Route::post('pagos', [PagoController::class, 'store'])->name('pagos.store');
    Route::get('pagos/{pago}', [PagoController::class, 'show'])->name('pagos.show');
    Route::get('api/pagos/buscar-estudiante', [PagoController::class, 'buscarEstudiante'])->name('pagos.buscar');
    Route::get('api/pagos/cuotas/{estudiante}', [PagoController::class, 'cuotasPendientes'])->name('pagos.cuotas');

    // --- DOCUMENTOS ---

// Rutas de Control de Expedientes Digitales por Alumno
    Route::get('/documentos/expediente/{id}', [DocumentoController::class, 'show'])->name('documentos.show');
    Route::post('/documentos/guardar', [DocumentoController::class, 'store'])->name('documentos.store');
    Route::delete('/documentos/eliminar/{documento}', [DocumentoController::class, 'destroy'])->name('documentos.destroy');
    Route::get('/documentos/descargar/{documento}', [DocumentoController::class, 'download'])->name('documentos.download');

    // --- DEUDAS Y SALDOS ---
    Route::get('deudas', [DeudaController::class, 'index'])->name('deudas.index');
    Route::get('deudas/{estudiante}', [DeudaController::class, 'show'])->name('deudas.show');

    // --- CURSOS (GESTIÓN ADMINISTRATIVA) ---
    Route::get('cursos', [CursoController::class, 'index'])->name('cursos.index');
    Route::get('cursos/{curso}', [InscripcionController::class, 'show'])->name('cursos.show');
    Route::post('cursos', [CursoController::class, 'store'])->name('cursos.store');
    Route::put('cursos/{curso}', [CursoController::class, 'update'])->name('cursos.update');
    Route::put('cursos/{curso}/toggle', [CursoController::class, 'toggleActivo'])->name('cursos.toggle');

    // --- REPORTES Y PLANILLAS ---
    Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::post('reportes/generar', [ReporteController::class, 'generar'])->name('reportes.generar');
    Route::get('/reportes/exportar-planilla/{cursoId}', [ReporteController::class, 'exportarExcelPlanilla'])->name('reportes.excel.curso');

    // --- RESPALDOS DE BASE DE DATOS ---
    Route::get('respaldos', [RespaldoController::class, 'index'])->name('respaldos.index');
    Route::post('respaldos/crear', [RespaldoController::class, 'crear'])->name('respaldos.crear');
    Route::get('respaldos/descargar/{archivo}', [RespaldoController::class, 'descargar'])->name('respaldos.descargar');
    Route::delete('respaldos/eliminar', [RespaldoController::class, 'eliminar'])->name('respaldos.eliminar');

});

// =============================================================================
// RUTAS EXCLUSIVAS PARA ADMINISTRADOR
// =============================================================================
Route::middleware(['auth', 'rol:administrador'])->group(function () {

    // --- GESTIÓN DE USUARIOS DEL SISTEMA ---
    Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('usuarios/crear', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('usuarios/{usuario}/editar', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::put('usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::delete('usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

    // --- CONFIGURACIONES GENERALES DEL SISTEMA ---
    Route::get('configuraciones', [ConfiguracionController::class, 'index'])->name('configuraciones.index');
    Route::get('configuraciones/crear', [ConfiguracionController::class, 'create'])->name('configuraciones.create');
    Route::post('configuraciones', [ConfiguracionController::class, 'store'])->name('configuraciones.store');
    Route::get('configuraciones/{configuracion}/editar', [ConfiguracionController::class, 'edit'])->name('configuraciones.edit');
    Route::put('configuraciones/{configuracion}', [ConfiguracionController::class, 'update'])->name('configuraciones.update');
    Route::delete('configuraciones/{configuracion}', [ConfiguracionController::class, 'destroy'])->name('configuraciones.destroy');

});

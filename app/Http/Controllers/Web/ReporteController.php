<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Inscripcion;
use App\Models\Pago;
use App\Models\Estudiante;
use App\Exports\ReporteInscritosExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    /**
     * Muestra la página de reportes y planillas.
     */
    public function index(): View
    {
        $cursos = Curso::all();
        return view('reportes.index', compact('cursos'));
    }

    /**
     * Genera reportes PDF según el tipo seleccionado.
     */
    public function generar(Request $request)
    {
        $data = $request->validate([
            'tipo_reporte' => 'required|in:pagos,deudas,inscripciones',
            'curso_id' => 'nullable|exists:cursos,id',
            'fecha_desde' => 'nullable|date',
            'fecha_hasta' => 'nullable|date',
        ]);

        // Por ahora, solo implementamos reporte de pagos
        // Los otros tipos se pueden agregar en el futuro
        if ($data['tipo_reporte'] !== 'pagos') {
            return redirect()->back()->with('error', 'Este tipo de reporte aún no está implementado. Por favor seleccione "Reporte General de Pagos".');
        }

        $query = Pago::query()->with('inscripcion.estudiante', 'inscripcion.curso')
            ->whereHas('inscripcion');

        if ($data['curso_id']) {
            $query->whereHas('inscripcion', function($q) use ($data) {
                $q->where('curso_id', $data['curso_id']);
            });
        }

        if ($data['fecha_desde']) {
            $query->where('fecha_pago', '>=', $data['fecha_desde']);
        }

        if ($data['fecha_hasta']) {
            $query->where('fecha_pago', '<=', $data['fecha_hasta']);
        }

        $pagos = $query->orderBy('fecha_pago', 'desc')->get();

        $pdf = Pdf::loadView('reportes.pdf.pagos', compact('pagos', 'data'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('reporte_pagos_' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Procesa y descarga la planilla oficial en formato Excel para un curso específico.
     *
     * @param int $cursoId
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportarExcelPlanilla($cursoId)
    {
        // 1. Validar que el curso exista en el sistema
        $curso = Curso::findOrFail($cursoId);

        // 2. Recuperar inscripciones con todas sus relaciones anidadas optimizadas
        $inscripciones = Inscripcion::where('curso_id', $cursoId)
            ->with([
                'estudiante',
                'planPago.detalles.pagos'
            ])
            ->get()
            ->sortBy(function ($inscripcion) {
                // Ordenar alfabéticamente de forma estricta por el apellido paterno del estudiante
                return trim(mb_strtolower($inscripcion->estudiante->paterno));
            });

        // 3. Sanitizar y limpiar el nombre del archivo para evitar fallos en sistemas operativos
        $nombreCursoLimpio = str_replace([' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $curso->nombre);
        $nombreArchivo = 'PLANILLA_PAGOS_' . strtoupper($curso->tipo->value) . '_' . strtoupper($nombreCursoLimpio) . '_V' . $curso->version . '_E' . $curso->edicion . '.xlsx';

        // 4. Retornar la descarga del Excel delegando la estructura a la clase Exportable
        return Excel::download(new ReporteInscritosExport($inscripciones, $curso), $nombreArchivo);
    }
}

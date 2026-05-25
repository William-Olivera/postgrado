<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use App\Models\Inscripcion;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DeudaController extends Controller
{
    public function index(): View
    {
        $estudiantesData = Estudiante::with([
            'inscripciones.curso',
            'inscripciones.planPago',
        ])
        ->whereHas('inscripciones')
        ->get()
        ->map(function (Estudiante $estudiante) {
            $pagado = 0;
            $pendiente = 0;

            foreach ($estudiante->inscripciones as $inscripcion) {
                if ($inscripcion->planPago) {
                    $pagado += (float) $inscripcion->planPago->monto_total_pagado;
                    $pendiente += (float) $inscripcion->planPago->saldo_pendiente;
                }
            }

            $total = $pagado + $pendiente;
            $progreso = $total > 0 ? (int) round(($pagado / $total) * 100) : 0;

            return [
                'estudiante' => $estudiante,
                'pagado' => $pagado,
                'pendiente' => $pendiente,
                'total' => $total,
                'progreso' => $progreso,
            ];
        })
        ->sortByDesc('pendiente')
        ->values();

        $deudaTotal = $estudiantesData->sum('pendiente');
        $estudiantesConDeuda = $estudiantesData->where('pendiente', '>', 0)->count();

        return view('deudas.index', compact('estudiantesData', 'deudaTotal', 'estudiantesConDeuda'));
    }

    public function show(Estudiante $estudiante): JsonResponse
    {
        $inscripciones = Inscripcion::with([
            'curso',
            'planPago.detalles.pagos',
        ])
        ->where('estudiante_id', $estudiante->id)
        ->get();

        $cursos = $inscripciones->map(function (Inscripcion $inscripcion) {
            $plan = $inscripcion->planPago;
            $detalles = $plan?->detalles ?? collect();

            return [
                'curso' => $inscripcion->curso->nombre,
                'tipo' => $inscripcion->tipo_inscripcion->value,
                'periodo' => $inscripcion->curso->periodo,
                'total_pagado' => (float) ($plan->monto_total_pagado ?? 0),
                'saldo_pendiente' => (float) ($plan->saldo_pendiente ?? 0),
                'cuotas' => $detalles->map(fn ($d) => [
                    'concepto' => $d->concepto,
                    'fase' => $d->fase,
                    'monto_programado' => (float) $d->monto_programado,
                    'monto_pagado' => (float) $d->monto_pagado,
                    'saldo' => (float) $d->saldo_cuota,
                    'estado' => $d->estado,
                    'pagos' => $d->pagos->map(fn ($p) => [
                        'id' => $p->id,
                        'fecha_pago' => $p->fecha_pago->format('Y-m-d'),
                        'monto' => (float) $p->monto,
                        'nro_comprobante' => $p->nro_comprobante,
                        'usuario' => $p->registradoPor?->name,
                        'observacion' => $p->observacion,
                        'archivo_adjunto' => $p->archivo_adjunto,
                    ]),
                ]),
                'saldo_total' => (float) ($plan->saldo_pendiente ?? 0),
            ];
        });

        $totalPagado = $cursos->sum('total_pagado');
        $saldoPendiente = $cursos->sum('saldo_pendiente');

        return response()->json([
            'estudiante' => [
                'id' => $estudiante->id,
                'nombre_completo' => $estudiante->nombre_completo,
                'cedula' => $estudiante->cedula,
                'registro' => $estudiante->registro,
                'iniciales' => $estudiante->iniciales,
            ],
            'total_pagado' => $totalPagado,
            'saldo_pendiente' => $saldoPendiente,
            'cursos' => $cursos,
        ]);
    }
}

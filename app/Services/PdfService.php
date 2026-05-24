<?php
namespace App\Services;

use App\Models\Pago;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService implements Contracts\PdfServiceInterface
{
    public function generarRecibo(Pago $pago): string
    {
        $pago->load([
            'detallePlanPago.planPago.inscripcion.estudiante',
            'detallePlanPago.planPago.inscripcion.curso'
        ]);

        $pdf = Pdf::loadView('pdfs.recibo-pago', ['pago' => $pago])->setPaper('letter');
        $nombreArchivo = "recibos/recibo_{$pago->id}_{$pago->nro_comprobante}.pdf";
        Storage::disk('public')->put($nombreArchivo, $pdf->output());
        return Storage::disk('public')->url($nombreArchivo);
    }

    public function generarReportePagos(array $filtros): string
    {
        $query = Pago::with(['detallePlanPago.planPago.inscripcion.estudiante', 'detallePlanPago.planPago.inscripcion.curso']);

        if (!empty($filtros['estudiante_id'])) {
            $query->whereHas('detallePlanPago.planPago.inscripcion', function ($q) use ($filtros) {
                $q->where('estudiante_id', $filtros['estudiante_id']);
            });
        }
        if (!empty($filtros['curso_id'])) {
            $query->whereHas('detallePlanPago.planPago.inscripcion', function ($q) use ($filtros) {
                $q->where('curso_id', $filtros['curso_id']);
            });
        }
        if (!empty($filtros['fecha_desde']) && !empty($filtros['fecha_hasta'])) {
            $query->whereBetween('fecha_pago', [$filtros['fecha_desde'], $filtros['fecha_hasta']]);
        }

        $pagos = $query->get();
        $pdf = Pdf::loadView('pdfs.reporte-pagos', ['pagos' => $pagos, 'filtros' => $filtros])->setPaper('a4', 'landscape');
        $nombreArchivo = "reportes/reporte_pagos_" . now()->format('Ymd_His') . ".pdf";
        Storage::disk('public')->put($nombreArchivo, $pdf->output());
        return Storage::disk('public')->url($nombreArchivo);
    }
}

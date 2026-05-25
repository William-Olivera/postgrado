<?php

namespace App\Services;

use App\Models\DetallePlanPago;
use App\Models\Pago;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PagoService implements Contracts\PagoServiceInterface
{
    public function registrar(array $data): Pago
    {
        return DB::transaction(function () use ($data) {
            $detalle = DetallePlanPago::with('planPago')->findOrFail($data['detalle_plan_pago_id']);

            if ((float) $data['monto'] > (float) $detalle->saldo_cuota) {
                throw new \InvalidArgumentException('El monto excede el saldo pendiente de la cuota.');
            }

            $archivoAdjunto = null;
            if (isset($data['archivo_adjunto']) && $data['archivo_adjunto']) {
                \Log::info('Archivo recibido', ['archivo' => $data['archivo_adjunto']]);
                $archivoAdjunto = $data['archivo_adjunto']->store('pagos', 'public');
                \Log::info('Archivo guardado', ['ruta' => $archivoAdjunto]);
            } else {
                \Log::info('No se recibió archivo adjunto');
            }

            return Pago::create([
                'detalle_plan_pago_id' => $detalle->id,
                'inscripcion_id' => $detalle->planPago->inscripcion_id,
                'fecha_pago' => $data['fecha_pago'],
                'monto' => $data['monto'],
                'nro_comprobante' => $data['nro_comprobante'],
                'observacion' => $data['observacion'] ?? null,
                'archivo_adjunto' => $archivoAdjunto,
            ]);
        });
    }
}

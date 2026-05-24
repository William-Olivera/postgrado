<?php

namespace App\Services;

use App\Models\DetallePlanPago;
use App\Models\Pago;
use Illuminate\Support\Facades\DB;

class PagoService implements Contracts\PagoServiceInterface
{
    public function registrar(array $data): Pago
    {
        return DB::transaction(function () use ($data) {
            $detalle = DetallePlanPago::with('planPago')->findOrFail($data['detalle_plan_pago_id']);

            if ((float) $data['monto'] > (float) $detalle->saldo_cuota) {
                throw new \InvalidArgumentException('El monto excede el saldo pendiente de la cuota.');
            }

            return Pago::create([
                'detalle_plan_pago_id' => $detalle->id,
                'inscripcion_id' => $detalle->planPago->inscripcion_id,
                'fecha_pago' => $data['fecha_pago'],
                'monto' => $data['monto'],
                'nro_comprobante' => $data['nro_comprobante'],
                'observacion' => $data['observacion'] ?? null,
            ]);
        });
    }
}

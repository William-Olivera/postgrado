<?php

namespace App\Observers;

use App\Models\Pago;

class PagoObserver
{
    public function created(Pago $pago): void
    {
        $this->recalcular($pago);
    }

    public function updated(Pago $pago): void
    {
        $this->recalcular($pago);
    }

    public function deleted(Pago $pago): void
    {
        $this->recalcular($pago);
    }

    private function recalcular(Pago $pago): void
    {
        $detalle = $pago->detallePlanPago;
        if (!$detalle) return;

        $detalle->recalcular();

        $plan = $detalle->planPago;
        if ($plan) {
            $plan->recalcularTotales();
        }
    }
}
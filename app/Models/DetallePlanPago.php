<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DetallePlanPago extends Model
{
    use HasFactory;

    protected $table = 'detalle_plan_pagos';

    protected $fillable = [
        'plan_pago_id',
        'nro_cuota',
        'nro_modulo',
        'concepto',
        'fase',
        'monto_programado',
        'monto_pagado',
        'monto_descuento',
        'saldo_cuota',
        'fecha_vencimiento',
        'estado',
    ];

    protected $casts = [
        'monto_programado' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'monto_descuento' => 'decimal:2',
        'saldo_cuota' => 'decimal:2',
        'fecha_vencimiento' => 'date',
    ];

    public function planPago(): BelongsTo
    {
        return $this->belongsTo(PlanPago::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function recalcular(): void
    {
        $montoPagado = $this->pagos()->sum('monto');
        $saldo = max(0, (float) $this->monto_programado - $montoPagado);

        $estado = 'Pendiente';
        if ($saldo <= 0) {
            $estado = 'Pagado';
        } elseif ($montoPagado > 0) {
            $estado = 'Parcial';
        }

        $this->update([
            'monto_pagado' => $montoPagado,
            'saldo_cuota' => $saldo,
            'estado' => $estado,
        ]);
    }
}

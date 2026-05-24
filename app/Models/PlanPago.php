<?php

namespace App\Models;

use App\Enums\EstadoAcademico;
use App\Enums\EstadoFinanciero;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanPago extends Model
{
    use HasFactory;

    protected $table = 'plan_pagos';

    protected $fillable = [
        'inscripcion_id',
        'monto_total_programado',
        'monto_total_pagado',
        'saldo_pendiente',
        'total_cuotas',
        'estado',
    ];

    protected $casts = [
        'monto_total_programado' => 'decimal:2',
        'monto_total_pagado' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
    ];

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetallePlanPago::class);
    }

    public function recalcularTotales(): void
    {
        $this->load('detalles');

        $montoPagado = $this->detalles->sum('monto_pagado');
        $saldoPendiente = $this->detalles->sum('saldo_cuota');

        $estado = 'Pendiente';
        if ($saldoPendiente <= 0) {
            $estado = 'Pagado';
        } elseif ($montoPagado > 0) {
            $estado = 'En Mora';
            if ($this->detalles->every(fn ($d) => in_array($d->estado, ['Pagado', 'Condonado']) || $d->saldo_cuota <= 0)) {
                $estado = 'Pagado';
            }
        }

        $this->update([
            'monto_total_pagado' => $montoPagado,
            'saldo_pendiente' => max(0, $saldoPendiente),
            'estado' => $estado,
        ]);

        $inscripcion = $this->inscripcion;
        if ($inscripcion) {
            $updateData = [];
            
            if ($saldoPendiente <= 0) {
                $updateData['estado_financiero'] = EstadoFinanciero::Completado;
            } elseif ($montoPagado > 0) {
                $updateData['estado_financiero'] = EstadoFinanciero::Parcial;
                
                // Si el pago es registrado y el estado académico es Pendiente, pasarlo a Activo
                if ($inscripcion->estado_academico === EstadoAcademico::Pendiente) {
                    $updateData['estado_academico'] = EstadoAcademico::Activo;
                }
            }

            if (!empty($updateData)) {
                $inscripcion->update($updateData);
            }
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    protected $fillable = [
        'detalle_plan_pago_id',
        'inscripcion_id',
        'fecha_pago',
        'monto',
        'nro_comprobante',
        'observacion',
        'archivo_adjunto',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'monto' => 'decimal:2',
    ];

    public function detallePlanPago(): BelongsTo
    {
        return $this->belongsTo(DetallePlanPago::class);
    }

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class);
    }
}

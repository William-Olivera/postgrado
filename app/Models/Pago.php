<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $table = 'Pago';

    protected $primaryKey = 'Id_P';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'Id_PP', 'Id_E', 'Id_Cur', 'FechaP', 'MontoP', 'NroP', 'TipoP',
        'NroCompP', 'CuentaTransfP', 'ArchivoComprobanteP',
    ];

    public function planPago(): BelongsTo
    {
        return $this->belongsTo(PlanPago::class, 'Id_PP', 'Id_PP');
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'Id_E', 'Id_E');
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'Id_Cur', 'Id_Cur');
    }
}

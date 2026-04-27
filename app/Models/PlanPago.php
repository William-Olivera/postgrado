<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanPago extends Model
{
    protected $table = 'PlanPago';

    protected $primaryKey = 'Id_PP';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = ['MontoTotalPP', 'TotalCuotasPP', 'Id_Cur'];

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'Id_Cur', 'Id_Cur');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'Id_PP', 'Id_PP');
    }
}

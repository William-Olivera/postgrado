<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curso extends Model
{
    protected $table = 'Curso';

    protected $primaryKey = 'Id_Cur';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = ['NombreCur', 'TipoCur', 'VersionCur', 'EdicionCur'];

    public function estudiantes(): BelongsToMany
    {
        return $this->belongsToMany(Estudiante::class, 'Inscripcion', 'Id_Cur', 'Id_E')
            ->withPivot('FechaIns', 'EstadoIns');
    }

    public function planesPago(): HasMany
    {
        return $this->hasMany(PlanPago::class, 'Id_Cur', 'Id_Cur');
    }
}

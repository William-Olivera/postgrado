<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estudiante extends Model
{
    protected $table = 'Estudiante';

    protected $primaryKey = 'Id_E';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'nombreE', 'paternoE', 'maternoE', 'RegistroE', 'CedulaE',
        'TelefonoE', 'DireccionE', 'DescuentoE', 'ObservacionE', 'ActivoE',
    ];

    public function cursos(): BelongsToMany
    {
        return $this->belongsToMany(Curso::class, 'Inscripcion', 'Id_E', 'Id_Cur')
            ->withPivot('FechaIns', 'EstadoIns');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'Id_E', 'Id_E');
    }

    public function nombreCompleto(): string
    {
        $m = $this->maternoE ? ' '.$this->maternoE : '';

        return trim("{$this->nombreE} {$this->paternoE}{$m}");
    }
}

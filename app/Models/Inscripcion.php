<?php

namespace App\Models;

use App\Enums\EstadoAcademico;
use App\Enums\EstadoFinanciero;
use App\Enums\TipoCurso;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Inscripcion extends Model
{
    use HasFactory;

    protected $table = 'inscripciones';

    protected $fillable = [
        'estudiante_id',
        'curso_id',
        'tipo_inscripcion',
        'fecha_inscripcion',
        'estado_academico',
        'estado_financiero',
        'modalidad_pago',
        'observacion',
    ];

    protected $casts = [
        'tipo_inscripcion' => TipoCurso::class,
        'estado_academico' => EstadoAcademico::class,
        'estado_financiero' => EstadoFinanciero::class,
        'fecha_inscripcion' => 'date',
    ];

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class);
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    public function planPago(): HasOne
    {
        return $this->hasOne(PlanPago::class);
    }

    public function getNumeroInscripcionAttribute(): string
    {
        return 'INS-' . $this->curso->periodo . '-' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }
}
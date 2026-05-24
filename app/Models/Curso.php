<?php

namespace App\Models;

use App\Enums\TipoCurso;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'tipo', 'version', 'edicion', 'periodo',
        'costo_matricula', 'costo_total_estudio',
        'costo_defensa_diplomado', 'costo_defensa_especialidad', 'costo_defensa_maestria',
        'nro_modulos_diplomado', 'nro_modulos_especialidad', 'nro_modulos_maestria',
        'cupo', 'activo',
    ];

    protected $casts = [
        'tipo' => TipoCurso::class,
        'costo_matricula' => 'decimal:2',
        'costo_total_estudio' => 'decimal:2',
        'costo_defensa_diplomado' => 'decimal:2',
        'costo_defensa_especialidad' => 'decimal:2',
        'costo_defensa_maestria' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function getColorTipoAttribute(): string
    {
        return $this->tipo->color();
    }

    public function getColorTipoBgAttribute(): string
    {
        return $this->tipo->color() . '20';
    }

    public function getIconoTipoAttribute(): string
    {
        return $this->tipo->icono();
    }

    public function getTotalModulosAttribute(): int
    {
        return match($this->tipo) {
            TipoCurso::Diplomado => $this->nro_modulos_diplomado,
            TipoCurso::Especialidad => $this->nro_modulos_diplomado + ($this->nro_modulos_especialidad ?? 0),
            TipoCurso::Maestria => $this->nro_modulos_diplomado + ($this->nro_modulos_especialidad ?? 0) + ($this->nro_modulos_maestria ?? 0),
        };
    }

    public function getCostoModuloAttribute(): float
    {
        $total = $this->total_modulos;
        if ($total <= 0) return 0;
        return round($this->costo_total_estudio / $total, 2);
    }

    public function getCostoDefensasAttribute(): float
    {
        $defensas = $this->costo_defensa_diplomado;
        if ($this->tipo === TipoCurso::Especialidad || $this->tipo === TipoCurso::Maestria) {
            $defensas += $this->costo_defensa_especialidad;
        }
        if ($this->tipo === TipoCurso::Maestria) {
            $defensas += $this->costo_defensa_maestria;
        }
        return $defensas;
    }

    public function getCostoTotalProgramaAttribute(): float
    {
        return $this->costo_matricula + $this->costo_total_estudio + $this->costo_defensas;
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }
}
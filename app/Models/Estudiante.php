<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estudiante extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombres',
        'paterno',
        'materno',
        'registro',
        'cedula',
        'celular',
        'observaciones',
        'descuento_porcentaje',
        'activo',
    ];

    protected $casts = [
        'descuento_porcentaje' => 'integer',
        'activo' => 'boolean',
    ];

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombres} {$this->paterno} {$this->materno}");
    }

    public function getInicialesAttribute(): string
    {
        $iniciales = strtoupper(substr($this->nombres, 0, 1));
        if ($this->paterno) {
            $iniciales .= strtoupper(substr($this->paterno, 0, 1));
        }
        return $iniciales;
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }
}

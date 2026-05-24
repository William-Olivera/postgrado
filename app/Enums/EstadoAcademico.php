<?php

namespace App\Enums;

enum EstadoAcademico: string
{
    case Pendiente = 'Pendiente';
    case Activo = 'Activo';
    case Congelado = 'Congelado';
    case Egresado = 'Egresado';
    case Retirado = 'Retirado';

    public function label(): string
    {
        return match($this) {
            self::Pendiente => 'Pendiente',
            self::Activo => 'Activo',
            self::Congelado => 'Congelado',
            self::Egresado => 'Egresado',
            self::Retirado => 'Retirado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pendiente => '#f59e0b', // amarillo
            self::Activo => '#22c55e',    // verde
            self::Congelado => '#64748b', // gris
            self::Egresado => '#3b82f6',  // azul
            self::Retirado => '#ef4444',  // rojo
        };
    }
}
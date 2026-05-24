<?php

namespace App\Enums;

enum EstadoFinanciero: string
{
    case SinPagar = 'Sin Pagar';
    case Parcial = 'Parcial';
    case AlDia = 'Al Día';
    case Completado = 'Completado';

    public function label(): string
    {
        return match($this) {
            self::SinPagar => 'Sin Pagar',
            self::Parcial => 'Parcial',
            self::AlDia => 'Al Día',
            self::Completado => 'Completado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::SinPagar => '#ef4444',   // rojo
            self::Parcial => '#f59e0b',    // amarillo
            self::AlDia => '#22c55e',      // verde
            self::Completado => '#6366f1', // índigo
        };
    }
}
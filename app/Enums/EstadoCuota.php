<?php

namespace App\Enums;

enum EstadoCuota: string
{
    case Pendiente = 'Pendiente';
    case Pagado = 'Pagado';
    case Parcial = 'Parcial';
    case Vencido = 'Vencido';
    case Condonado = 'Condonado';

    public function label(): string
    {
        return $this->value;
    }

    public function color(): string
    {
        return match ($this) {
            self::Pendiente => '#f59e0b',
            self::Pagado => '#22c55e',
            self::Parcial => '#3b82f6',
            self::Vencido => '#ef4444',
            self::Condonado => '#64748b',
        };
    }
}

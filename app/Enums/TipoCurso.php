<?php

namespace App\Enums;

enum TipoCurso: string
{
    case Diplomado = 'Diplomado';
    case Especialidad = 'Especialidad';
    case Maestria = 'Maestría';

    public function label(): string
    {
        return $this->value;
    }

    public function modulosDefault(): int
    {
        return match($this) {
            self::Diplomado => 5,
            self::Especialidad => 11,
            self::Maestria => 16,
        };
    }

    public function defensas(): array
    {
        return match($this) {
            self::Diplomado => ['diplomado'],
            self::Especialidad => ['diplomado', 'especialidad'],
            self::Maestria => ['diplomado', 'especialidad', 'maestria'],
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Diplomado => '#3b82f6',
            self::Especialidad => '#8b5cf6',
            self::Maestria => '#f59e0b',
        };
    }

    public function icono(): string
    {
        return match($this) {
            self::Diplomado => 'fa-certificate',
            self::Especialidad => 'fa-graduation-cap',
            self::Maestria => 'fa-university',
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function options(): array
    {
        return array_map(fn($case) => ['value' => $case->value, 'label' => $case->label()], self::cases());
    }
}
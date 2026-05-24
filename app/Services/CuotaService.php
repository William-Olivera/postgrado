<?php
namespace App\Services;

use App\Models\Curso;
use App\Models\Inscripcion;
use App\Models\PlanPago;
use App\Models\DetallePlanPago;

class CuotaService
{
    /**
     * Genera las cuotas automáticamente al inscribir un estudiante
     */
    public function generarCuotas(Inscripcion $inscripcion): PlanPago
    {
        $curso = $inscripcion->curso;
        $estudiante = $inscripcion->estudiante;
        $descuento = $estudiante->descuento_porcentaje; // 0, 50, 100

        $cuotas = [];
        $total = 0;

        // Matrícula (Cuota 0)
        $matricula = $this->aplicarDescuento($curso->costo_matricula, $descuento);
        $cuotas[] = ['nro' => 0, 'concepto' => 'Matrícula', 'monto' => $matricula];
        $total += $matricula;

        // Módulos según tipo
        $cantidadModulos = match($curso->tipo) {
            'Diplomado' => 5,
            'Especialidad' => 5,
            'Maestría' => 4,
            default => 0,
        };

        for ($i = 1; $i <= $cantidadModulos; $i++) {
            $monto = $this->aplicarDescuento($curso->costo_modulo, $descuento);
            $cuotas[] = ['nro' => $i, 'concepto' => "Módulo {$i}", 'monto' => $monto];
            $total += $monto;
        }

        // Defensa según fase
        $defensa = match(true) {
            $inscripcion->estado === 'Egresado' && $curso->tipo === 'Maestría' => $curso->costo_defensa_maestria,
            $inscripcion->estado === 'Egresado' && $curso->tipo === 'Especialidad' => $curso->costo_defensa_especialidad,
            $inscripcion->estado === 'Egresado' && $curso->tipo === 'Diplomado' => $curso->costo_defensa_diplomado,
            default => 0,
        };

        if ($defensa > 0) {
            $defensa = $this->aplicarDescuento($defensa, $descuento);
            $cuotas[] = ['nro' => 99, 'concepto' => "Defensa {$curso->tipo}", 'monto' => $defensa];
            $total += $defensa;
        }

        // Crear Plan de Pago
        $plan = PlanPago::create([
            'inscripcion_id' => $inscripcion->id,
            'monto_total_programado' => $total,
            'saldo_pendiente' => $total,
            'total_cuotas' => count($cuotas),
            'estado' => 'Pendiente',
        ]);

        foreach ($cuotas as $c) {
            DetallePlanPago::create([
                'plan_pago_id' => $plan->id,
                'nro_cuota' => $c['nro'],
                'concepto' => $c['concepto'],
                'monto_programado' => $c['monto'],
                'saldo_cuota' => $c['monto'],
                'estado' => 'Pendiente',
            ]);
        }

        return $plan;
    }

    private function aplicarDescuento(float $monto, int $porcentaje): float
    {
        return $monto - ($monto * $porcentaje / 100);
    }
}

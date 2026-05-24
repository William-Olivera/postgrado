<?php

namespace App\Services;

use App\Enums\EstadoAcademico;
use App\Enums\EstadoFinanciero;
use App\Enums\EstadoCuota;
use App\Enums\TipoCurso;
use App\Models\Curso;
use App\Models\DetallePlanPago;
use App\Models\Estudiante;
use App\Models\Inscripcion;
use App\Models\PlanPago;
use Illuminate\Support\Facades\DB;

class InscripcionService
{
    public function inscribir(
        Estudiante $estudiante,
        Curso $curso,
        string $tipoInscripcion = 'completo',
        string $modalidadPago = 'Cuotas',
        ?string $observacion = null
    ): Inscripcion {
        return DB::transaction(function () use ($estudiante, $curso, $tipoInscripcion, $modalidadPago, $observacion) {
            
            $tipoInscripcionEnum = TipoCurso::from($tipoInscripcion);
            
            // 1. Crear la inscripción con los dos estados nuevos
            $inscripcion = Inscripcion::create([
                'estudiante_id' => $estudiante->id,
                'curso_id' => $curso->id,
                'tipo_inscripcion' => $tipoInscripcionEnum,
                'fecha_inscripcion' => now(),
                'estado_academico' => EstadoAcademico::Pendiente,    // ← Usar EstadoAcademico
                'estado_financiero' => EstadoFinanciero::SinPagar,   // ← Usar EstadoFinanciero
                'modalidad_pago' => $modalidadPago,
                'observacion' => $observacion,
            ]);

            $detalles = $this->generarCuotas($curso, $tipoInscripcionEnum, $estudiante->descuento_porcentaje);
            $montoTotal = collect($detalles)->sum('monto_programado');
            $totalCuotas = count($detalles);

            // 2. Crear el plan de pagos cabecera
            $planPago = PlanPago::create([
                'inscripcion_id' => $inscripcion->id,
                'monto_total_programado' => $montoTotal,
                'monto_total_pagado' => 0,
                'saldo_pendiente' => $montoTotal,
                'total_cuotas' => $totalCuotas,
                'estado' => 'Pendiente',
            ]);

            foreach ($detalles as $detalle) {
                $detalle['plan_pago_id'] = $planPago->id;
                DetallePlanPago::create($detalle);
            }

            return $inscripcion->load('planPago.detalles');
        });
    }

    private function generarCuotas(Curso $curso, TipoCurso $tipoInscripcion, int $descuentoEstudiante): array
    {
        $cuotas = [];
        $nroCuota = 1;
        
        // Factor de descuento SOLO para módulos
        $factorModulo = (100 - $descuentoEstudiante) / 100;
        $costoModulo = $curso->costo_modulo;

        // ==========================================
        // FASE DIPLOMADO
        // ==========================================
        
        // 1. MATRÍCULA (SIN DESCUENTO)
        $cuotas[] = [
            'nro_cuota' => $nroCuota++,
            'nro_modulo' => 0, // Matrícula es módulo 0 lógicamente para el sistema
            'concepto' => 'Matricula',
            'fase' => 'Diplomado',
            'monto_programado' => round($curso->costo_matricula, 2),
            'monto_pagado' => 0,
            'monto_descuento' => 0,
            'saldo_cuota' => round($curso->costo_matricula, 2),
            'fecha_vencimiento' => now()->addDays(7)->toDateString(),
            'estado' => EstadoCuota::Pendiente->value,
        ];

        // 2. CUOTAS MÓDULOS DIPLOMADO (CON DESCUENTO)
        for ($i = 1; $i <= $curso->nro_modulos_diplomado; $i++) {
            $montoConDescuento = round($costoModulo * $factorModulo, 2);
            $montoOriginal = round($costoModulo, 2);
            
            $cuotas[] = [
                'nro_cuota' => $nroCuota++,
                'nro_modulo' => $i,
                'concepto' => "Deposito Diplomado {$i}",
                'fase' => 'Diplomado',
                'monto_programado' => $montoConDescuento,
                'monto_pagado' => 0,
                'monto_descuento' => round($montoOriginal - $montoConDescuento, 2),
                'saldo_cuota' => $montoConDescuento,
                'fecha_vencimiento' => now()->addMonths($i)->toDateString(),
                'estado' => EstadoCuota::Pendiente->value,
            ];
        }

        // 3. DEFENSA DIPLOMADO (SIN DESCUENTO)
        $cuotas[] = [
            'nro_cuota' => $nroCuota++,
            'concepto' => 'Defensa Diplomado',
            'fase' => 'Diplomado',
            'monto_programado' => round($curso->costo_defensa_diplomado, 2),
            'monto_pagado' => 0,
            'monto_descuento' => 0,
            'saldo_cuota' => round($curso->costo_defensa_diplomado, 2),
            'fecha_vencimiento' => now()->addMonths($curso->nro_modulos_diplomado + 1)->toDateString(),
            'estado' => EstadoCuota::Pendiente->value,
        ];

        if ($tipoInscripcion === TipoCurso::Diplomado) {
            return $cuotas;
        }

        // ==========================================
        // FASE ESPECIALIDAD
        // ==========================================
        $modulosEsp = $curso->nro_modulos_especialidad ?? 0;
        
        if ($modulosEsp > 0) {
            for ($i = 1; $i <= $modulosEsp; $i++) {
                $montoConDescuento = round($costoModulo * $factorModulo, 2);
                $montoOriginal = round($costoModulo, 2);
                
                $cuotas[] = [
                    'nro_cuota' => $nroCuota++,
                    'nro_modulo' => $i,
                    'concepto' => "Deposito Especialidad {$i}",
                    'fase' => 'Especialidad',
                    'monto_programado' => $montoConDescuento,
                    'monto_pagado' => 0,
                    'monto_descuento' => round($montoOriginal - $montoConDescuento, 2),
                    'saldo_cuota' => $montoConDescuento,
                    'fecha_vencimiento' => now()->addMonths($curso->nro_modulos_diplomado + $i)->toDateString(),
                    'estado' => EstadoCuota::Pendiente->value,
                ];
            }

            $cuotas[] = [
                'nro_cuota' => $nroCuota++,
                'concepto' => 'Defensa Especialidad',
                'fase' => 'Especialidad',
                'monto_programado' => round($curso->costo_defensa_especialidad, 2),
                'monto_pagado' => 0,
                'monto_descuento' => 0,
                'saldo_cuota' => round($curso->costo_defensa_especialidad, 2),
                'fecha_vencimiento' => now()->addMonths($curso->nro_modulos_diplomado + $modulosEsp + 1)->toDateString(),
                'estado' => EstadoCuota::Pendiente->value,
            ];
        }

        if ($tipoInscripcion === TipoCurso::Especialidad) {
            return $cuotas;
        }

        // ==========================================
        // FASE MAESTRÍA
        // ==========================================
        $modulosMae = $curso->nro_modulos_maestria ?? 0;
        
        if ($modulosMae > 0) {
            for ($i = 1; $i <= $modulosMae; $i++) {
                $montoConDescuento = round($costoModulo * $factorModulo, 2);
                $montoOriginal = round($costoModulo, 2);
                
                $cuotas[] = [
                    'nro_cuota' => $nroCuota++,
                    'nro_modulo' => $i,
                    'concepto' => "Deposito Maestria {$i}",
                    'fase' => 'Maestria',
                    'monto_programado' => $montoConDescuento,
                    'monto_pagado' => 0,
                    'monto_descuento' => round($montoOriginal - $montoConDescuento, 2),
                    'saldo_cuota' => $montoConDescuento,
                    'fecha_vencimiento' => now()->addMonths($curso->nro_modulos_diplomado + $modulosEsp + $i)->toDateString(),
                    'estado' => EstadoCuota::Pendiente->value,
                ];
            }

            $cuotas[] = [
                'nro_cuota' => $nroCuota++,
                'concepto' => 'Defensa Maestria',
                'fase' => 'Maestria',
                'monto_programado' => round($curso->costo_defensa_maestria, 2),
                'monto_pagado' => 0,
                'monto_descuento' => 0,
                'saldo_cuota' => round($curso->costo_defensa_maestria, 2),
                'fecha_vencimiento' => now()->addMonths($curso->nro_modulos_diplomado + $modulosEsp + $modulosMae + 1)->toDateString(),
                'estado' => EstadoCuota::Pendiente->value,
            ];
        }

        return $cuotas;
    }
}
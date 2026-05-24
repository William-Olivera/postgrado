<?php

namespace Database\Seeders;

use App\Enums\RolUsuario;
use App\Enums\TipoCurso;
use App\Models\Configuracion;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Pago;
use App\Models\User;
use App\Services\InscripcionService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PostgradoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@finor.edu.bo'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'rol' => RolUsuario::Administrador,
                'activo' => true,
            ]
        );

        Configuracion::truncate();
        Configuracion::insert([
            ['clave' => 'matricula_default', 'valor' => '500', 'descripcion' => 'Costo matricula estandar'],
            ['clave' => 'defensa_diplomado', 'valor' => '500', 'descripcion' => 'Defensa fase diplomado'],
            ['clave' => 'defensa_especialidad', 'valor' => '700', 'descripcion' => 'Defensa fase especialidad'],
            ['clave' => 'defensa_maestria', 'valor' => '4000', 'descripcion' => 'Defensa fase maestria'],
        ]);

        $curso = Curso::updateOrCreate(
            ['nombre' => 'Maestria en Educacion Superior', 'version' => 4, 'edicion' => 2],
            [
                'tipo' => TipoCurso::Maestria,
                'periodo' => '2026-1',
                'costo_matricula' => 500,
                'costo_total_estudio' => 12500,
                'costo_defensa_diplomado' => 500,
                'costo_defensa_especialidad' => 700,
                'costo_defensa_maestria' => 4000,
                'nro_modulos_diplomado' => 5,
                'nro_modulos_especialidad' => 6,
                'nro_modulos_maestria' => 5,
                'cupo' => 30,
                'activo' => true,
            ]
        );

        $est1 = Estudiante::firstOrCreate(
            ['registro' => '219091846'],
            [
                'nombres' => 'William Ezequiel',
                'paterno' => 'Olivera',
                'materno' => null,
                'cedula' => '17749814',
                'celular' => '72118349',
                'observaciones' => null,
                'descuento_porcentaje' => 50,
            ]
        );

        $est2 = Estudiante::firstOrCreate(
            ['registro' => '220045362'],
            [
                'nombres' => 'Carlos Manuel',
                'paterno' => 'Ferrel',
                'materno' => 'Escobar',
                'cedula' => '15796548',
                'celular' => '69058761',
                'observaciones' => null,
                'descuento_porcentaje' => 50,
            ]
        );

        $est3 = Estudiante::firstOrCreate(
            ['registro' => '221245781'],
            [
                'nombres' => 'Carlos Daniel',
                'paterno' => 'Cortez',
                'materno' => 'Barco',
                'cedula' => '18597584',
                'celular' => '71085649',
                'observaciones' => null,
                'descuento_porcentaje' => 50,
            ]
        );

        $est4 = Estudiante::firstOrCreate(
            ['registro' => '221292346'],
            [
                'nombres' => 'Rodrigo',
                'paterno' => 'Parada',
                'materno' => 'Bazan',
                'cedula' => '15187469',
                'celular' => '68830260',
                'observaciones' => null,
                'descuento_porcentaje' => 0,
            ]
        );

        $service = new InscripcionService();

        $ins1 = \App\Models\Inscripcion::where('estudiante_id', $est1->id)->where('curso_id', $curso->id)->first()
               ?? $service->inscribir($est1, $curso, TipoCurso::Maestria->value, 'Cuotas');
        $ins2 = \App\Models\Inscripcion::where('estudiante_id', $est2->id)->where('curso_id', $curso->id)->first()
               ?? $service->inscribir($est2, $curso, TipoCurso::Maestria->value, 'Cuotas');
        $ins3 = \App\Models\Inscripcion::where('estudiante_id', $est3->id)->where('curso_id', $curso->id)->first()
               ?? $service->inscribir($est3, $curso, TipoCurso::Maestria->value, 'Cuotas');
        $ins4 = \App\Models\Inscripcion::where('estudiante_id', $est4->id)->where('curso_id', $curso->id)->first()
               ?? $service->inscribir($est4, $curso, TipoCurso::Maestria->value, 'Cuotas');

        $fechaIns = '2026-04-21';
        $compsMat = ['21649751', '24631592', '20548921', '25862489'];
        $compsCuota = ['21649752', '24631593', '20548922', '25862490'];
        $cuentas = ['29873549', '23651821', '24060081', '26125975'];

        $inscripciones = [$ins1, $ins2, $ins3, $ins4];

        foreach ($inscripciones as $i => $inscripcion) {
            $detalleMatricula = $inscripcion->planPago->detalles->where('concepto', 'Matricula')->first();

            if ($detalleMatricula && $detalleMatricula->pagos->count() === 0) {
                Pago::create([
                    'detalle_plan_pago_id' => $detalleMatricula->id,
                    'inscripcion_id' => $inscripcion->id,
                    'fecha_pago' => $fechaIns,
                    'monto' => 500.00,
                    'nro_comprobante' => $compsMat[$i],
                    'observacion' => 'Pago de matrícula inicial',
                ]);
            }
        }

        /*
        foreach ($inscripciones as $i => $inscripcion) {
            $detalleCuota = $inscripcion->planPago->detalles->where('concepto', 'Deposito Diplomado 1')->first();

            if ($detalleCuota && $detalleCuota->pagos->count() === 0) {
                Pago::create([
                    'detalle_plan_pago_id' => $detalleCuota->id,
                    'inscripcion_id' => $inscripcion->id,
                    'fecha_pago' => $fechaIns,
                    'monto' => $detalleCuota->monto_programado,
                    'nro_comprobante' => $compsCuota[$i],
                    'banco_destino' => 'Banco Union',
                    'cuenta_destino' => $cuentas[$i],
                    'registrado_por' => $admin->id,
                ]);

                $detalleCuota->update([
                    'monto_pagado' => $detalleCuota->monto_programado,
                    'saldo_cuota' => 0,
                    'estado' => \App\Enums\EstadoCuota::Pagado->value,
                ]);
            }
        }
        */

        $this->command->info('✅ Postgrado seedeado: 4 estudiantes inscritos, pagos registrados.');

        // Asegurar que los estados se actualicen (disparar eventos si no se dispararon)
        foreach (\App\Models\Inscripcion::all() as $ins) {
            if ($ins->planPago) {
                $ins->planPago->recalcularTotales();
            }
        }
    }
}

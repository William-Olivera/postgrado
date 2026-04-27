<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Pago;
use App\Models\PlanPago;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ControlSaldosController extends Controller
{
    /**
     * Estudiantes con al menos una inscripción (activos en el sistema).
     */
    public function buscarEstudiantes(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'campo' => ['required', Rule::in(['nombre', 'registro', 'cedula'])],
            'q' => ['required', 'string', 'min:1', 'max:120'],
        ]);

        $q = trim($validated['q']);

        $query = Estudiante::query()
            ->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('Inscripcion')
                    ->whereColumn('Inscripcion.Id_E', 'Estudiante.Id_E');
            });

        match ($validated['campo']) {
            'nombre' => $query->where(function ($w) use ($q) {
                $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $q).'%';
                $w->where('nombreE', 'like', $like)
                    ->orWhere('paternoE', 'like', $like)
                    ->orWhere('maternoE', 'like', $like)
                    ->orWhereRaw(
                        "CONCAT(COALESCE(nombreE,''), ' ', COALESCE(paternoE,''), ' ', COALESCE(maternoE,'')) LIKE ?",
                        [$like]
                    );
            }),
            'registro' => $query->where('RegistroE', $q),
            'cedula' => $query->where('CedulaE', 'like', '%'.str_replace(['%', '_'], ['\\%', '\\_'], $q).'%'),
        };

        $rows = $query
            ->orderBy('paternoE')
            ->orderBy('nombreE')
            ->get()
            ->map(fn (Estudiante $e) => [
                'Id_E' => $e->Id_E,
                'nombre_completo' => $e->nombreCompleto(),
                'RegistroE' => $e->RegistroE,
                'CedulaE' => $e->CedulaE,
            ]);

        return response()->json(['data' => $rows]);
    }

    public function saldoEstudiante(int $id): JsonResponse
    {
        $estudiante = Estudiante::query()->find($id);
        if (! $estudiante) {
            return response()->json(['message' => 'Estudiante no encontrado.'], 404);
        }

        $tieneInscripcion = DB::table('Inscripcion')->where('Id_E', $id)->exists();
        if (! $tieneInscripcion) {
            return response()->json(['message' => 'El estudiante no tiene inscripciones activas.'], 422);
        }

        $cursosIds = DB::table('Inscripcion')->where('Id_E', $id)->pluck('Id_Cur')->unique()->values();

        $montoPagado = (float) Pago::query()->where('Id_E', $id)->sum('MontoP');

        $montoTotal = (float) PlanPago::query()
            ->whereIn('Id_Cur', $cursosIds)
            ->sum('MontoTotalPP');

        $montoMora = round(max(0, $montoTotal - $montoPagado), 2);

        return response()->json([
            'data' => [
                'Id_E' => $estudiante->Id_E,
                'nombre_completo' => $estudiante->nombreCompleto(),
                'monto_pagado' => round($montoPagado, 2),
                'monto_mora' => $montoMora,
                'monto_total' => round($montoTotal, 2),
            ],
        ]);
    }

    public function saldosPorCurso(): JsonResponse
    {
        $cursosActivos = Curso::query()
            ->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('Inscripcion')
                    ->whereColumn('Inscripcion.Id_Cur', 'Curso.Id_Cur');
            })
            ->orderBy('NombreCur')
            ->get();

        $out = [];
        foreach ($cursosActivos as $curso) {
            $n = (int) DB::table('Inscripcion')->where('Id_Cur', $curso->Id_Cur)->count('Id_E');
            $planTotal = (float) PlanPago::query()->where('Id_Cur', $curso->Id_Cur)->sum('MontoTotalPP');
            $montoTotal = round($planTotal * $n, 2);
            $montoPagado = (float) Pago::query()->where('Id_Cur', $curso->Id_Cur)->sum('MontoP');
            $montoPagado = round($montoPagado, 2);
            $montoMora = round(max(0, $montoTotal - $montoPagado), 2);

            $out[] = [
                'Id_Cur' => $curso->Id_Cur,
                'nombre_curso' => $curso->NombreCur,
                'cantidad_estudiantes' => $n,
                'monto_pagado' => $montoPagado,
                'monto_mora' => $montoMora,
                'monto_total' => $montoTotal,
            ];
        }

        return response()->json(['data' => $out]);
    }

    public function saldosGestion(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'anio' => ['required', 'integer', 'min:1990', 'max:2100'],
        ]);

        $anio = (int) $validated['anio'];

        $inscripciones = DB::table('Inscripcion')
            ->whereYear('FechaIns', $anio)
            ->get(['Id_E', 'Id_Cur']);

        if ($inscripciones->isEmpty()) {
            return response()->json([
                'data' => [
                    'anio' => $anio,
                    'monto_pagado' => 0.0,
                    'monto_mora' => 0.0,
                    'monto_total' => 0.0,
                ],
            ]);
        }

        $montoTotal = 0.0;
        $montoPagado = 0.0;

        foreach ($inscripciones as $row) {
            $plan = PlanPago::query()->where('Id_Cur', $row->Id_Cur)->first();
            if ($plan) {
                $montoTotal += (float) $plan->MontoTotalPP;
            }
            $montoPagado += (float) Pago::query()
                ->where('Id_E', $row->Id_E)
                ->where('Id_Cur', $row->Id_Cur)
                ->sum('MontoP');
        }

        $montoTotal = round($montoTotal, 2);
        $montoPagado = round($montoPagado, 2);
        $montoMora = round(max(0, $montoTotal - $montoPagado), 2);

        return response()->json([
            'data' => [
                'anio' => $anio,
                'monto_pagado' => $montoPagado,
                'monto_mora' => $montoMora,
                'monto_total' => $montoTotal,
            ],
        ]);
    }
}

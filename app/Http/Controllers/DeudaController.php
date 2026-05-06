<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Pago;
use App\Models\PlanPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeudaController extends Controller
{
    public function index(Request $request)
    {
        $cursos = Curso::orderBy('NombreCur')->get();
        
        $estudiantes = Estudiante::query()
            ->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('Inscripcion')
                    ->whereColumn('Inscripcion.Id_E', 'Estudiante.Id_E');
            })
            ->orderBy('paternoE')
            ->orderBy('nombreE')
            ->get();

        $deudas = [];
        $deudaTotal = 0;
        $estudiantesConDeuda = 0;

        foreach ($estudiantes as $estudiante) {
            $cursosIds = DB::table('Inscripcion')
                ->where('Id_E', $estudiante->Id_E)
                ->pluck('Id_Cur')
                ->unique()
                ->values();

            $montoPagado = (float) Pago::query()->where('Id_E', $estudiante->Id_E)->sum('MontoP');
            $montoTotal = (float) PlanPago::query()
                ->whereIn('Id_Cur', $cursosIds)
                ->sum('MontoTotalPP');
            $saldoPendiente = round(max(0, $montoTotal - $montoPagado), 2);

            if ($request->filled('curso')) {
                $cursoFiltro = $request->input('curso');
                $tieneCurso = DB::table('Inscripcion')
                    ->where('Id_E', $estudiante->Id_E)
                    ->where('Id_Cur', $cursoFiltro)
                    ->exists();
                if (! $tieneCurso) continue;
            }

            if ($request->filled('q')) {
                $q = strtolower($request->input('q'));
                $nombreCompleto = strtolower($estudiante->nombreCompleto());
                $cedula = strtolower($estudiante->CedulaE);
                if (strpos($nombreCompleto, $q) === false && strpos($cedula, $q) === false) {
                    continue;
                }
            }

            if ($saldoPendiente > 0) {
                $deudaTotal += $saldoPendiente;
                $estudiantesConDeuda++;
            }

            $cursoNombre = '';
            $cursoId = $cursosIds->first();
            if ($cursoId) {
                $curso = Curso::find($cursoId);
                $cursoNombre = $curso ? $curso->NombreCur : '';
            }

            $deudas[] = [
                'estudiante' => $estudiante,
                'curso' => $cursoNombre,
                'costoTotal' => $montoTotal,
                'pagado' => $montoPagado,
                'saldoPendiente' => $saldoPendiente,
                'progreso' => $montoTotal > 0 ? round(($montoPagado / $montoTotal) * 100, 0) : 0,
            ];
        }

        $deudaPromedio = $estudiantesConDeuda > 0 ? round($deudaTotal / $estudiantesConDeuda, 2) : 0;

        return view('deudas.index', compact('deudas', 'cursos', 'deudaTotal', 'estudiantesConDeuda', 'deudaPromedio'));
    }
}
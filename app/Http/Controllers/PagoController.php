<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Pago;
use App\Models\PlanPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PagoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pago::query()
            ->with(['estudiante', 'curso'])
            ->orderBy('FechaP', 'desc');

        if ($request->filled('tipo') && $request->input('tipo') !== 'todos') {
            $query->where('TipoP', $request->input('tipo'));
        }
        if ($request->filled('curso')) {
            $query->where('Id_Cur', $request->input('curso'));
        }
        if ($request->filled('fecha')) {
            $query->whereDate('FechaP', $request->input('fecha'));
        }
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->whereHas('estudiante', function ($w) use ($q) {
                $like = '%' . $q . '%';
                $w->where('nombreE', 'like', $like)
                  ->orWhere('paternoE', 'like', $like)
                  ->orWhere('maternoE', 'like', $like)
                  ->orWhere('RegistroE', 'like', $like)
                  ->orWhere('CedulaE', 'like', $like);
            });
        }

        $perPage = in_array($request->input('perPage'), [10, 25, 50, 100]) ? $request->input('perPage') : 10;
        $pagos = $query->paginate($perPage)->withQueryString();
        $cursos = Curso::orderBy('NombreCur')->get();

        $totalRecaudado = Pago::sum('MontoP');
        $pagosParciales = Pago::where('TipoP', 'Cuota')->count();
        $pagosTotales = Pago::whereIn('TipoP', ['Matricula', 'Defensa'])->count();

        return view('pagos.index', compact('pagos', 'cursos', 'totalRecaudado', 'pagosParciales', 'pagosTotales', 'perPage'));
    }

    public function create()
    {
        $cursos = Curso::orderBy('NombreCur')->get();
        $estudiantes = Estudiante::orderBy('paternoE')->orderBy('nombreE')->get();
        $estudiantesData = $estudiantes->map(function ($est) {
            return [
                'id' => $est->Id_E,
                'label' => trim($est->paternoE . ' ' . $est->nombreE . ' ' . $est->maternoE),
                'ci' => $est->CedulaE,
                'registro' => $est->RegistroE,
            ];
        })->values();

        return view('pagos.create', compact('cursos', 'estudiantes', 'estudiantesData'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Id_E' => ['required', 'integer', 'exists:Estudiante,Id_E'],
            'Id_Cur' => ['required', 'integer', 'exists:Curso,Id_Cur'],
            'MontoP' => ['required', 'numeric', 'min:0.01'],
            'TipoP' => ['required', Rule::in(['Matricula', 'Cuota', 'Defensa'])],
            'NroP' => ['required', 'integer', 'min:0', 'max:6'],
            'FechaP' => ['required', 'date'],
            'NroCompP' => ['required', 'integer', 'min:1'],
            'CuentaTransfP' => ['required', 'string', 'max:50'],
        ]);

        $inscrito = DB::table('Inscripcion')
            ->where('Id_E', $validated['Id_E'])
            ->where('Id_Cur', $validated['Id_Cur'])
            ->exists();

        if (! $inscrito) {
            throw ValidationException::withMessages([
                'Id_E' => ['El estudiante no esta inscrito en el curso seleccionado.'],
            ]);
        }

        $nroEsperado = match ($validated['TipoP']) {
            'Matricula' => 0,
            'Defensa' => 6,
            'Cuota' => null,
        };

        if ($validated['TipoP'] === 'Cuota') {
            if ($validated['NroP'] < 1 || $validated['NroP'] > 5) {
                throw ValidationException::withMessages([
                    'NroP' => ['Para cuota, el numero debe estar entre 1 y 5.'],
                ]);
            }
        } elseif ($validated['NroP'] !== $nroEsperado) {
            throw ValidationException::withMessages([
                'NroP' => ['El numero de pago no coincide con el tipo seleccionado.'],
            ]);
        }

        $plan = PlanPago::query()->where('Id_Cur', $validated['Id_Cur'])->first();
        if (! $plan) {
            throw ValidationException::withMessages([
                'Id_Cur' => ['No existe un plan de pago para este curso.'],
            ]);
        }

        $duplicado = Pago::query()
            ->where('Id_E', $validated['Id_E'])
            ->where('Id_Cur', $validated['Id_Cur'])
            ->where('TipoP', $validated['TipoP'])
            ->where('NroP', $validated['NroP'])
            ->exists();

        if ($duplicado) {
            throw ValidationException::withMessages([
                'TipoP' => ['Ya existe un pago registrado con el mismo tipo y numero para este estudiante y curso.'],
            ]);
        }

        Pago::query()->create([
            'Id_PP' => $plan->Id_PP,
            'Id_E' => $validated['Id_E'],
            'Id_Cur' => $validated['Id_Cur'],
            'FechaP' => $validated['FechaP'],
            'MontoP' => $validated['MontoP'],
            'NroP' => $validated['NroP'],
            'TipoP' => $validated['TipoP'],
            'NroCompP' => $validated['NroCompP'],
            'CuentaTransfP' => $validated['CuentaTransfP'],
        ]);

        return redirect()->route('pagos.index')->with('success', 'Pago registrado correctamente.');
    }
}
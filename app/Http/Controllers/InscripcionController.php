<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InscripcionController extends Controller
{
    private function getActivePeriod(): string
    {
        return Curso::orderByDesc('PeriodoCur')->value('PeriodoCur') ?? date('Y') . '-I';
    }

    public function index(Request $request)
    {
        $periodos = Curso::select('PeriodoCur')
            ->distinct()
            ->orderByDesc('PeriodoCur')
            ->pluck('PeriodoCur');

        $activePeriod = $request->filled('periodo') ? $request->input('periodo') : $this->getActivePeriod();

        $query = DB::table('Inscripcion')
            ->join('Estudiante', 'Inscripcion.Id_E', '=', 'Estudiante.Id_E')
            ->join('Curso', 'Inscripcion.Id_Cur', '=', 'Curso.Id_Cur')
            ->select(
                'Inscripcion.*',
                'Estudiante.nombreE',
                'Estudiante.paternoE',
                'Estudiante.maternoE',
                'Curso.NombreCur',
                'Curso.PeriodoCur'
            )
            ->where('Curso.PeriodoCur', $activePeriod)
            ->orderBy('Inscripcion.FechaIns', 'desc');

        if ($request->filled('curso')) {
            $query->where('Inscripcion.Id_Cur', $request->input('curso'));
        }
        if ($request->filled('estado')) {
            $query->where('Inscripcion.EstadoIns', $request->input('estado'));
        }

        $inscripciones = $query->paginate(10);
        $cursos = Curso::where('PeriodoCur', $activePeriod)
            ->orderBy('NombreCur')
            ->get();

        return view('inscripciones.index', compact('inscripciones', 'cursos', 'periodos', 'activePeriod'));
    }

    public function create()
    {
        $activePeriod = $this->getActivePeriod();

        $estudiantes = Estudiante::where('ActivoE', true)
            ->orderBy('paternoE')
            ->orderBy('nombreE')
            ->get();

        $cursos = Curso::withCount('estudiantes')
            ->where('PeriodoCur', $activePeriod)
            ->get()
            ->filter(function ($curso) {
                return $curso->CupoCur > $curso->estudiantes_count;
            });

        return view('inscripciones.create', compact('estudiantes', 'cursos', 'activePeriod'));
    }

    public function store(Request $request)
    {
        $activePeriod = $this->getActivePeriod();

        $validated = $request->validate([
            'Id_E' => ['required', 'integer', 'exists:Estudiante,Id_E'],
            'Id_Cur' => ['required', 'integer', 'exists:Curso,Id_Cur'],
            'FechaIns' => ['required', 'date'],
            'EstadoIns' => ['required', 'in:Completada,Pendiente'],
        ]);

        $curso = Curso::withCount('estudiantes')->find($validated['Id_Cur']);

        if (!$curso || $curso->PeriodoCur !== $activePeriod) {
            throw ValidationException::withMessages([
                'Id_Cur' => ['El curso seleccionado no pertenece al período académico activo.'],
            ]);
        }

        if ($curso->CupoCur <= $curso->estudiantes_count) {
            throw ValidationException::withMessages([
                'Id_Cur' => ['El curso seleccionado ya no tiene cupos disponibles.'],
            ]);
        }

        $yaInscrito = DB::table('Inscripcion')
            ->where('Id_E', $validated['Id_E'])
            ->where('Id_Cur', $validated['Id_Cur'])
            ->exists();

        if ($yaInscrito) {
            throw ValidationException::withMessages([
                'Id_E' => ['El estudiante ya esta inscrito en este curso.'],
            ]);
        }

        DB::table('Inscripcion')->insert([
            'Id_E' => $validated['Id_E'],
            'Id_Cur' => $validated['Id_Cur'],
            'FechaIns' => $validated['FechaIns'],
            'EstadoIns' => $validated['EstadoIns'],
        ]);

        return redirect()->route('inscripciones.index')->with('success', 'Inscripcion registrada correctamente.');
    }

    public function destroy($idE, $idCur)
    {
        DB::table('Inscripcion')
            ->where('Id_E', $idE)
            ->where('Id_Cur', $idCur)
            ->delete();

        return redirect()->route('inscripciones.index')->with('success', 'Inscripcion eliminada correctamente.');
    }

}

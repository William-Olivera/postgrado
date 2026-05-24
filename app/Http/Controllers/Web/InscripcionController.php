<?php

namespace App\Http\Controllers\Web;

use App\Enums\EstadoAcademico;
use App\Enums\EstadoFinanciero;
use App\Enums\TipoCurso;
use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Inscripcion;
use App\Services\InscripcionService;
use Illuminate\Http\Request;

class InscripcionController extends Controller
{
    public function index()
    {
        return redirect()->route('cursos.index');
    }

    public function show(Curso $curso)
    {
        $curso->loadCount('inscripciones');

        $inscripciones = Inscripcion::with(['estudiante', 'planPago'])
            ->where('curso_id', $curso->id)
            ->orderBy('fecha_inscripcion', 'desc')
            ->get();

        return view('inscripciones.show', compact('curso', 'inscripciones'));
    }

    public function buscarEstudiante(Request $request)
    {
        $query = $request->get('q', '');

        $estudiantes = Estudiante::where(function ($q) use ($query) {
            $q->where('nombres', 'like', "%{$query}%")
              ->orWhere('paterno', 'like', "%{$query}%")
              ->orWhere('materno', 'like', "%{$query}%")
              ->orWhere('cedula', 'like', "%{$query}%")
              ->orWhere('registro', 'like', "%{$query}%");
        })
        ->where('activo', true)
        ->limit(10)
        ->get(['id', 'nombres', 'paterno', 'materno', 'cedula', 'registro', 'descuento_porcentaje']);

        return response()->json($estudiantes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'estudiante_id' => 'required|exists:estudiantes,id',
            'fecha_inscripcion' => 'required|date',
        ]);

        $estudiante = Estudiante::findOrFail($validated['estudiante_id']);
        $curso = Curso::findOrFail($validated['curso_id']);

        $existe = Inscripcion::where('estudiante_id', $estudiante->id)
            ->where('curso_id', $curso->id)
            ->exists();

        if ($existe) {
            return back()->with('error', 'El estudiante ya está inscrito en este curso.');
        }

        $inscritos = Inscripcion::where('curso_id', $curso->id)->count();
        if ($inscritos >= $curso->cupo) {
            return back()->with('error', 'El curso no tiene cupos disponibles.');
        }

        $service = new InscripcionService();
        $inscripcion = $service->inscribir(
            $estudiante,
            $curso,
            $curso->tipo->value,
            'Cuotas',
            null
        );

        if ($request->has('fecha_inscripcion')) {
            $inscripcion->update(['fecha_inscripcion' => $validated['fecha_inscripcion']]);
        }

        return redirect()
            ->route('inscripciones.show', $curso)
            ->with('success', "Estudiante {$estudiante->nombre_completo} inscrito exitosamente.");
    }

    public function destroy(Inscripcion $inscripcion)
    {
        $curso = $inscripcion->curso;
        $inscripcion->delete();

        return redirect()
            ->route('inscripciones.show', $curso)
            ->with('success', 'Inscripción eliminada exitosamente.');
    }
}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    public function index()
    {
        $cursos = Curso::withCount(['inscripciones as inscripciones_count'])
            ->with(['inscripciones.planPago'])
            ->orderBy('activo', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($curso) {
                // Contar inscripciones con estado académico 'Activo'
                $curso->activos_count = $curso->inscripciones
                    ->where('estado_academico', \App\Enums\EstadoAcademico::Activo)
                    ->count();

                return $curso;
            });

        return view('cursos.index', compact('cursos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'tipo' => 'required|in:Diplomado,Especialidad,Maestría',
            'version' => 'required|integer|min:1',
            'edicion' => 'required|integer|min:1',
            'periodo' => 'required|string|max:20',
            'costo_matricula' => 'required|numeric|min:0',
            'costo_total_estudio' => 'required|numeric|min:0',
            'costo_defensa_diplomado' => 'required|numeric|min:0',
            'costo_defensa_especialidad' => 'nullable|numeric|min:0',
            'costo_defensa_maestria' => 'nullable|numeric|min:0',
            'nro_modulos_diplomado' => 'required|integer|min:1',
            'nro_modulos_especialidad' => 'nullable|integer|min:0',
            'nro_modulos_maestria' => 'nullable|integer|min:0',
            'cupo' => 'required|integer|min:1',
        ]);

        // Asegurar que los nullable sean 0 si no vienen
        $validated['costo_defensa_especialidad'] = $validated['costo_defensa_especialidad'] ?? 0;
        $validated['costo_defensa_maestria'] = $validated['costo_defensa_maestria'] ?? 0;
        $validated['nro_modulos_especialidad'] = $validated['nro_modulos_especialidad'] ?? 0;
        $validated['nro_modulos_maestria'] = $validated['nro_modulos_maestria'] ?? 0;

        Curso::create($validated);

        return redirect()->route('cursos.index')->with('success', 'Curso creado exitosamente.');
    }

    public function update(Request $request, Curso $curso)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'tipo' => 'required|in:Diplomado,Especialidad,Maestría',
            'version' => 'required|integer|min:1',
            'edicion' => 'required|integer|min:1',
            'periodo' => 'required|string|max:20',
            'costo_matricula' => 'required|numeric|min:0',
            'costo_total_estudio' => 'required|numeric|min:0',
            'costo_defensa_diplomado' => 'required|numeric|min:0',
            'costo_defensa_especialidad' => 'nullable|numeric|min:0',
            'costo_defensa_maestria' => 'nullable|numeric|min:0',
            'nro_modulos_diplomado' => 'required|integer|min:1',
            'nro_modulos_especialidad' => 'nullable|integer|min:0',
            'nro_modulos_maestria' => 'nullable|integer|min:0',
            'cupo' => 'required|integer|min:1',
            'activo' => 'boolean',
        ]);

        $validated['costo_defensa_especialidad'] = $validated['costo_defensa_especialidad'] ?? 0;
        $validated['costo_defensa_maestria'] = $validated['costo_defensa_maestria'] ?? 0;
        $validated['nro_modulos_especialidad'] = $validated['nro_modulos_especialidad'] ?? 0;
        $validated['nro_modulos_maestria'] = $validated['nro_modulos_maestria'] ?? 0;

        $curso->update($validated);

        return redirect()->route('cursos.index')->with('success', 'Curso actualizado exitosamente.');
    }

    public function toggleActivo(Curso $curso)
    {
        $curso->update(['activo' => !$curso->activo]);

        $estado = $curso->activo ? 'activado' : 'desactivado';
        return redirect()->route('cursos.index')->with('success', "Curso {$estado} exitosamente.");
    }

    public function destroy(Curso $curso)
    {
        $curso->delete();

        return redirect()->route('cursos.index')->with('success', 'Curso eliminado exitosamente.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use Illuminate\Http\Request;

class EstudianteController extends Controller
{
    public function index(Request $request)
    {
        $query = Estudiante::query()->with('cursos');

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($w) use ($q) {
                $like = '%' . $q . '%';
                $w->where('nombreE', 'like', $like)
                  ->orWhere('paternoE', 'like', $like)
                  ->orWhere('maternoE', 'like', $like)
                  ->orWhere('RegistroE', 'like', $like)
                  ->orWhere('CedulaE', 'like', $like);
            });
        }

        $estudiantes = $query->orderBy('paternoE')->orderBy('nombreE')->paginate(10);

        return view('estudiantes.index', compact('estudiantes'));
    }

    public function create()
    {
        return view('estudiantes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombreE' => ['required', 'string', 'max:50'],
            'paternoE' => ['required', 'string', 'max:50'],
            'maternoE' => ['nullable', 'string', 'max:50'],
            'CedulaE' => ['required', 'string', 'max:20', 'unique:Estudiante,CedulaE'],
            'RegistroE' => ['required', 'integer', 'min:1', 'unique:Estudiante,RegistroE'],
            'TelefonoE' => ['nullable', 'string', 'max:20'],
            'DireccionE' => ['nullable', 'string', 'max:100'],
            'DescuentoE' => ['nullable', 'integer', 'min:0'],
            'ObservacionE' => ['nullable', 'string', 'max:50'],
        ]);

        $validated['DescuentoE'] = $validated['DescuentoE'] ?? 0;
        $validated['ObservacionE'] = $validated['ObservacionE'] ?? null;
        $validated['ActivoE'] = true;

        Estudiante::create($validated);

        return redirect()->route('estudiantes.index')->with('success', 'Estudiante registrado correctamente.');
    }

    public function show(Estudiante $estudiante)
    {
        return view('estudiantes.show', compact('estudiante'));
    }

    public function edit(Estudiante $estudiante)
    {
        return view('estudiantes.edit', compact('estudiante'));
    }

    public function update(Request $request, Estudiante $estudiante)
    {
        $validated = $request->validate([
            'nombreE' => ['required', 'string', 'max:50'],
            'paternoE' => ['required', 'string', 'max:50'],
            'maternoE' => ['nullable', 'string', 'max:50'],
            'CedulaE' => ['required', 'string', 'max:20', 'unique:Estudiante,CedulaE,' . $estudiante->Id_E . ',Id_E'],
            'RegistroE' => ['required', 'integer', 'min:1', 'unique:Estudiante,RegistroE,' . $estudiante->Id_E . ',Id_E'],
            'TelefonoE' => ['nullable', 'string', 'max:20'],
            'DireccionE' => ['nullable', 'string', 'max:100'],
            'DescuentoE' => ['nullable', 'integer', 'min:0'],
            'ObservacionE' => ['nullable', 'string', 'max:50'],
            'ActivoE' => ['required', 'boolean'],
        ]);

        $validated['DescuentoE'] = $validated['DescuentoE'] ?? 0;
        $validated['ObservacionE'] = $validated['ObservacionE'] ?? null;

        $estudiante->update($validated);

        return redirect()->route('estudiantes.index')->with('success', 'Estudiante actualizado correctamente.');
    }

    public function destroy(Estudiante $estudiante)
    {
        $estudiante->update(['ActivoE' => false]);

        return redirect()->route('estudiantes.index')->with('success', 'Estudiante dado de baja correctamente.');
    }
}
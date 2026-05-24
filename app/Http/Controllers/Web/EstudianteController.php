<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use Illuminate\Http\Request;

class EstudianteController extends Controller
{
    public function index()
    {
        $estudiantes = Estudiante::orderBy('paterno')->orderBy('nombres')->paginate(10);
        return view('estudiantes.index', compact('estudiantes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres' => ['required', 'string', 'max:50'],
            'paterno' => ['required', 'string', 'max:20'],
            'materno' => ['nullable', 'string', 'max:20'],
            'registro' => ['required', 'string', 'max:20', 'unique:estudiantes'],
            'cedula' => ['required', 'string', 'max:20', 'unique:estudiantes'],
            'celular' => ['nullable', 'string', 'max:20'],
            'observaciones' => ['nullable', 'string', 'max:255'],
            'descuento_porcentaje' => ['nullable', 'integer', 'min:0', 'max:100'],
        ], [
            'nombres.required' => 'Los nombres son obligatorios.',
            'paterno.required' => 'El apellido paterno es obligatorio.',
            'registro.required' => 'El número de registro es obligatorio.',
            'registro.unique' => 'Este registro ya existe.',
            'cedula.required' => 'La cédula de identidad es obligatoria.',
            'cedula.unique' => 'Esta cédula ya está registrada.',
            'descuento_porcentaje.max' => 'El descuento no puede superar el 100%.',
        ]);

        Estudiante::create(array_merge($request->all(), ['activo' => true]));

        return redirect()->route('estudiantes.index')->with('success', 'Estudiante registrado correctamente.');
    }

    public function update(Request $request, Estudiante $estudiante)
    {
        $request->validate([
            'nombres' => ['required', 'string', 'max:50'],
            'paterno' => ['required', 'string', 'max:20'],
            'materno' => ['nullable', 'string', 'max:20'],
            'registro' => ['required', 'string', 'max:20', 'unique:estudiantes,registro,' . $estudiante->id],
            'cedula' => ['required', 'string', 'max:20', 'unique:estudiantes,cedula,' . $estudiante->id],
            'celular' => ['nullable', 'string', 'max:20'],
            'observaciones' => ['nullable', 'string', 'max:255'],
            'descuento_porcentaje' => ['nullable', 'integer', 'min:0', 'max:100'],
        ], [
            'nombres.required' => 'Los nombres son obligatorios.',
            'paterno.required' => 'El apellido paterno es obligatorio.',
            'registro.unique' => 'Este registro ya existe.',
            'cedula.unique' => 'Esta cédula ya está registrada.',
            'descuento_porcentaje.max' => 'El descuento no puede superar el 100%.',
        ]);

        $data = $request->all();
        if (!isset($data['activo'])) {
            $data['activo'] = $estudiante->activo;
        }

        $estudiante->update($data);

        return redirect()->route('estudiantes.index')->with('success', 'Estudiante actualizado correctamente.');
    }

    public function darBaja(Estudiante $estudiante)
    {
        $estudiante->update(['activo' => false]);
        return redirect()->route('estudiantes.index')->with('success', 'Estudiante dado de baja correctamente.');
    }

        public function darAlta(Estudiante $estudiante)
    {
        $estudiante->update(['activo' => true]);
        return redirect()->route('estudiantes.index')->with('success', 'Estudiante dado de alta correctamente.');
    }
}
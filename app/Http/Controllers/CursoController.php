<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\PlanPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CursoController extends Controller
{
    public function index(Request $request)
    {
        $query = Curso::query()->withCount(['estudiantes as inscripciones_count']);

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where('NombreCur', 'like', '%' . $q . '%')
                  ->orWhere('TipoCur', 'like', '%' . $q . '%')
                  ->orWhere('PeriodoCur', 'like', '%' . $q . '%');
        }

        $cursos = $query->orderBy('NombreCur')->paginate(10);

        return view('cursos.index', compact('cursos'));
    }

    public function create()
    {
        return view('cursos.create');
    }

    public function store(Request $request)
    {
        // Debug: Log the incoming request data
        \Log::info('Curso store method called', ['request_data' => $request->all()]);
        
        $validated = $request->validate([
            'NombreCur' => ['required', 'string', 'max:100'],
            'TipoCur' => ['required', 'in:Diplomado,Especialidad,Maestria,Doctorado'],
            'VersionCur' => ['required', 'integer', 'min:1'],
            'EdicionCur' => ['required', 'string', 'max:20'],
            'DuracionCur' => ['required', 'integer', 'min:1'],
            'CupoCur' => ['required', 'integer', 'min:1'],
            'PeriodoCur' => ['required', 'string', 'max:50'],
            'CostoCur' => ['required', 'numeric', 'min:0.01'],
            'DescripcionCur' => ['nullable', 'string', 'max:500'],
            'MontoMatriculaPP' => ['required', 'numeric', 'min:0.01'],
            'MontoCuotaPP' => ['required', 'numeric', 'min:0.01'],
            'NroCuotasPP' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        DB::beginTransaction();

        try {
            \Log::info('Attempting to create course', ['validated_data' => $validated]);
            
            $curso = Curso::create([
                'NombreCur' => $validated['NombreCur'],
                'TipoCur' => $validated['TipoCur'],
                'VersionCur' => $validated['VersionCur'],
                'EdicionCur' => $validated['EdicionCur'],
                'DuracionCur' => $validated['DuracionCur'],
                'CupoCur' => $validated['CupoCur'],
                'PeriodoCur' => $validated['PeriodoCur'],
                'CostoCur' => $validated['CostoCur'],
                'DescripcionCur' => $validated['DescripcionCur'] ?? null,
            ]);

            \Log::info('Course created successfully', ['course_id' => $curso->Id_Cur]);

            PlanPago::create([
                'Id_Cur' => $curso->Id_Cur,
                'MontoTotalPP' => $validated['CostoCur'],
                'MontoMatriculaPP' => $validated['MontoMatriculaPP'],
                'MontoCuotaPP' => $validated['MontoCuotaPP'],
                'NroCuotasPP' => $validated['NroCuotasPP'],
                'TotalCuotasPP' => $validated['NroCuotasPP'],
            ]);

            \Log::info('PlanPago created successfully');

            DB::commit();

            \Log::info('Redirecting to cursos.index');
            return redirect()->route('cursos.index')->with('success', 'Curso registrado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating course', ['error' => $e->getMessage()]);
            throw ValidationException::withMessages([
                'error' => ['No se pudo registrar el curso. Intente nuevamente.']
            ]);
        }
    }

    public function edit(Curso $curso)
    {
        $plan = PlanPago::where('Id_Cur', $curso->Id_Cur)->first();
        return view('cursos.edit', compact('curso', 'plan'));
    }

    public function update(Request $request, Curso $curso)
    {
        $validated = $request->validate([
            'NombreCur' => ['required', 'string', 'max:100'],
            'TipoCur' => ['required', 'in:Diplomado,Especialidad,Maestria,Doctorado'],
            'VersionCur' => ['required', 'integer', 'min:1'],
            'EdicionCur' => ['required', 'string', 'max:20'],
            'DuracionCur' => ['required', 'integer', 'min:1'],
            'CupoCur' => ['required', 'integer', 'min:1'],
            'PeriodoCur' => ['required', 'string', 'max:50'],
            'CostoCur' => ['required', 'numeric', 'min:0.01'],
            'DescripcionCur' => ['nullable', 'string', 'max:500'],
            'MontoMatriculaPP' => ['required', 'numeric', 'min:0.01'],
            'MontoCuotaPP' => ['required', 'numeric', 'min:0.01'],
            'NroCuotasPP' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        DB::beginTransaction();

        try {
            $curso->update([
                'NombreCur' => $validated['NombreCur'],
                'TipoCur' => $validated['TipoCur'],
                'VersionCur' => $validated['VersionCur'],
                'EdicionCur' => $validated['EdicionCur'],
                'DuracionCur' => $validated['DuracionCur'],
                'CupoCur' => $validated['CupoCur'],
                'PeriodoCur' => $validated['PeriodoCur'],
                'CostoCur' => $validated['CostoCur'],
                'DescripcionCur' => $validated['DescripcionCur'] ?? null,
            ]);

            PlanPago::where('Id_Cur', $curso->Id_Cur)->update([
                'MontoTotalPP' => $validated['CostoCur'],
                'MontoMatriculaPP' => $validated['MontoMatriculaPP'],
                'MontoCuotaPP' => $validated['MontoCuotaPP'],
                'NroCuotasPP' => $validated['NroCuotasPP'],
            ]);

            DB::commit();

            return redirect()->route('cursos.index')->with('success', 'Curso actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' => ['No se pudo actualizar el curso. Intente nuevamente.']
            ]);
        }
    }

    public function destroy(Curso $curso)
    {
        $tieneInscripciones = DB::table('Inscripcion')->where('Id_Cur', $curso->Id_Cur)->exists();

        if ($tieneInscripciones) {
            return redirect()->route('cursos.index')->with('error', 'No se puede eliminar el curso porque tiene inscripciones asociadas.');
        }

        DB::beginTransaction();

        try {
            PlanPago::where('Id_Cur', $curso->Id_Cur)->delete();
            $curso->delete();
            DB::commit();

            return redirect()->route('cursos.index')->with('success', 'Curso eliminado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cursos.index')->with('error', 'No se pudo eliminar el curso.');
        }
    }
}
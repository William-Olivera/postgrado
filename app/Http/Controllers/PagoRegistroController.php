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
use Illuminate\Validation\ValidationException;

class PagoRegistroController extends Controller
{
    public function cursos(): JsonResponse
    {
        $cursos = Curso::query()
            ->orderBy('NombreCur')
            ->get(['Id_Cur', 'NombreCur', 'TipoCur', 'VersionCur', 'EdicionCur']);

        return response()->json(['data' => $cursos]);
    }

    public function buscarEstudiantes(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'Id_Cur' => ['required', 'integer', 'exists:Curso,Id_Cur'],
            'campo' => ['required', Rule::in(['nombre', 'registro', 'cedula'])],
            'q' => ['required', 'string', 'min:1', 'max:120'],
        ]);

        $q = trim($validated['q']);
        $idCur = (int) $validated['Id_Cur'];

        $query = Estudiante::query()
            ->whereExists(function ($sub) use ($idCur) {
                $sub->select(DB::raw(1))
                    ->from('Inscripcion')
                    ->whereColumn('Inscripcion.Id_E', 'Estudiante.Id_E')
                    ->where('Inscripcion.Id_Cur', $idCur);
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

    public function buscarEstudiantesBase(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'campo' => ['required', Rule::in(['nombre', 'registro', 'cedula'])],
            'q' => ['required', 'string', 'min:1', 'max:120'],
        ]);

        $q = trim($validated['q']);

        $query = Estudiante::query();

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
            ->limit(30)
            ->get()
            ->map(fn (Estudiante $e) => [
                'Id_E' => $e->Id_E,
                'nombre_completo' => $e->nombreCompleto(),
                'RegistroE' => $e->RegistroE,
                'CedulaE' => $e->CedulaE,
            ]);

        return response()->json(['data' => $rows]);
    }

    public function storeInscripcion(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tipo_estudiante' => ['required', Rule::in(['nuevo', 'antiguo'])],
            'Id_Cur' => ['required', 'integer', 'exists:Curso,Id_Cur'],
            'Id_E' => ['nullable', 'integer', 'exists:Estudiante,Id_E'],
            'estudiante' => ['nullable', 'array'],
            'estudiante.nombreE' => ['required_if:tipo_estudiante,nuevo', 'string', 'max:50'],
            'estudiante.paternoE' => ['required_if:tipo_estudiante,nuevo', 'string', 'max:20'],
            'estudiante.maternoE' => ['nullable', 'string', 'max:20'],
            'estudiante.RegistroE' => ['required_if:tipo_estudiante,nuevo', 'integer'],
            'estudiante.CedulaE' => ['required_if:tipo_estudiante,nuevo', 'string', 'max:20'],
            'estudiante.TelefonoE' => ['required_if:tipo_estudiante,nuevo', 'string', 'max:20'],
            'estudiante.DescuentoE' => ['required_if:tipo_estudiante,nuevo', 'integer', 'min:0', 'max:100'],
        ]);

        $idE = null;

        if ($validated['tipo_estudiante'] === 'antiguo') {
            $idE = (int) ($validated['Id_E'] ?? 0);
            if ($idE <= 0) {
                throw ValidationException::withMessages([
                    'Id_E' => ['Seleccione un estudiante registrado.'],
                ]);
            }
        } else {
            $estudianteData = $validated['estudiante'];

            $yaExisteRegistro = Estudiante::query()
                ->where('RegistroE', $estudianteData['RegistroE'])
                ->exists();
            if ($yaExisteRegistro) {
                throw ValidationException::withMessages([
                    'estudiante.RegistroE' => ['Ya existe un estudiante con ese número de registro.'],
                ]);
            }

            $yaExisteCi = Estudiante::query()
                ->where('CedulaE', $estudianteData['CedulaE'])
                ->exists();
            if ($yaExisteCi) {
                throw ValidationException::withMessages([
                    'estudiante.CedulaE' => ['Ya existe un estudiante con esa cédula.'],
                ]);
            }

            $nuevo = Estudiante::query()->create([
                'nombreE' => trim($estudianteData['nombreE']),
                'paternoE' => trim($estudianteData['paternoE']),
                'maternoE' => isset($estudianteData['maternoE']) ? trim((string) $estudianteData['maternoE']) : null,
                'RegistroE' => (int) $estudianteData['RegistroE'],
                'CedulaE' => trim($estudianteData['CedulaE']),
                'TelefonoE' => trim($estudianteData['TelefonoE']),
                'DescuentoE' => (int) $estudianteData['DescuentoE'],
            ]);
            $idE = (int) $nuevo->Id_E;
        }

        $inscrito = DB::table('Inscripcion')
            ->where('Id_E', $idE)
            ->where('Id_Cur', $validated['Id_Cur'])
            ->exists();
        if ($inscrito) {
            throw ValidationException::withMessages([
                'Id_Cur' => ['El estudiante ya está inscrito en el curso seleccionado.'],
            ]);
        }

        DB::table('Inscripcion')->insert([
            'Id_E' => $idE,
            'Id_Cur' => (int) $validated['Id_Cur'],
            'FechaIns' => now()->toDateString(),
            'EstadoIns' => 'Impaga',
        ]);

        return response()->json([
            'message' => 'Inscripción registrada correctamente.',
            'data' => [
                'Id_E' => $idE,
                'Id_Cur' => (int) $validated['Id_Cur'],
            ],
        ], 201);
    }

    public function store(Request $request): JsonResponse
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
                'Id_E' => ['El estudiante no está inscrito en el curso seleccionado.'],
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
                    'NroP' => ['Para cuota, el número debe estar entre 1 y 5.'],
                ]);
            }
        } elseif ($validated['NroP'] !== $nroEsperado) {
            throw ValidationException::withMessages([
                'NroP' => ['El número de pago no coincide con el tipo seleccionado.'],
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
                'TipoP' => ['Ya existe un pago registrado con el mismo tipo y número para este estudiante y curso.'],
            ]);
        }

        $pago = Pago::query()->create([
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

        return response()->json([
            'message' => 'Pago registrado correctamente.',
            'data' => ['Id_P' => $pago->Id_P],
        ], 201);
    }
}

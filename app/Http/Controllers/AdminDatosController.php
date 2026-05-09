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

class AdminDatosController extends Controller
{
    public function storeCurso(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'NombreCur' => ['required', 'string', 'max:100'],
            'TipoCur' => ['required', Rule::in(['Maestria', 'Diplomado'])],
            'EdicionCur' => ['required', 'integer', 'min:1'],
            'VersionCur' => ['required', 'integer', 'min:1'],
        ]);

        $curso = Curso::query()->create([
            'NombreCur' => trim($validated['NombreCur']),
            'TipoCur' => $validated['TipoCur'],
            'EdicionCur' => (int) $validated['EdicionCur'],
            'VersionCur' => (int) $validated['VersionCur'],
        ]);

        return response()->json([
            'message' => 'Curso creado correctamente.',
            'data' => [
                'Id_Cur' => $curso->Id_Cur,
                'NombreCur' => $curso->NombreCur,
                'TipoCur' => $curso->TipoCur,
                'VersionCur' => $curso->VersionCur,
                'EdicionCur' => $curso->EdicionCur,
            ],
        ], 201);
    }

    public function updateCurso(Request $request, int $id): JsonResponse
    {
        $curso = Curso::query()->find($id);
        if (! $curso) {
            return response()->json(['message' => 'Curso no encontrado.'], 404);
        }

        $validated = $request->validate([
            'NombreCur' => ['required', 'string', 'max:100'],
            'TipoCur' => ['required', Rule::in(['Maestria', 'Diplomado'])],
            'EdicionCur' => ['required', 'integer', 'min:1'],
            'VersionCur' => ['required', 'integer', 'min:1'],
        ]);

        $curso->update([
            'NombreCur' => trim($validated['NombreCur']),
            'TipoCur' => $validated['TipoCur'],
            'EdicionCur' => (int) $validated['EdicionCur'],
            'VersionCur' => (int) $validated['VersionCur'],
        ]);

        return response()->json([
            'message' => 'Curso actualizado correctamente.',
            'data' => [
                'Id_Cur' => $curso->Id_Cur,
                'NombreCur' => $curso->NombreCur,
                'TipoCur' => $curso->TipoCur,
                'VersionCur' => $curso->VersionCur,
                'EdicionCur' => $curso->EdicionCur,
            ],
        ]);
    }

    public function showEstudiante(int $id): JsonResponse
    {
        $e = Estudiante::query()->find($id);
        if (! $e) {
            return response()->json(['message' => 'Estudiante no encontrado.'], 404);
        }

        return response()->json([
            'data' => [
                'Id_E' => $e->Id_E,
                'nombreE' => $e->nombreE,
                'paternoE' => $e->paternoE,
                'maternoE' => $e->maternoE,
                'RegistroE' => $e->RegistroE,
                'CedulaE' => $e->CedulaE,
                'TelefonoE' => $e->TelefonoE,
                'DescuentoE' => $e->DescuentoE,
            ],
        ]);
    }

    public function updateEstudiante(Request $request, int $id): JsonResponse
    {
        $e = Estudiante::query()->find($id);
        if (! $e) {
            return response()->json(['message' => 'Estudiante no encontrado.'], 404);
        }

        $validated = $request->validate([
            'nombreE' => ['required', 'string', 'max:50'],
            'paternoE' => ['required', 'string', 'max:20'],
            'maternoE' => ['nullable', 'string', 'max:20'],
            'RegistroE' => ['required', 'integer'],
            'CedulaE' => ['required', 'string', 'max:20'],
            'TelefonoE' => ['required', 'string', 'max:20'],
            'DescuentoE' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $reg = (int) $validated['RegistroE'];
        if (Estudiante::query()->where('RegistroE', $reg)->where('Id_E', '!=', $id)->exists()) {
            throw ValidationException::withMessages([
                'RegistroE' => ['Ya existe otro estudiante con ese número de registro.'],
            ]);
        }

        $ci = trim($validated['CedulaE']);
        if (Estudiante::query()->where('CedulaE', $ci)->where('Id_E', '!=', $id)->exists()) {
            throw ValidationException::withMessages([
                'CedulaE' => ['Ya existe otro estudiante con esa cédula.'],
            ]);
        }

        $e->update([
            'nombreE' => trim($validated['nombreE']),
            'paternoE' => trim($validated['paternoE']),
            'maternoE' => isset($validated['maternoE']) ? trim((string) $validated['maternoE']) : null,
            'RegistroE' => $reg,
            'CedulaE' => $ci,
            'TelefonoE' => trim($validated['TelefonoE']),
            'DescuentoE' => (int) $validated['DescuentoE'],
        ]);

        return response()->json(['message' => 'Estudiante actualizado correctamente.']);
    }

    public function pagosEstudiante(int $id): JsonResponse
    {
        if (! Estudiante::query()->where('Id_E', $id)->exists()) {
            return response()->json(['message' => 'Estudiante no encontrado.'], 404);
        }

        $rows = Pago::query()
            ->where('Id_E', $id)
            ->with('curso')
            ->orderByDesc('FechaP')
            ->orderByDesc('Id_P')
            ->get()
            ->map(fn (Pago $p) => [
                'Id_P' => $p->Id_P,
                'Id_Cur' => $p->Id_Cur,
                'curso_etiqueta' => $p->curso
                    ? ($p->curso->NombreCur.' — '.$p->curso->TipoCur.' v'.$p->curso->VersionCur.' (Ed. '.$p->curso->EdicionCur.')')
                    : '',
                'MontoP' => (float) $p->MontoP,
                'TipoP' => $p->TipoP,
                'NroP' => $p->NroP,
                'FechaP' => $p->FechaP,
                'NroCompP' => $p->NroCompP,
                'CuentaTransfP' => $p->CuentaTransfP,
            ]);

        return response()->json(['data' => $rows]);
    }

    public function updatePago(Request $request, int $id): JsonResponse
    {
        $pago = Pago::query()->find($id);
        if (! $pago) {
            return response()->json(['message' => 'Pago no encontrado.'], 404);
        }

        $validated = $request->validate([
            'MontoP' => ['required', 'numeric', 'min:0.01'],
            'TipoP' => ['required', Rule::in(['Matricula', 'Cuota', 'Defensa'])],
            'NroP' => ['required', 'integer', 'min:0', 'max:6'],
            'FechaP' => ['required', 'date'],
            'NroCompP' => ['required', 'integer', 'min:1'],
            'CuentaTransfP' => ['required', 'string', 'max:50'],
        ]);

        $idE = (int) $pago->Id_E;
        $idCur = (int) $pago->Id_Cur;

        $inscrito = DB::table('Inscripcion')
            ->where('Id_E', $idE)
            ->where('Id_Cur', $idCur)
            ->exists();

        if (! $inscrito) {
            throw ValidationException::withMessages([
                'Id_P' => ['El estudiante no está inscrito en el curso de este pago.'],
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

        $plan = PlanPago::query()->where('Id_Cur', $idCur)->first();
        if (! $plan) {
            throw ValidationException::withMessages([
                'Id_Cur' => ['No existe un plan de pago para el curso de este pago.'],
            ]);
        }

        $duplicado = Pago::query()
            ->where('Id_E', $idE)
            ->where('Id_Cur', $idCur)
            ->where('TipoP', $validated['TipoP'])
            ->where('NroP', $validated['NroP'])
            ->where('Id_P', '!=', $id)
            ->exists();

        if ($duplicado) {
            throw ValidationException::withMessages([
                'TipoP' => ['Ya existe otro pago con el mismo tipo y número para este estudiante y curso.'],
            ]);
        }

        $pago->update([
            'MontoP' => $validated['MontoP'],
            'TipoP' => $validated['TipoP'],
            'NroP' => $validated['NroP'],
            'FechaP' => $validated['FechaP'],
            'NroCompP' => $validated['NroCompP'],
            'CuentaTransfP' => $validated['CuentaTransfP'],
        ]);

        return response()->json(['message' => 'Pago actualizado correctamente.']);
    }
}

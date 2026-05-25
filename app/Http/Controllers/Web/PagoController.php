<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pago\StoreRequest;
use App\Models\DetallePlanPago;
use App\Models\Estudiante;
use App\Models\Inscripcion;
use App\Models\Pago;
use App\Services\PagoService;
use App\Services\PdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PagoController extends Controller
{
    public function __construct(
        private PagoService $pagoService,
        private PdfService $pdfService
    ) {}

    public function index(): View
    {
        return view('pagos.index');
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        try {
            $pago = $this->pagoService->registrar($request->validated());
            // Se comenta la generación automática de PDF por ser opcional
            // $this->pdfService->generarRecibo($pago);

            return redirect()
                ->route('pagos.index')
                ->with('success', 'Pago registrado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(int $id): View
    {
        $pago = Pago::with([
            'detallePlanPago.planPago.inscripcion.estudiante',
            'detallePlanPago.planPago.inscripcion.curso',
            'registradoPor',
        ])->findOrFail($id);

        return view('pagos.show', compact('pago'));
    }

    public function subirArchivo(Request $request, Pago $pago): JsonResponse
    {
        try {
            $validated = $request->validate([
                'archivo_adjunto' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ]);

            if ($request->hasFile('archivo_adjunto')) {
                // Eliminar archivo anterior si existe
                if ($pago->archivo_adjunto) {
                    Storage::disk('public')->delete($pago->archivo_adjunto);
                }

                // Guardar nuevo archivo
                $path = $request->file('archivo_adjunto')->store('pagos', 'public');
                $pago->archivo_adjunto = $path;
                $pago->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Archivo adjuntado correctamente.',
                    'archivo' => $path
                ]);
            }

            return response()->json(['success' => false, 'message' => 'No se recibió ningún archivo'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function buscarEstudiante(Request $request): JsonResponse
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
        ->whereHas('inscripciones.planPago.detalles', fn ($q) => $q->where('saldo_cuota', '>', 0))
        ->limit(10)
        ->get(['id', 'nombres', 'paterno', 'materno', 'cedula', 'registro', 'descuento_porcentaje']);

        return response()->json($estudiantes);
    }

    public function cuotasPendientes(Estudiante $estudiante): JsonResponse
    {
        $inscripciones = Inscripcion::with([
            'curso',
            'planPago.detalles' => fn ($q) => $q->orderBy('nro_cuota'),
        ])
        ->where('estudiante_id', $estudiante->id)
        ->get();

        $cuotas = [];
        foreach ($inscripciones as $inscripcion) {
            if (!$inscripcion->planPago) {
                continue;
            }
            foreach ($inscripcion->planPago->detalles as $detalle) {
                $cuotas[] = [
                    'id' => $detalle->id,
                    'concepto' => $detalle->concepto,
                    'fase' => $detalle->fase,
                    'nro_modulo' => $detalle->nro_modulo,
                    'saldo' => (float) $detalle->saldo_cuota,
                    'monto_programado' => (float) $detalle->monto_programado,
                    'estado' => $detalle->estado,
                    'curso' => $inscripcion->curso->nombre,
                    'inscripcion_id' => $inscripcion->id,
                ];
            }
        }

        return response()->json([
            'estudiante' => [
                'id' => $estudiante->id,
                'nombre_completo' => $estudiante->nombre_completo,
                'cedula' => $estudiante->cedula,
                'registro' => $estudiante->registro,
                'descuento_porcentaje' => $estudiante->descuento_porcentaje,
            ],
            'cuotas' => $cuotas,
            'inscripciones' => $inscripciones->map(fn ($i) => [
                'inscripcion_id' => $i->id,
                'curso' => $i->curso->nombre,
                'tipo' => $i->tipo_inscripcion->value,
                'periodo' => $i->curso->periodo,
            ]),
        ]);
    }

    public function eliminarArchivo(Pago $pago): JsonResponse
    {
        try {
            if (!$pago->archivo_adjunto) {
                return response()->json(['success' => false, 'message' => 'No hay archivo adjunto'], 404);
            }

            // Eliminar el archivo del storage
            Storage::disk('public')->delete($pago->archivo_adjunto);

            // Actualizar el registro
            $pago->archivo_adjunto = null;
            $pago->save();

            return response()->json(['success' => true, 'message' => 'Archivo eliminado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

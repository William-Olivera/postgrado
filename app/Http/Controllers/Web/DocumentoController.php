<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Documento;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DocumentoController extends Controller
{
    /**
     * Muestra la carpeta digital de un estudiante específico.
     */
    public function show(string $id): View
    {
        // Busca al estudiante o lanza un error 404
        $estudiante = Estudiante::findOrFail($id);

        // Recupera solo los documentos del estudiante seleccionado
        $documentos = Documento::where('estudiante_id', $id)->get();

        return view('documentos.index', compact('estudiante', 'documentos'));
    }

    /**
     * Procesa la subida o la actualización (reemplazo) de un archivo.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'estudiante_id' => 'required|exists:estudiantes,id',
            'tipo' => 'required|in:CI,Titulo,Diploma,Certificado,Otro',
            'archivo' => 'required|file|mimes:pdf,jpg,png|max:5120', // Máx 5MB
        ]);

        $estudiante = Estudiante::findOrFail($data['estudiante_id']);
        $archivo = $request->file('archivo');

        // Busca si el requisito ya existía previamente para reemplazarlo
        $documentoExistente = Documento::where('estudiante_id', $estudiante->id)
            ->where('tipo', $data['tipo'])
            ->first();

        if ($documentoExistente) {
            // Borra el archivo físico anterior del disco
            Storage::disk('public')->delete($documentoExistente->ruta_archivo);
        }

        // Estructura de almacenamiento: storage/app/public/documentos/{estudiante_id}/
        $ruta = $archivo->store('documentos/' . $estudiante->id, 'public');

        if (!$ruta) {
            return back()->with('error', 'Error al guardar el archivo en el servidor.');
        }

        // Crea el nuevo registro o sobrescribe el anterior
        Documento::updateOrCreate(
            [
                'estudiante_id' => $estudiante->id,
                'tipo' => $data['tipo']
            ],
            [
                'nombre_archivo' => $archivo->getClientOriginalName(),
                'ruta_archivo' => $ruta,
                'tamanio_kb' => $archivo->getSize() / 1024,
                'extension' => $archivo->getClientOriginalExtension(),
                'subido_por' => auth()->id(), // Vinculación automática con el usuario activo
            ]
        );

        return redirect()->route('documentos.show', $estudiante->id)
            ->with('success', 'Documento guardado correctamente en el expediente.');
    }

    /**
     * Descarga segura de un archivo original.
     */
    public function download(Documento $documento)
    {
        if (!Storage::disk('public')->exists($documento->ruta_archivo)) {
            return back()->with('error', 'El archivo no se encuentra físicamente en el servidor.');
        }
        return Storage::disk('public')->download($documento->ruta_archivo, $documento->nombre_archivo);
    }

    /**
     * Elimina un archivo y su registro del expediente.
     */
    public function destroy(Documento $documento): RedirectResponse
    {
        $estudianteId = $documento->estudiante_id;

        if (Storage::disk('public')->exists($documento->ruta_archivo)) {
            Storage::disk('public')->delete($documento->ruta_archivo);
        }

        $documento->delete();

        return redirect()->route('documentos.show', $estudianteId)
            ->with('success', 'Documento eliminado del expediente.');
    }
}

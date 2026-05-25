<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use ZipArchive;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RespaldoController extends Controller
{
    public function index(): View
    {
        $backups = [];
        $backupPath = storage_path('app/backups');

        if (File::exists($backupPath)) {
            $files = File::files($backupPath);

            foreach ($files as $file) {
                if ($file->getExtension() === 'zip') {
                    $backups[] = [
                        'archivo' => $file->getFilename(),
                        'tamano' => $this->formatSize($file->getSize()),
                        'fecha' => date('d/m/Y H:i', $file->getMTime()),
                    ];
                }
            }

            // Ordenar por fecha de modificación (más reciente primero)
            usort($backups, function($a, $b) {
                return strtotime($b['fecha']) - strtotime($a['fecha']);
            });
        }

        return view('respaldos.index', compact('backups'));
    }

    public function crear(Request $request)
    {
        try {
            $backupPath = storage_path('app/backups');

            // Crear directorio si no existe
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            // Nombre del archivo de respaldo
            $fecha = now()->format('Y-m-d_H-i-s');
            $zipFileName = "backup_postgrado_{$fecha}.zip";
            $zipFilePath = $backupPath . '/' . $zipFileName;

            // Crear archivo ZIP
            $zip = new ZipArchive();
            if ($zip->open($zipFilePath, ZipArchive::CREATE) === true) {
                // Exportar base de datos MySQL usando mysqldump
                $mysqlConfig = config('database.connections.mysql');
                $sqlFileName = "backup_mysql_{$fecha}.sql";
                $sqlFilePath = $backupPath . '/' . $sqlFileName;

                $command = sprintf(
                    'mysqldump -h%s -P%s -u%s -p%s %s > %s',
                    $mysqlConfig['host'],
                    $mysqlConfig['port'],
                    $mysqlConfig['username'],
                    $mysqlConfig['password'],
                    $mysqlConfig['database'],
                    $sqlFilePath
                );

                exec($command, $output, $returnVar);

                if (File::exists($sqlFilePath)) {
                    $zip->addFile($sqlFilePath, $sqlFileName);
                    // Eliminar archivo temporal SQL después de agregarlo al ZIP
                    File::delete($sqlFilePath);
                }

                // Agregar carpeta de storage/app/public (documentos de estudiantes)
                $storagePublicPath = storage_path('app/public');
                if (File::exists($storagePublicPath)) {
                    $files = File::allFiles($storagePublicPath);
                    foreach ($files as $file) {
                        $relativePath = str_replace($storagePublicPath . '/', '', $file->getPathname());
                        $zip->addFile($file->getPathname(), 'storage_public/' . $relativePath);
                    }
                }

                $zip->close();
            }

            return redirect()->route('respaldos.index')
                ->with('success', 'Respaldo creado exitosamente: ' . $zipFileName);

        } catch (\Exception $e) {
            return redirect()->route('respaldos.index')
                ->with('error', 'Error al crear el respaldo: ' . $e->getMessage());
        }
    }

    public function descargar($archivo)
    {
        $backupPath = storage_path('app/backups');
        $filePath = $backupPath . '/' . $archivo;

        if (!File::exists($filePath)) {
            abort(404, 'Archivo de respaldo no encontrado');
        }

        return response()->download($filePath);
    }

    public function eliminar(Request $request)
    {
        $archivo = $request->input('archivo');
        $backupPath = storage_path('app/backups');
        $filePath = $backupPath . '/' . $archivo;

        if (File::exists($filePath)) {
            File::delete($filePath);
            return redirect()->route('respaldos.index')
                ->with('success', 'Respaldo eliminado exitosamente');
        }

        return redirect()->route('respaldos.index')
            ->with('error', 'Archivo de respaldo no encontrado');
    }

    private function formatSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 0) {
            return $bytes . ' bytes';
        }
        return '0 bytes';
    }
}

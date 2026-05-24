<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use App\Models\Pago;
use App\Models\Inscripcion;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $estadisticas = [
            'total_estudiantes' => Estudiante::count(),
            'total_inscripciones' => Inscripcion::where('estado_academico', 'Activo')->count(),
            'pagos_hoy' => Pago::whereDate('fecha_pago', today())->sum('monto'),
            'saldo_pendiente_total' => \App\Models\PlanPago::sum('saldo_pendiente'),
        ];
        return view('dashboard.index', compact('estadisticas'));
    }
}

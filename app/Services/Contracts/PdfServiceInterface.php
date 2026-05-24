<?php
namespace App\Services\Contracts;

use App\Models\Pago;

interface PdfServiceInterface
{
    public function generarRecibo(Pago $pago): string;
    public function generarReportePagos(array $filtros): string;
}

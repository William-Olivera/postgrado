<?php
namespace App\Services\Contracts;

use App\Models\Pago;

interface PagoServiceInterface
{
    public function registrar(array $data): Pago;
}

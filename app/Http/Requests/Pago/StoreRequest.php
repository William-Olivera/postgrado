<?php
namespace App\Http\Requests\Pago;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'detalle_plan_pago_id' => 'required|exists:detalle_plan_pagos,id',
            'fecha_pago'           => 'required|date',
            'monto'                => 'required|numeric|min:0.01',
            'nro_comprobante'      => 'required|string|max:50',
            'observacion'          => 'nullable|string',
        ];
    }
}

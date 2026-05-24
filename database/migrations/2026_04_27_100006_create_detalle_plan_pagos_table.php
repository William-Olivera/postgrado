<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_plan_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_pago_id')->constrained('plan_pagos')->cascadeOnDelete();
            $table->integer('nro_cuota');
            $table->integer('nro_modulo')->nullable();
            $table->string('concepto', 50);
            $table->string('fase', 20)->nullable();
            $table->decimal('monto_programado', 10, 2);
            $table->decimal('monto_pagado', 10, 2)->default(0);
            $table->decimal('monto_descuento', 10, 2)->default(0);
            $table->decimal('saldo_cuota', 10, 2)->default(0);
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('estado', ['Pendiente', 'Pagado', 'Parcial', 'Vencido', 'Condonado'])->default('Pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_plan_pagos');
    }
};
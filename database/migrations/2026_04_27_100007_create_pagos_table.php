<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detalle_plan_pago_id')->constrained('detalle_plan_pagos');
            $table->foreignId('inscripcion_id')->constrained('inscripciones');
            $table->date('fecha_pago');
            $table->decimal('monto', 10, 2);
            $table->string('nro_comprobante', 50);
            $table->text('observacion')->nullable();
            $table->string('archivo_adjunto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};

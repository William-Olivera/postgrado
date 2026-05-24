<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();
            $table->foreignId('curso_id')->constrained('cursos')->cascadeOnDelete();
            
            // Tipo de inscripción (hasta qué fase llega)
            $table->enum('tipo_inscripcion', ['Diplomado', 'Especialidad', 'Maestría'])->default('Maestría');
            
            $table->date('fecha_inscripcion');
            
            // ESTADO ACADÉMICO: situación del estudiante en el curso
            $table->enum('estado_academico', [
                'Pendiente',   // Inscrito pero sin pagar matrícula
                'Activo',      // Pagó matrícula, cursando
                'Congelado',   // Pausa temporal solicitada
                'Egresado',    // Completó todos los módulos
                'Retirado'     // Abandonó definitivamente
            ])->default('Pendiente');
            
            // ESTADO FINANCIERO: situación de sus pagos
            $table->enum('estado_financiero', [
                'Sin Pagar',   // No ha realizado ningún pago
                'Parcial',     // Pagó algo pero debe saldo
                'Al Día',      // Saldo pendiente = 0 (pero aún tiene cuotas futuras)
                'Completado'   // Pagó TODO el plan completo (saldo total = 0)
            ])->default('Sin Pagar');
            
            $table->enum('modalidad_pago', ['Contado', 'Cuotas'])->default('Cuotas');
            $table->string('observacion', 255)->nullable();
            $table->timestamps();
            
            $table->unique(['estudiante_id', 'curso_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripciones');
    }
};
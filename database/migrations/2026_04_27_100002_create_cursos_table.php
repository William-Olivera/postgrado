<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->enum('tipo', ['Diplomado', 'Especialidad', 'Maestría']);
            $table->integer('version');
            $table->integer('edicion');
            $table->string('periodo', 20);

            // Costos del curso (todos obligatorios, el cliente ingresa cada uno)
            $table->decimal('costo_matricula', 10, 2);
            $table->decimal('costo_total_estudio', 10, 2);
            $table->decimal('costo_defensa_diplomado', 10, 2);
            $table->decimal('costo_defensa_especialidad', 10, 2);
            $table->decimal('costo_defensa_maestria', 10, 2);

            // Módulos configurables por curso (el cliente elige cuántos para cada fase)
            $table->integer('nro_modulos_diplomado');
            $table->integer('nro_modulos_especialidad')->nullable();
            $table->integer('nro_modulos_maestria')->nullable();

            $table->integer('cupo');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cursos');
    }
};
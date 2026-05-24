<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->string('nombres', 50);
            $table->string('paterno', 20);
            $table->string('materno', 20)->nullable();
            $table->string('registro', 20)->unique();
            $table->string('cedula', 20)->unique();
            $table->string('celular', 20)->nullable();
            $table->string('observaciones', 255)->nullable();
            $table->integer('descuento_porcentaje')->default(0);
            $table->boolean('activo')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
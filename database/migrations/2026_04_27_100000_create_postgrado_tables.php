<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Estudiante', function (Blueprint $table) {
            $table->increments('Id_E');
            $table->string('nombreE', 50);
            $table->string('paternoE', 20);
            $table->string('maternoE', 20)->nullable();
            $table->integer('RegistroE');
            $table->string('CedulaE', 20);
            $table->string('TelefonoE', 20);
            $table->integer('DescuentoE');
            $table->string('ObservacionE', 50)->nullable();
        });

        Schema::create('Curso', function (Blueprint $table) {
            $table->increments('Id_Cur');
            $table->string('NombreCur', 100);
            $table->string('TipoCur', 20);
            $table->integer('VersionCur');
            $table->integer('EdicionCur');
        });

        Schema::create('Inscripcion', function (Blueprint $table) {
            $table->unsignedInteger('Id_E');
            $table->unsignedInteger('Id_Cur');
            $table->date('FechaIns');
            $table->string('EstadoIns', 20)->nullable();
            $table->primary(['Id_E', 'Id_Cur']);
            $table->foreign('Id_E')->references('Id_E')->on('Estudiante')->cascadeOnDelete();
            $table->foreign('Id_Cur')->references('Id_Cur')->on('Curso')->cascadeOnDelete();
        });

        Schema::create('PlanPago', function (Blueprint $table) {
            $table->increments('Id_PP');
            $table->decimal('MontoTotalPP', 10, 2);
            $table->integer('TotalCuotasPP');
            $table->unsignedInteger('Id_Cur')->nullable();
            $table->foreign('Id_Cur')->references('Id_Cur')->on('Curso')->nullOnDelete();
        });

        Schema::create('Pago', function (Blueprint $table) {
            $table->increments('Id_P');
            $table->unsignedInteger('Id_PP');
            $table->unsignedInteger('Id_E');
            $table->unsignedInteger('Id_Cur');
            $table->date('FechaP');
            $table->decimal('MontoP', 10, 2);
            $table->integer('NroP');
            $table->string('TipoP', 20);
            $table->bigInteger('NroCompP');
            $table->string('CuentaTransfP', 50);
            $table->foreign('Id_PP')->references('Id_PP')->on('PlanPago')->cascadeOnDelete();
            $table->foreign('Id_E')->references('Id_E')->on('Estudiante')->cascadeOnDelete();
            $table->foreign('Id_Cur')->references('Id_Cur')->on('Curso')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Pago');
        Schema::dropIfExists('PlanPago');
        Schema::dropIfExists('Inscripcion');
        Schema::dropIfExists('Curso');
        Schema::dropIfExists('Estudiante');
    }
};

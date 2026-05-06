<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('PlanPago', function (Blueprint $table) {
            $table->decimal('MontoMatriculaPP', 10, 2)->after('MontoTotalPP');
            $table->decimal('MontoCuotaPP', 10, 2)->after('MontoMatriculaPP');
            $table->integer('NroCuotasPP')->after('MontoCuotaPP');
        });
    }

    public function down(): void
    {
        Schema::table('PlanPago', function (Blueprint $table) {
            $table->dropColumn(['MontoMatriculaPP', 'MontoCuotaPP', 'NroCuotasPP']);
        });
    }
};

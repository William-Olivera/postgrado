<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('Curso', function (Blueprint $table) {
            if (!Schema::hasColumn('Curso', 'DuracionCur')) {
                $table->integer('DuracionCur')->default(0)->after('EdicionCur');
            }
            if (!Schema::hasColumn('Curso', 'CupoCur')) {
                $table->integer('CupoCur')->default(0)->after('DuracionCur');
            }
            if (!Schema::hasColumn('Curso', 'PeriodoCur')) {
                $table->string('PeriodoCur', 50)->nullable()->after('CupoCur');
            }
            if (!Schema::hasColumn('Curso', 'CostoCur')) {
                $table->decimal('CostoCur', 10, 2)->default(0)->after('PeriodoCur');
            }
            if (!Schema::hasColumn('Curso', 'DescripcionCur')) {
                $table->string('DescripcionCur', 500)->nullable()->after('CostoCur');
            }
        });
    }

    public function down(): void
    {
        Schema::table('Curso', function (Blueprint $table) {
            if (Schema::hasColumn('Curso', 'DescripcionCur')) {
                $table->dropColumn('DescripcionCur');
            }
            if (Schema::hasColumn('Curso', 'CostoCur')) {
                $table->dropColumn('CostoCur');
            }
            if (Schema::hasColumn('Curso', 'PeriodoCur')) {
                $table->dropColumn('PeriodoCur');
            }
            if (Schema::hasColumn('Curso', 'CupoCur')) {
                $table->dropColumn('CupoCur');
            }
            if (Schema::hasColumn('Curso', 'DuracionCur')) {
                $table->dropColumn('DuracionCur');
            }
        });
    }
};

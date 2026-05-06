<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('Estudiante', function (Blueprint $table) {
            if (!Schema::hasColumn('Estudiante', 'DireccionE')) {
                $table->string('DireccionE', 100)->nullable()->after('TelefonoE');
            }
            if (!Schema::hasColumn('Estudiante', 'ActivoE')) {
                $table->boolean('ActivoE')->default(true)->after('DireccionE');
            }
        });
    }

    public function down(): void
    {
        Schema::table('Estudiante', function (Blueprint $table) {
            if (Schema::hasColumn('Estudiante', 'ActivoE')) {
                $table->dropColumn('ActivoE');
            }
            if (Schema::hasColumn('Estudiante', 'DireccionE')) {
                $table->dropColumn('DireccionE');
            }
        });
    }
};

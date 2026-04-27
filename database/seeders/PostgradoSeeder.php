<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostgradoSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('Estudiante')->exists()) {
            return;
        }

        DB::table('Estudiante')->insert([
            ['nombreE' => 'William Ezequiel', 'paternoE' => 'Olivera', 'maternoE' => null, 'RegistroE' => 219091846, 'CedulaE' => '17749814', 'TelefonoE' => '72118349', 'DescuentoE' => 50, 'ObservacionE' => null],
            ['nombreE' => 'Carlos Manuel', 'paternoE' => 'Ferrel', 'maternoE' => 'Escobar', 'RegistroE' => 220045362, 'CedulaE' => '15796548', 'TelefonoE' => '69058761', 'DescuentoE' => 50, 'ObservacionE' => null],
            ['nombreE' => 'Carlos Daniel', 'paternoE' => 'Cortez', 'maternoE' => 'Barco', 'RegistroE' => 221245781, 'CedulaE' => '18597584', 'TelefonoE' => '71085649', 'DescuentoE' => 50, 'ObservacionE' => null],
            ['nombreE' => 'Rodrigo', 'paternoE' => 'Parada', 'maternoE' => 'Bazan', 'RegistroE' => 221292346, 'CedulaE' => '15187469', 'TelefonoE' => '68830260', 'DescuentoE' => 0, 'ObservacionE' => null],
        ]);

        DB::table('Curso')->insert([
            ['NombreCur' => 'Maestria en Educacion Superior', 'TipoCur' => 'Maestria', 'VersionCur' => 4, 'EdicionCur' => 2],
        ]);

        $fechaIns = '2026-04-21';
        foreach ([1, 2, 3, 4] as $idE) {
            DB::table('Inscripcion')->insert([
                'Id_E' => $idE,
                'Id_Cur' => 1,
                'FechaIns' => $fechaIns,
                'EstadoIns' => 'Completada',
            ]);
        }

        DB::table('PlanPago')->insert([
            'MontoTotalPP' => 9820.00,
            'TotalCuotasPP' => 7,
            'Id_Cur' => 1,
        ]);

        $compsMat = [21649751, 24631592, 20548921, 25862489];
        $compsCuota = [21649752, 24631593, 20548922, 25862490];
        $cuentas = ['29873549', '23651821', '24060081', '26125975'];
        $pagos = [];
        foreach ([1, 2, 3, 4] as $i => $idE) {
            $pagos[] = [
                'Id_PP' => 1, 'Id_E' => $idE, 'Id_Cur' => 1, 'FechaP' => $fechaIns,
                'MontoP' => 500.00, 'NroP' => 0, 'TipoP' => 'Matricula',
                'NroCompP' => $compsMat[$i], 'CuentaTransfP' => $cuentas[$i],
            ];
        }
        foreach ([1, 2, 3, 4] as $i => $idE) {
            $pagos[] = [
                'Id_PP' => 1, 'Id_E' => $idE, 'Id_Cur' => 1, 'FechaP' => $fechaIns,
                'MontoP' => 500.00, 'NroP' => 1, 'TipoP' => 'Cuota',
                'NroCompP' => $compsCuota[$i], 'CuentaTransfP' => $cuentas[$i],
            ];
        }

        DB::table('Pago')->insert($pagos);
    }
}

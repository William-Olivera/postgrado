<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Curso;
use App\Models\PlanPago;

class TestDatabase extends Command
{
    protected $signature = 'test:database';
    protected $description = 'Test database connection and table operations';

    public function handle()
    {
        try {
            $this->info('Testing database connection...');
            
            // Test creating a course
            $curso = Curso::create([
                'NombreCur' => 'Test Course',
                'TipoCur' => 'Diplomado',
                'VersionCur' => 1,
                'EdicionCur' => 1,
                'DuracionCur' => 10,
                'CupoCur' => 20,
                'PeriodoCur' => 'Test Period',
                'CostoCur' => 100.50,
                'DescripcionCur' => 'Test Description'
            ]);
            
            $this->info('Course created successfully! ID: ' . $curso->Id_Cur);
            
            // Test creating a payment plan
            $plan = PlanPago::create([
                'Id_Cur' => $curso->Id_Cur,
                'MontoTotalPP' => 100.50,
                'MontoMatriculaPP' => 50.00,
                'MontoCuotaPP' => 25.25,
                'NroCuotasPP' => 2,
                'TotalCuotasPP' => 2,
            ]);
            
            $this->info('Payment plan created successfully! ID: ' . $plan->Id_PP);
            
            // Clean up
            $plan->delete();
            $curso->delete();
            
            $this->info('Test completed successfully! Database is working correctly.');
            
        } catch (\Exception $e) {
            $this->error('Database test failed: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}

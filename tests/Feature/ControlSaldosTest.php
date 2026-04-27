<?php

namespace Tests\Feature;

use Database\Seeders\PostgradoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ControlSaldosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PostgradoSeeder::class);
    }

    public function test_saldo_estudiante_coincide_con_datos_semilla(): void
    {
        $this->getJson('/api/saldos/estudiante/1')
            ->assertOk()
            ->assertJsonPath('data.monto_pagado', 1000)
            ->assertJsonPath('data.monto_total', 9820)
            ->assertJsonPath('data.monto_mora', 8820);
    }

    public function test_saldos_por_curso(): void
    {
        $this->getJson('/api/saldos/cursos')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.monto_pagado', 4000)
            ->assertJsonPath('data.0.monto_total', 39280)
            ->assertJsonPath('data.0.monto_mora', 35280)
            ->assertJsonPath('data.0.cantidad_estudiantes', 4);
    }

    public function test_saldos_gestion_2026(): void
    {
        $this->getJson('/api/saldos/gestion?anio=2026')
            ->assertOk()
            ->assertJsonPath('data.monto_pagado', 4000)
            ->assertJsonPath('data.monto_total', 39280)
            ->assertJsonPath('data.monto_mora', 35280);
    }

    public function test_saldos_gestion_sin_inscripciones(): void
    {
        $this->getJson('/api/saldos/gestion?anio=1999')
            ->assertOk()
            ->assertJsonPath('data.monto_pagado', 0)
            ->assertJsonPath('data.monto_total', 0);
    }
}

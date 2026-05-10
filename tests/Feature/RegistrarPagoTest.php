<?php

namespace Tests\Feature;

use App\Models\Pago;
use Database\Seeders\PostgradoSeeder;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrarPagoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
        $this->seed(PostgradoSeeder::class);
    }

    public function test_puede_registrar_cuota_dos(): void
    {
        $response = $this->postJson('/api/pagos', [
            'Id_E' => 1,
            'Id_Cur' => 1,
            'MontoP' => 150.25,
            'TipoP' => 'Cuota',
            'NroP' => 2,
            'FechaP' => '2026-04-27',
            'NroCompP' => 88776655,
            'CuentaTransfP' => '12345678',
        ]);

        $response->assertCreated();
        $this->assertIsInt($response->json('data.Id_P'));
        $this->assertGreaterThan(0, $response->json('data.Id_P'));
    }

    public function test_rechaza_matricula_duplicada(): void
    {
        $response = $this->postJson('/api/pagos', [
            'Id_E' => 1,
            'Id_Cur' => 1,
            'MontoP' => 500,
            'TipoP' => 'Matricula',
            'NroP' => 0,
            'FechaP' => '2026-04-27',
            'NroCompP' => 111,
            'CuentaTransfP' => '99999999',
        ]);

        $response->assertUnprocessable();
    }

    public function test_puede_adjuntar_comprobante_imagen_opcional(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('comprobante.jpg', 80, 80);

        $response = $this->post('/api/pagos', [
            'Id_E' => 1,
            'Id_Cur' => 1,
            'MontoP' => 160.00,
            'TipoP' => 'Cuota',
            'NroP' => 3,
            'FechaP' => '2026-04-28',
            'NroCompP' => 88776657,
            'CuentaTransfP' => '12345678',
            'comprobante' => $file,
        ]);

        $response->assertCreated();
        $path = Pago::query()->orderByDesc('Id_P')->value('ArchivoComprobanteP');
        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);
    }
}

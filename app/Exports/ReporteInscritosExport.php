<?php

namespace App\Exports;

use App\Models\Curso;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class ReporteInscritosExport implements FromCollection, WithHeadings, WithMapping, WithEvents, ShouldAutoSize
{
    protected $inscripciones;
    protected $curso;
    protected $nroModulos;

    public function __construct(Collection $inscripciones, Curso $curso)
    {
        $this->inscripciones = $inscripciones;
        $this->curso = $curso;

        // Estructura de módulos por fase académica
        $this->modulosDiplomado = $curso->nro_modulos_diplomado;
        $this->modulosEspecialidad = $curso->nro_modulos_especialidad ?? 0;
        $this->modulosMaestria = $curso->nro_modulos_maestria ?? 0;

        // Total de módulos para cálculos
        $this->nroModulos = $this->modulosDiplomado + $this->modulosEspecialidad + $this->modulosMaestria;
    }

    public function collection()
    {
        return $this->inscripciones;
    }

    /**
     * Genera los encabezados intercalando defensas entre módulos según progresión académica.
     */
    public function headings(): array
    {
        $headers = [
            'N°',
            'APELLIDOS Y NOMBRES',
            'N° REGISTRO',
            'CARNET DE IDENTIDAD',
            'N° DE CELULAR',
            'OBSERVACIONES',
            'FECHA MATRÍCULA',
            'MATRÍCULA'
        ];

        $contadorModulo = 1;

        // Fase 1: Módulos de Diplomado
        for ($i = 1; $i <= $this->modulosDiplomado; $i++) {
            $headers[] = "FECHA MOD. $contadorModulo";
            $headers[] = "MÓDULO $contadorModulo";
            $contadorModulo++;
        }

        // Defensa de Diplomado (después de los módulos de diplomado)
        $headers[] = 'FECHA DEF. DIPLOMADO';
        $headers[] = 'DEF. DIPLOMADO';

        // Fase 2: Módulos de Especialidad (si existen)
        for ($i = 1; $i <= $this->modulosEspecialidad; $i++) {
            $headers[] = "FECHA MOD. $contadorModulo";
            $headers[] = "MÓDULO $contadorModulo";
            $contadorModulo++;
        }

        // Defensa de Especialidad (si hay módulos de especialidad)
        if ($this->modulosEspecialidad > 0 || $this->curso->tipo->value === 'Especialidad' || $this->curso->tipo->value === 'Maestria') {
            $headers[] = 'FECHA DEF. ESPECIALIDAD';
            $headers[] = 'DEF. ESPECIALIDAD';
        }

        // Fase 3: Módulos de Maestría (si existen)
        for ($i = 1; $i <= $this->modulosMaestria; $i++) {
            $headers[] = "FECHA MOD. $contadorModulo";
            $headers[] = "MÓDULO $contadorModulo";
            $contadorModulo++;
        }

        // Defensa de Maestría (si es maestría)
        if ($this->curso->tipo->value === 'Maestria') {
            $headers[] = 'FECHA DEF. MAESTRÍA';
            $headers[] = 'DEF. MAESTRÍA';
        }

        // Columnas finales de Control Financiero
        $headers[] = 'TOTAL MOD. PAGADOS';
        $headers[] = 'TOTAL X PAGAR';

        return $headers;
    }

    /**
     * Mapea y distribuye la información en sus respectivas celdas por estudiante.
     */
    public function map($inscripcion): array
    {
        static $contador = 0;
        $contador++;

        $estudiante = $inscripcion->estudiante;
        $planPago = $inscripcion->planPago;

        $nombreCompleto = trim(strtoupper(($estudiante->paterno ?? '') . ' ' . ($estudiante->materno ?? '') . ' ' . ($estudiante->nombres ?? '')));

        // Datos Personales Base
        $fila = [
            $contador,
            $nombreCompleto,
            $estudiante->registro ?? '',
            $estudiante->cedula ?? '',
            $estudiante->celular ?? '',
            $estudiante->observaciones ?? '',
        ];

        // 1. Extraer Matrícula
        $detMatricula = null;
        if ($planPago) {
            $detMatricula = $planPago->detalles->first(function($d) {
                return str_contains(strtolower($d->concepto), 'matr');
            });
        }
        $pagoMatricula = $detMatricula ? $detMatricula->pagos->sortBy('fecha_pago')->first() : null;
        $fila[] = $this->formatearFecha($pagoMatricula);
        $fila[] = $detMatricula ? (float)$detMatricula->monto_pagado : 0.00;

        // 2. Extraer Módulos de Diplomado (Fase 1)
        for ($i = 1; $i <= $this->modulosDiplomado; $i++) {
            $detModulo = $this->buscarModulo($planPago, $i);
            $pagoMod = $detModulo ? $detModulo->pagos->sortBy('fecha_pago')->first() : null;
            $fila[] = $this->formatearFecha($pagoMod);
            $fila[] = $detModulo ? (float)$detModulo->monto_pagado : 0.00;
        }

        // 3. Defensa de Diplomado
        $detDefensaDiplomado = $this->buscarDefensa($planPago, 'diplomado');
        $pagoDefensaDiplomado = $detDefensaDiplomado ? $detDefensaDiplomado->pagos->sortBy('fecha_pago')->first() : null;
        $fila[] = $this->formatearFecha($pagoDefensaDiplomado);
        $fila[] = $detDefensaDiplomado ? (float)$detDefensaDiplomado->monto_pagado : 0.00;

        // 4. Módulos de Especialidad (Fase 2) - si existen
        $inicioEspecialidad = $this->modulosDiplomado + 1;
        for ($i = $inicioEspecialidad; $i <= $this->modulosDiplomado + $this->modulosEspecialidad; $i++) {
            $detModulo = $this->buscarModulo($planPago, $i);
            $pagoMod = $detModulo ? $detModulo->pagos->sortBy('fecha_pago')->first() : null;
            $fila[] = $this->formatearFecha($pagoMod);
            $fila[] = $detModulo ? (float)$detModulo->monto_pagado : 0.00;
        }

        // 5. Defensa de Especialidad (si aplica)
        if ($this->modulosEspecialidad > 0 || $this->curso->tipo->value === 'Especialidad' || $this->curso->tipo->value === 'Maestria') {
            $detDefensaEspecialidad = $this->buscarDefensa($planPago, 'especialidad');
            $pagoDefensaEspecialidad = $detDefensaEspecialidad ? $detDefensaEspecialidad->pagos->sortBy('fecha_pago')->first() : null;
            $fila[] = $this->formatearFecha($pagoDefensaEspecialidad);
            $fila[] = $detDefensaEspecialidad ? (float)$detDefensaEspecialidad->monto_pagado : 0.00;
        }

        // 6. Módulos de Maestría (Fase 3) - si existen
        $inicioMaestria = $this->modulosDiplomado + $this->modulosEspecialidad + 1;
        for ($i = $inicioMaestria; $i <= $this->nroModulos; $i++) {
            $detModulo = $this->buscarModulo($planPago, $i);
            $pagoMod = $detModulo ? $detModulo->pagos->sortBy('fecha_pago')->first() : null;
            $fila[] = $this->formatearFecha($pagoMod);
            $fila[] = $detModulo ? (float)$detModulo->monto_pagado : 0.00;
        }

        // 7. Defensa de Maestría (si es maestría)
        if ($this->curso->tipo->value === 'Maestria') {
            $detDefensaMaestria = $this->buscarDefensa($planPago, 'maestria');
            $pagoDefensaMaestria = $detDefensaMaestria ? $detDefensaMaestria->pagos->sortBy('fecha_pago')->first() : null;
            $fila[] = $this->formatearFecha($pagoDefensaMaestria);
            $fila[] = $detDefensaMaestria ? (float)$detDefensaMaestria->monto_pagado : 0.00;
        }

        // 8. Espacio para cálculos matemáticos nativos de Excel en AfterSheet
        $fila[] = 0; // Columna Total Pagado
        $fila[] = 0; // Columna Total Por Pagar

        return $fila;
    }

    /**
     * Inyección y cálculo dinámico de las celdas en Excel.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $totalFilas = $this->inscripciones->count();
                $filaInicioData = 2;
                $filaFinData = $totalFilas + 1;

                // Estilo Ejecutivo Gris Azulado Profesional
                $estiloCabecera = [
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '475569']]]
                ];

                $ultimaColumnaLetra = $sheet->getHighestColumn();
                $sheet->getStyle("A1:{$ultimaColumnaLetra}1")->applyFromArray($estiloCabecera);
                $sheet->getRowDimension(1)->setRowHeight(32);

                // Mapear Coordenadas Reales de las columnas de Dinero a Sumar
                $indiceMatricula = 8; // Columna H
                $indicesMontos = [$indiceMatricula];

                // Calcular índices dinámicamente según la nueva estructura
                $indiceActual = 10; // Empieza en columna J (después de matrícula)

                // Módulos de Diplomado (Fase 1)
                for ($i = 1; $i <= $this->modulosDiplomado; $i++) {
                    $indicesMontos[] = $indiceActual + 1; // Columna de monto
                    $indiceActual += 2;
                }

                // Defensa de Diplomado
                $indicesMontos[] = $indiceActual + 1;
                $indiceActual += 2;

                // Módulos de Especialidad (Fase 2)
                for ($i = 1; $i <= $this->modulosEspecialidad; $i++) {
                    $indicesMontos[] = $indiceActual + 1;
                    $indiceActual += 2;
                }

                // Defensa de Especialidad (si aplica)
                if ($this->modulosEspecialidad > 0 || $this->curso->tipo->value === 'Especialidad' || $this->curso->tipo->value === 'Maestria') {
                    $indicesMontos[] = $indiceActual + 1;
                    $indiceActual += 2;
                }

                // Módulos de Maestría (Fase 3)
                for ($i = 1; $i <= $this->modulosMaestria; $i++) {
                    $indicesMontos[] = $indiceActual + 1;
                    $indiceActual += 2;
                }

                // Defensa de Maestría (si es maestría)
                if ($this->curso->tipo->value === 'Maestria') {
                    $indicesMontos[] = $indiceActual + 1;
                    $indiceActual += 2;
                }

                // Columnas de control final
                $colTotalPagadoLetra = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($indiceActual + 1);
                $colTotalPorPagarLetra = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($indiceActual + 2);

                // Calcular el Costo Total Estático Teórico del Programa
                $costoTotalPrograma = (float)$this->curso->costo_total_estudio
                    + (float)$this->curso->costo_matricula
                    + (float)$this->curso->costo_defensa_diplomado
                    + (float)$this->curso->costo_defensa_especialidad
                    + (float)$this->curso->costo_defensa_maestria;

                // Escritura de fórmulas fila por fila
                for ($row = $filaInicioData; $row <= $filaFinData; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(22);

                    $celdasASumar = [];
                    foreach ($indicesMontos as $idx) {
                        $letra = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($idx);
                        $celdasASumar[] = "{$letra}{$row}";
                    }

                    // Formula Total Pagado
                    $formulaSuma = '=SUM(' . implode(',', $celdasASumar) . ')';
                    $sheet->setCellValue("{$colTotalPagadoLetra}{$row}", $formulaSuma);

                    // Formula Deuda Restante
                    $sheet->setCellValue("{$colTotalPorPagarLetra}{$row}", "={$costoTotalPrograma}-{$colTotalPagadoLetra}{$row}");

                    // Formatear visualmente bordes, textos y máscara monetaria
                    $sheet->getStyle("A{$row}:{$colTotalPorPagarLetra}{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');
                    $sheet->getStyle("G{$row}:{$colTotalPorPagarLetra}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("H{$row}:{$colTotalPorPagarLetra}{$row}")->getNumberFormat()->setFormatCode('#,##0.00');

                    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("C{$row}:E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            }
        ];
    }

    /**
     * Helper para formatear de forma segura objetos de fecha.
     */
    private function formatearFecha($pago): string
    {
        if (!$pago || !$pago->fecha_pago) {
            return '-';
        }

        return $pago->fecha_pago instanceof Carbon
            ? $pago->fecha_pago->format('Y-m-d')
            : Carbon::parse($pago->fecha_pago)->format('Y-m-d');
    }

    /**
     * Busca un módulo por su número en el plan de pago.
     */
    private function buscarModulo($planPago, $nroModulo)
    {
        if (!$planPago) {
            return null;
        }

        $detModulo = $planPago->detalles->where('nro_modulo', $nroModulo)->first();
        if (!$detModulo) {
            // Búsqueda alternativa por número de cuota (excluyendo matrículas y defensas)
            $detModulo = $planPago->detalles
                ->filter(function($d) {
                    $c = strtolower($d->concepto);
                    return !str_contains($c, 'matr') && !str_contains($c, 'defen') && !str_contains($c, 'defenz');
                })->where('nro_cuota', $nroModulo)->first();
        }

        return $detModulo;
    }

    /**
     * Busca una defensa por su fase en el plan de pago.
     */
    private function buscarDefensa($planPago, $fase)
    {
        if (!$planPago) {
            return null;
        }

        return $planPago->detalles->first(function($d) use ($fase) {
            $concepto = strtolower($d->concepto);
            $campoFase = strtolower($d->fase ?? '');

            $esDefensa = str_contains($concepto, 'defen') || str_contains($concepto, 'defenz');
            $correspondeAFase = str_contains($concepto, $fase) || str_contains($campoFase, $fase);

            return $esDefensa && $correspondeAFase;
        });
    }
}

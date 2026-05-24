<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Pagos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        .info {
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
        }
        .info span {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #333;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .total {
            margin-top: 20px;
            text-align: right;
            font-size: 14px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE PAGOS</h1>
        <p>Sistema de Gestión de Postgrado - F.I.N.O.R</p>
        <p>Fecha de generación: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="info">
        <p><span>Tipo de Reporte:</span> {{ ucfirst($data['tipo_reporte']) }}</p>
        @if($data['fecha_desde'])
            <p><span>Fecha Desde:</span> {{ \Carbon\Carbon::parse($data['fecha_desde'])->format('d/m/Y') }}</p>
        @endif
        @if($data['fecha_hasta'])
            <p><span>Fecha Hasta:</span> {{ \Carbon\Carbon::parse($data['fecha_hasta'])->format('d/m/Y') }}</p>
        @endif
        <p><span>Total de Registros:</span> {{ $pagos->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>N°</th>
                <th>Fecha de Pago</th>
                <th>Estudiante</th>
                <th>C.I.</th>
                <th>Monto</th>
                <th>Concepto</th>
            </tr>
        </thead>
        <tbody>
            @php($contador = 0)
            @foreach($pagos as $pago)
                @php($contador++)
                <tr>
                    <td>{{ $contador }}</td>
                    <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                    <td>{{ $pago->inscripcion?->estudiante?->nombre_completo ?? 'N/A' }}</td>
                    <td>{{ $pago->inscripcion?->estudiante?->cedula ?? 'N/A' }}</td>
                    <td>{{ number_format($pago->monto, 2) }} Bs</td>
                    <td>{{ $pago->observacion ?? 'Pago general' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        <p>Total General: {{ number_format($pagos->sum('monto'), 2) }} Bs</p>
    </div>

    <div class="footer">
        <p>Este reporte es un documento oficial generado automáticamente por el sistema.</p>
        <p>F.I.N.O.R - Facultad de Ingeniería y Oficios Rurales</p>
    </div>
</body>
</html>

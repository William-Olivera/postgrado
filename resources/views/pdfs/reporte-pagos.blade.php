<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Pagos</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 6px; }
        th { background: #1a1a2e; color: white; }
        tr:nth-child(even) { background: #f2f2f2; }
        .footer { margin-top: 20px; font-size: 9px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>F.I.N.O.R - Escuela de Postgrado</h2>
        <h3>Reporte de Pagos</h3>
        <p>Generado: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th><th>Fecha</th><th>Estudiante</th><th>Curso</th><th>Concepto</th><th>Comprobante</th><th>Monto</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($pagos as $i => $p)
            @php $total += $p->monto; @endphp
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $p->fecha_pago }}</td>
                <td>{{ $p->detallePlanPago->planPago->inscripcion->estudiante->nombreCompleto() }}</td>
                <td>{{ $p->detallePlanPago->planPago->inscripcion->curso->nombre }}</td>
                <td>{{ $p->detallePlanPago->concepto }}</td>
                <td>{{ $p->nro_comprobante }}</td>
                <td style="text-align:right;">Bs. {{ number_format($p->monto, 2) }}</td>
            </tr>
            @endforeach
            <tr style="font-weight:bold; background:#ddd;">
                <td colspan="6" style="text-align:right;">TOTAL:</td>
                <td style="text-align:right;">Bs. {{ number_format($total, 2) }}</td>
            </tr>
        </tbody>
    </table>
    <div class="footer">Sistema de Postgrado F.I.N.O.R</div>
</body>
</html>

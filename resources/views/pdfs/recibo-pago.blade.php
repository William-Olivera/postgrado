<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pago</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 40px; }
        .header { text-align: center; border-bottom: 3px solid #1a1a2e; padding-bottom: 15px; margin-bottom: 25px; }
        .header h2 { margin: 0; color: #1a1a2e; }
        .header h3 { margin: 5px 0 0; color: #555; font-size: 14px; }
        .datos { margin-bottom: 20px; }
        .datos p { margin: 6px 0; }
        .datos strong { display: inline-block; width: 140px; color: #333; }
        .monto-box { background: #f8f9fa; border: 2px solid #1a1a2e; padding: 15px; text-align: center; margin: 20px 0; }
        .monto-box h1 { margin: 0; color: #1a1a2e; font-size: 28px; }
        .footer { margin-top: 40px; border-top: 1px solid #ddd; padding-top: 15px; font-size: 10px; color: #666; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h2>F.I.N.O.R</h2>
        <h3>Escuela de Postgrado</h3>
        <small>Recibo Oficial de Pago</small>
    </div>

    <div class="datos">
        <p><strong>N° Recibo:</strong> P-{{ str_pad($pago->id, 5, '0', STR_PAD_LEFT) }}</p>
        <p><strong>Fecha:</strong> {{ $pago->fecha_pago->format('d/m/Y') }}</p>
        <p><strong>N° Comprobante:</strong> {{ $pago->nro_comprobante }}</p>
    </div>

    <div class="datos">
        <p><strong>Estudiante:</strong> {{ $pago->detallePlanPago->planPago->inscripcion->estudiante->nombreCompleto() }}</p>
        <p><strong>Cédula:</strong> {{ $pago->detallePlanPago->planPago->inscripcion->estudiante->cedula }}</p>
        <p><strong>Curso:</strong> {{ $pago->detallePlanPago->planPago->inscripcion->curso->nombre }}</p>
        <p><strong>Concepto:</strong> {{ $pago->detallePlanPago->concepto }}</p>
    </div>

    <div class="monto-box">
        <small>MONTO PAGADO</small>
        <h1>Bs. {{ number_format($pago->monto, 2) }}</h1>
    </div>

    <div class="datos">
        <p><strong>Saldo Pendiente Cuota:</strong> Bs. {{ number_format($pago->detallePlanPago->saldo_cuota, 2) }}</p>
        <p><strong>Saldo Total Pendiente:</strong> Bs. {{ number_format($pago->detallePlanPago->planPago->saldo_pendiente, 2) }}</p>
    </div>

    <div class="footer">
        Documento generado el {{ now()->format('d/m/Y H:i:s') }} | Sistema de Postgrado F.I.N.O.R<br>
        Este recibo es un comprobante de pago. Conserve este documento.
    </div>
</body>
</html>

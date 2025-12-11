<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura <?= $factura['numero_fiscal'] ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body { background: #525659; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 20px; display: flex; justify-content: center; }
        .invoice-box { background: white; max-width: 800px; width: 100%; padding: 40px; box-shadow: 0 0 15px rgba(0,0,0,0.15); min-height: 1000px; position: relative; }
        
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .company-info h1 { margin: 0; color: #333; font-size: 24px; }
        .company-info p { margin: 2px 0; color: #666; font-size: 14px; }
        
        .invoice-meta { text-align: right; }
        .invoice-meta h2 { margin: 0; color: #2563eb; text-transform: uppercase; }
        .badge { padding: 5px 10px; border-radius: 4px; color: white; font-weight: bold; font-size: 12px; display: inline-block; margin-top: 5px; }
        .bg-pagada { background: #10b981; }
        .bg-emitida { background: #f59e0b; }
        
        .bill-to { margin-bottom: 30px; padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; }
        .table-invoice { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table-invoice th { background: #f1f5f9; padding: 12px; text-align: left; font-weight: 600; color: #475569; border-bottom: 2px solid #e2e8f0; }
        .table-invoice td { padding: 12px; border-bottom: 1px solid #e2e8f0; color: #333; }
        .text-right { text-align: right; }
        
        .totals { margin-left: auto; width: 300px; }
        .totals-row { display: flex; justify-content: space-between; padding: 5px 0; }
        .totals-row.final { font-weight: bold; font-size: 18px; border-top: 2px solid #333; margin-top: 10px; padding-top: 10px; }

        .toolbar { position: fixed; top: 0; left: 0; right: 0; background: #333; padding: 10px; text-align: center; z-index: 100; box-shadow: 0 2px 5px rgba(0,0,0,0.3); }
        .btn { padding: 8px 15px; border-radius: 4px; border: none; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; }
        .btn-print { background: white; color: #333; }
        .btn-pay { background: #10b981; color: white; margin-left: 10px; }

        @media print { .toolbar { display: none; } body { background: white; padding: 0; } .invoice-box { box-shadow: none; } }
    </style>
</head>
<body>

    <div class="toolbar" data-html2canvas-ignore="true">
        <button onclick="window.print()" class="btn btn-print"><i class="fas fa-print"></i> Imprimir / PDF</button>
        <?php if($factura['estado'] === 'emitida'): ?>
            <a href="<?= ENV_APP['BASE_URL'] ?>/pagos/pagar/<?= $factura['id'] ?>" class="btn btn-pay">
                <i class="fas fa-credit-card"></i> Pagar Ahora
            </a>
        <?php endif; ?>
    </div>

    <div class="invoice-box" id="factura">
        <div class="header">
            <div class="company-info">
                <h1>Consultores Chiriquí S.A.</h1>
                <p>RUC: 155694852-2-2025 DV 55</p>
                <p>Plaza Las Lomas, David, Chiriquí</p>
                <p>facturacion@consultores.com</p>
            </div>
            
            <!-- QR CODE -->
            <?php if(isset($qrUrl)): ?>
                <div style="text-align: center; margin: 0 20px;">
                    <img src="<?= $qrUrl ?>" alt="QR Factura Electrónica" style="width: 100px; height: 100px; border: 1px solid #ddd; padding: 2px;">
                </div>
            <?php endif; ?>

            <div class="invoice-meta">
                <h2>Factura Electrónica</h2>
                <p><strong>Nº:</strong> <?= $factura['numero_fiscal'] ?></p>
                <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($factura['fecha_emision'])) ?></p>
                <span class="badge bg-<?= $factura['estado'] ?>"><?= strtoupper($factura['estado']) ?></span>
            </div>
        </div>

        <div class="bill-to">
            <h3 style="margin: 0 0 10px 0; font-size: 14px; color: #64748b; text-transform: uppercase;">Facturado A:</h3>
            <div style="font-size: 16px; font-weight: bold;"><?= htmlspecialchars($factura['empresa_nombre']) ?></div>
            <div>RUC: <?= htmlspecialchars($factura['ruc']) ?></div>
            <div><?= htmlspecialchars($factura['direccion']) ?></div>
        </div>

        <table class="table-invoice">
            <thead>
                <tr>
                    <th width="50%">Descripción</th>
                    <th width="15%" class="text-right">Cant.</th>
                    <th width="15%" class="text-right">Precio</th>
                    <th width="20%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($detalles as $det): ?>
                <tr>
                    <td>
                        <?php 
                            $map = [
                                'ver_detalle' => 'Visualización de Vacante',
                                'click_aplicar' => 'Postulación Recibida',
                                'chat_consulta' => 'Consulta Asistente IA'
                            ];
                            echo $map[$det['tipo_interaccion']] ?? 'Servicio de Plataforma';
                        ?>
                    </td>
                    <td class="text-right"><?= $det['cantidad_interacciones'] ?></td>
                    <td class="text-right">B/. <?= number_format($det['tarifa_unitaria'], 2) ?></td>
                    <td class="text-right">B/. <?= number_format($det['total_linea'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-row">
                <span>Subtotal:</span>
                <span>B/. <?= number_format($factura['subtotal'], 2) ?></span>
            </div>
            <div class="totals-row">
                <span>ITBMS (7%):</span>
                <span>B/. <?= number_format($factura['itbms'], 2) ?></span>
            </div>
            <div class="totals-row final">
                <span>TOTAL A PAGAR:</span>
                <span>B/. <?= number_format($factura['total'], 2) ?></span>
            </div>
        </div>

        <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; text-align: center;">
            Documento generado electrónicamente. Resolución DGI No. 201-2025.<br>
            CUFE: <?= $factura['cufe'] ?? 'N/A' ?>
        </div>
    </div>

</body>
</html>

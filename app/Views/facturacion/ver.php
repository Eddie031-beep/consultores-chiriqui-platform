<?php
use App\Helpers\Auth;
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura FE-<?= $factura['numero_fiscal'] ?> | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <style>
        /* Estilos Base */
        body { background: #f3f4f6; color: #1e293b; font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }

        /* Contenedor Principal de la Factura */
        .invoice-box {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            position: relative;
        }

        /* Encabezado */
        .header-row { display: flex; justify-content: space-between; border-bottom: 2px solid #e2e8f0; padding-bottom: 1rem; margin-bottom: 1rem; }
        .company-info h1 { font-size: 1.4rem; color: #1e293b; margin: 0; font-weight: 800; }
        .company-info p { color: #64748b; margin: 2px 0 0; font-size: 0.8rem; }
        
        .invoice-meta { text-align: right; }
        .invoice-meta h2 { color: #2563eb; margin: 0; font-size: 1.1rem; text-transform: uppercase; }
        .invoice-meta .meta-item { color: #475569; margin-top: 4px; font-size: 0.8rem; font-family: monospace; }

        /* Caja DGI (Gris) */
        .dgi-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 0.5rem 0.8rem;
            margin-bottom: 1rem;
            font-size: 0.7rem; 
            color: #475569;
            font-family: monospace;
        }
        .dgi-line { display: flex; justify-content: space-between; margin-bottom: 2px; }

        /* Cliente y Periodo */
        .info-row { display: flex; justify-content: space-between; margin-bottom: 1.5rem; font-size: 0.85rem; }
        .client-col { width: 55%; }
        .period-col { width: 40%; text-align: right; }
        
        h3.section-title { font-size: 0.75rem; text-transform: uppercase; color: #94a3b8; margin-bottom: 0.4rem; font-weight: 700; border-bottom: 1px solid #f1f5f9; padding-bottom: 2px; }
        .info-text { margin: 2px 0; color: #1e293b; }
        .info-text strong { font-weight: 600; }

        /* Tabla */
        .invoice-table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; font-size: 0.85rem; }
        .invoice-table th { background: #f1f5f9; padding: 0.5rem; text-align: left; font-weight: 700; color: #475569; border-bottom: 2px solid #e2e8f0; }
        .invoice-table td { padding: 0.5rem; border-bottom: 1px solid #e2e8f0; color: #334155; }
        .invoice-table .text-right { text-align: right; }
        .invoice-table .text-center { text-align: center; }

        /* Footer (Totales y QR) */
        .footer-row { display: flex; align-items: flex-start; justify-content: space-between; margin-top: 0.5rem; border-top: 2px solid #e2e8f0; padding-top: 1rem; }
        
        .qr-area { display: flex; align-items: center; gap: 10px; width: 60%; }
        .qr-img { width: 80px; height: 80px; border: 1px solid #e2e8f0; padding: 2px; }
        .qr-desc { font-size: 0.7rem; color: #94a3b8; line-height: 1.3; }

        .totals-area { width: 35%; font-size: 0.85rem; }
        .total-line { display: flex; justify-content: space-between; padding: 0.2rem 0; color: #64748b; }
        .total-line.grand-total { font-weight: 800; color: #1e293b; font-size: 1rem; border-top: 1px solid #e2e8f0; margin-top: 0.3rem; padding-top: 0.3rem; }

        /* Estado Badge */
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
        .bg-emitida { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
        .bg-pagada { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .bg-anulada { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

        /* Botones (No se imprimen) */
        .actions-panel { 
            max-width: 800px; margin: 0 auto 2rem auto; 
            background: white; padding: 1rem; border-radius: 8px; 
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .btn-group { display: flex; gap: 10px; }
        .btn { padding: 0.6rem 1.2rem; border-radius: 6px; text-decoration: none; font-weight: 600; cursor: pointer; border: none; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 6px; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-light { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .btn:hover { opacity: 0.9; transform: translateY(-1px); }

        /* Ocultar en PDF/Impresión */
        @media print {
            .no-print { display: none !important; }
            .invoice-box { box-shadow: none; margin: 0; border: none; padding: 0; }
            body { background: white; }
        }
        /* Clase especial para html2pdf */
        .html2pdf__page-break { page-break-before: always; }
    </style>
</head>
<body>

    <div class="no-print" data-html2canvas-ignore="true">
        <?php include __DIR__ . '/../components/navbar.php'; ?>
    </div>

    <?php if ($user['rol'] === 'admin_consultora'): ?>
    <div class="actions-panel no-print" data-html2canvas-ignore="true">
        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion" style="color: #64748b; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
            &larr; Volver a Facturación
        </a>

        <div class="btn-group">
            <form action="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion/actualizar/<?= $factura['id'] ?>" method="POST" style="display:flex; gap: 5px;">
                <input type="hidden" name="factura_id" value="<?= $factura['id'] ?>">
                <select name="estado_factura" style="padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; background: white;">
                    <option value="emitida" <?= $factura['estado'] === 'emitida' ? 'selected' : '' ?>>Emitida</option>
                    <option value="pagada" <?= $factura['estado'] === 'pagada' ? 'selected' : '' ?>>Pagada</option>
                    <option value="anulada" <?= $factura['estado'] === 'anulada' ? 'selected' : '' ?>>Anulada</option>
                </select>
                <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem;">Guardar</button>
            </form>

            <button onclick="generarPDF()" class="btn btn-light">
                <i class="fas fa-file-pdf" style="color: #ef4444;"></i> Descargar
            </button>
            
            <button onclick="window.print()" class="btn btn-light">
                <i class="fas fa-print"></i>
            </button>
        </div>
    </div>
    <?php endif; ?>

    <div class="invoice-box" id="facturaPDF">
        
        <div class="header-row">
            <div class="company-info">
                <h1>Consultores Chiriquí S.A.</h1>
                <p>RUC: 155694852-2-2025 DV 55</p>
                <p>Plaza Las Lomas, David, Chiriquí | +507 775-0000</p>
                <p>facturacion@consultores.com</p>
            </div>
            <div class="invoice-meta">
                <h2>Factura Electrónica</h2>
                <div class="meta-item">No. <?= $factura['numero_fiscal'] ?></div>
                <div class="meta-item">Fecha: <?= date('d/m/Y', strtotime($factura['fecha_emision'])) ?></div>
                <div style="margin-top: 5px;">
                    <span class="badge bg-<?= $factura['estado'] ?>"><?= strtoupper($factura['estado']) ?></span>
                </div>
            </div>
        </div>

        <div class="dgi-box">
            <div class="dgi-line"><span>CUFE:</span> <strong><?= $factura['cufe'] ?? 'N/A' ?></strong></div>
            <div class="dgi-line"><span>Autorización:</span> <strong><?= $factura['protocolo_autorizacion'] ?? 'N/A' ?></strong></div>
            <div class="dgi-line"><span>Clave Acceso:</span> <strong><?= $factura['clave_acceso'] ?? 'N/A' ?></strong></div>
        </div>

        <div class="info-row">
            <div class="client-col">
                <h3 class="section-title">Facturado A:</h3>
                <div class="info-text"><strong><?= htmlspecialchars($factura['empresa_nombre']) ?></strong></div>
                <div class="info-text">RUC: <?= htmlspecialchars($factura['ruc'] ?? 'N/A') ?></div>
                <div class="info-text"><?= htmlspecialchars($factura['direccion'] ?? '') ?></div>
                <div class="info-text"><?= htmlspecialchars($factura['email_contacto'] ?? '') ?></div>
            </div>
            <div class="period-col">
                <h3 class="section-title">Período de Servicio</h3>
                <div class="info-text">Desde: <?= date('d/m/Y', strtotime($factura['periodo_desde'])) ?></div>
                <div class="info-text">Hasta: <?= date('d/m/Y', strtotime($factura['periodo_hasta'])) ?></div>
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th width="55%">Descripción</th>
                    <th width="15%" class="text-center">Cant.</th>
                    <th width="15%" class="text-right">Precio</th>
                    <th width="15%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($detalles)): ?>
                    <tr><td colspan="4" class="text-center">Sin detalles registrados.</td></tr>
                <?php else: ?>
                    <?php foreach ($detalles as $det): ?>
                    <tr>
                        <td>
                            <?php 
                                // FIX: Detectar nombre del servicio
                                $desc = "Servicio de Plataforma";
                                if (!empty($det['tipo_interaccion'])) {
                                    $map = [
                                        'ver_detalle' => 'Visualización de Vacante',
                                        'click_aplicar' => 'Postulación (Click)',
                                        'chat_consulta' => 'Consulta Asistente IA'
                                    ];
                                    $desc = $map[$det['tipo_interaccion']] ?? ucfirst(str_replace('_', ' ', $det['tipo_interaccion']));
                                } else {
                                    // Fallback por precio unitario
                                    $p = (float)$det['tarifa_unitaria'];
                                    if($p == 1.50) $desc = 'Visualización de Vacante';
                                    elseif($p == 5.00) $desc = 'Postulación (Click)';
                                    elseif($p == 2.50) $desc = 'Consulta Asistente IA';
                                }
                                echo htmlspecialchars($desc);
                            ?>
                        </td>
                        <td class="text-center"><?= $det['cantidad_interacciones'] ?></td>
                        <td class="text-right">B/. <?= number_format($det['tarifa_unitaria'], 2) ?></td>
                        <td class="text-right" style="font-weight: 600;">B/. <?= number_format($det['total_linea'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="footer-row">
            <div class="qr-area">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?= urlencode("CUFE:{$factura['cufe']}|TOTAL:{$factura['total']}") ?>" class="qr-img">
                <div class="qr-desc">
                    Escanee para validar en DGI.<br>
                    Resolución No. 201-2025.<br>
                    Documento Oficial.
                </div>
            </div>
            
            <div class="totals-area">
                <div class="total-line">
                    <span>Subtotal:</span>
                    <span>B/. <?= number_format($factura['subtotal'], 2) ?></span>
                </div>
                <div class="total-line">
                    <span>ITBMS (7%):</span>
                    <span>B/. <?= number_format($factura['itbms'], 2) ?></span>
                </div>
                <div class="total-line grand-total">
                    <span>TOTAL:</span>
                    <span>B/. <?= number_format($factura['total'], 2) ?></span>
                </div>
            </div>
        </div>

    </div>

    <script>
        function generarPDF() {
            const element = document.getElementById('facturaPDF');
            
            // Configuración optimizada para 1 sola página
            const opt = {
                margin:       0.3, // Margen muy pequeño (pulgadas)
                filename:     'Factura_<?= $factura['numero_fiscal'] ?>.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true, scrollY: 0 },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            
            html2pdf().set(opt).from(element).save();
        }
    </script>

</body>
</html>
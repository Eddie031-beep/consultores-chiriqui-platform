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
    <style>
        .invoice-container {
            max-width: 900px;
            margin: 2rem auto;
            background: white;
            padding: 3rem;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            /* font-family: 'Arial', sans-serif; Removed to match global theme */
            position: relative;
            position: relative;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #eee;
            padding-bottom: 2rem;
            margin-bottom: 2rem;
        }
        .company-logo h1 { font-size: 1.8rem; color: #1e293b; margin: 0; }
        .company-logo p { color: #64748b; margin: 5px 0 0; }
        
        .invoice-details { text-align: right; }
        .invoice-details h2 { color: #4f46e5; margin: 0; font-size: 1.5rem; }
        .invoice-details .meta { color: #64748b; margin-top: 10px; font-size: 0.9rem; }

        .dgi-info {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            padding: 1rem;
            margin-bottom: 2rem;
            font-size: 0.85rem;
            color: #475569;
            word-break: break-all;
        }
        .dgi-info strong { color: #334155; }

        .bill-to { display: flex; justify-content: space-between; margin-bottom: 2rem; }
        .bill-box h3 { font-size: 1rem; text-transform: uppercase; color: #94a3b8; margin-bottom: 1rem; }
        .bill-box p { margin: 3px 0; color: #1e293b; }

        .table-invoice { width: 100%; border-collapse: collapse; margin-bottom: 2rem; }
        .table-invoice th { background: #f1f5f9; padding: 1rem; text-align: left; font-weight: 600; color: #475569; }
        .table-invoice td { padding: 1rem; border-bottom: 1px solid #e2e8f0; color: #334155; }
        .table-invoice tr:last-child td { border-bottom: none; }
        .table-invoice .text-right { text-align: right; }

        .totals { margin-left: auto; width: 300px; }
        .totals-row { display: flex; justify-content: space-between; padding: 0.5rem 0; color: #64748b; }
        .totals-row.final { font-weight: 700; color: #1e293b; font-size: 1.2rem; border-top: 2px solid #e2e8f0; margin-top: 0.5rem; padding-top: 1rem; }

        .qr-section { margin-top: 3rem; display: flex; align-items: center; gap: 2rem; border-top: 1px solid #eee; padding-top: 2rem; }
        .qr-code img { width: 120px; height: 120px; border: 1px solid #ddd; padding: 5px; }
        .qr-text { font-size: 0.8rem; color: #94a3b8; max-width: 500px; }

        .status-badge {
            display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;
        }
        .status-emitida { background: #e0f2fe; color: #0284c7; }
        .status-pagada { background: #dcfce7; color: #166534; }
        .status-anulada { background: #fee2e2; color: #991b1b; }
        .status-en_revision { background: #fff7ed; color: #9a3412; }

        .actions-bar { margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end; }
        .btn { padding: 0.6rem 1.2rem; border-radius: 6px; text-decoration: none; font-weight: 500; cursor: pointer; border: none; }
        .btn-primary { background: #4f46e5; color: white; }
        .btn-secondary { background: #e2e8f0; color: #475569; }
        
        @media print {
            .no-print { display: none; }
            .invoice-container { box-shadow: none; border: none; margin: 0; padding: 0; }
        }
    </style>
</head>
<body style="background: #f3f4f6;">

    <div class="no-print">
        <?php include __DIR__ . '/../components/navbar.php'; ?>
    </div>

    <div class="invoice-container">
        
        <?php if(isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?= $_SESSION['mensaje']['tipo'] ?> no-print" style="padding: 1rem; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 2rem;">
                <?= $_SESSION['mensaje']['texto'] ?>
            </div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>

        <div class="invoice-header">
            <div class="company-logo">
                <h1>Consultores Chiriquí S.A.</h1>
                <p>RUC: 155694852-2-2025 DV 55</p>
                <p><i class="fas fa-map-marker-alt"></i> David, Chiriquí, Panamá</p>
                <p><i class="fas fa-envelope"></i> facturacion@consultores.com</p>
            </div>
            <div class="invoice-details">
                <h2>FACTURA ELECTRÓNICA</h2>
                <div class="meta">No. <?= $factura['numero_fiscal'] ?></div>
                <div class="meta">Fecha: <?= date('d/m/Y H:i', strtotime($factura['fecha_emision'])) ?></div>
                <div class="meta">
                    Estado: <span class="status-badge status-<?= $factura['estado'] ?>"><?= str_replace('_', ' ', strtoupper($factura['estado'])) ?></span>
                </div>
            </div>
        </div>

        <!-- DGI INFO -->
        <div class="dgi-info">
            <div style="margin-bottom: 5px;"><strong>CUFE:</strong> <?= $factura['cufe'] ?? 'N/A' ?></div>
            <div style="margin-bottom: 5px;"><strong>Protocolo de Autorización:</strong> <?= $factura['protocolo_autorizacion'] ?? 'N/A' ?></div>
            <div><strong>Clave de Acceso:</strong> <?= $factura['clave_acceso'] ?? 'N/A' ?></div>
            <div><strong>Fecha Autorización:</strong> <?= $factura['fecha_autorizacion'] ?? date('d/m/Y H:i', strtotime($factura['fecha_emision'])) ?></div>
        </div>

        <div class="bill-to">
            <div class="bill-box">
                <h3>Facturado a:</h3>
                <p style="font-weight: 700; font-size: 1.1rem;"><?= htmlspecialchars($factura['empresa_nombre']) ?></p>
                <p><strong>RUC:</strong> <?= htmlspecialchars($factura['ruc'] ?? 'N/A') ?></p>
                <p><strong>Dirección:</strong> <?= htmlspecialchars($factura['direccion'] ?? 'Ciudad de Panamá') ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($factura['email_contacto'] ?? '') ?></p>
            </div>
            <div class="bill-box text-right">
                <h3>Período de Facturación</h3>
                <p>Desde: <?= date('d/m/Y', strtotime($factura['periodo_desde'])) ?></p>
                <p>Hasta: <?= date('d/m/Y', strtotime($factura['periodo_hasta'])) ?></p>
            </div>
        </div>

        <table class="table-invoice">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th class="text-right">Cantidad</th>
                    <th class="text-right">Precio Unit.</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalles as $det): ?>
                <tr>
                    <td>
                        Interacción: 
                        <?php 
                            $names = [
                                'ver_detalle' => 'Visualización de Vacante',
                                'click_aplicar' => 'Click en Aplicar',
                                'chat_consulta' => 'Consulta por Chat'
                            ];
                            echo $names[$det['tipo_interaccion']] ?? $det['tipo_interaccion']; // Fallback fix
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
                <span>Subtotal</span>
                <span>B/. <?= number_format($factura['subtotal'], 2) ?></span>
            </div>
            <div class="totals-row">
                <span>ITBMS (7%)</span>
                <span>B/. <?= number_format($factura['itbms'], 2) ?></span>
            </div>
            <div class="totals-row final">
                <span>TOTAL</span>
                <span>B/. <?= number_format($factura['total'], 2) ?></span>
            </div>
        </div>

        <div class="qr-section">
            <div class="qr-code">
                <img src="<?= $qrUrl ?>" alt="Código QR DGI">
            </div>
            <div class="qr-text">
                <p>Escanee este código QR para verificar la validez de esta factura electrónica en el sistema de la DGI.</p>
                <p>Resolución No. 201-2025 del 20 de Enero de 2025.</p>
            </div>
        </div>

        <!-- ACTIONS (Only for Consultora) -->
        <?php if ($user['rol'] === 'admin_consultora'): ?>
        <div class="actions-bar no-print">
            <form action="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion/actualizar/<?= $factura['id'] ?>" method="POST" style="display:flex; align-items:center; gap: 10px;">
                <input type="hidden" name="factura_id" value="<?= $factura['id'] ?>">
                <select name="estado_factura" class="form-select" style="padding: 0.6rem; border-radius: 6px; border: 1px solid #ddd;">
                    <option value="emitida" <?= $factura['estado'] === 'emitida' ? 'selected' : '' ?>>Emitida</option>
                    <option value="en_revision" <?= $factura['estado'] === 'en_revision' ? 'selected' : '' ?>>En Revisión</option>
                    <option value="pagada" <?= $factura['estado'] === 'pagada' ? 'selected' : '' ?>>Pagada</option>
                    <option value="anulada" <?= $factura['estado'] === 'anulada' ? 'selected' : '' ?>>Anulada</option>
                </select>
                <button type="submit" class="btn btn-primary">Actualizar Estado</button>
            </form>
            <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion/descargar/<?= $factura['id'] ?>" class="btn btn-secondary">
                <i class="fas fa-download"></i> PDF
            </a>

            <a href="javascript:window.print()" class="btn btn-secondary">
                <i class="fas fa-print"></i> Imprimir
            </a>
        </div>
        <?php endif; ?>

    </div>

</body>
</html>

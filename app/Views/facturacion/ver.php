<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura <?= htmlspecialchars($factura['numero_fiscal']) ?></title>
    <!-- Styles -->
    <style>
        body{font-family:'Courier New', Courier, monospace; background:#f1f5f9; padding:2rem;}
        .invoice-box{max-width:800px;margin:auto;padding:30px;border:1px solid #eee;box-shadow:0 0 10px rgba(0,0,0,.15);font-size:16px;line-height:24px;color:#555;background:white;}
        .invoice-box table{width:100%;line-height:inherit;text-align:left;}
        .invoice-box table td{padding:5px;vertical-align:top;}
        .invoice-box table tr td:nth-child(2){text-align:right;}
        .top{padding-bottom:20px;}
        .top .title{font-size:45px;line-height:45px;color:#333;}
        .information{padding-bottom:40px;}
        .heading{background:#eee;border-bottom:1px solid #ddd;font-weight:bold;}
        .details{padding-bottom:20px;}
        .item{border-bottom:1px solid #eee;}
        .item.last{border-bottom:none;}
        .total{border-top:2px solid #eee;font-weight:bold;}
        .qr{text-align: center; margin-top: 30px;}
        .back-btn{display:inline-block;margin-bottom:20px;text-decoration:none;color:#555;font-weight:bold;}
    </style>
</head>
<body>
    <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion" class="back-btn">← Volver al Listado</a>

    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">FAT</td>
                            <td>
                                Factura #: <?= htmlspecialchars($factura['numero_fiscal']) ?><br>
                                Creada: <?= date('d/m/Y', strtotime($factura['fecha_emision'])) ?><br>
                                Estado: 
                                <span style="background: <?= $factura['estado'] == 'pagada' ? '#dcfce7;color:#166534' : '#fee2e2;color:#991b1b' ?>; padding: 2px 8px; border-radius: 4px; font-size: 0.8em;">
                                    <?= ucfirst($factura['estado']) ?>
                                </span>
                                
                                <!-- Status Updater -->
                                <form action="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion/actualizar/<?= $factura['id'] ?>" method="POST" style="margin-top: 10px;">
                                    <select name="estado_factura" style="padding: 4px; border-radius: 4px; border: 1px solid #ddd;">
                                        <option value="emitida" <?= $factura['estado'] == 'emitida' ? 'selected' : '' ?>>Emitida</option>
                                        <option value="pagada" <?= $factura['estado'] == 'pagada' ? 'selected' : '' ?>>Pagada ✅</option>
                                        <option value="anulada" <?= $factura['estado'] == 'anulada' ? 'selected' : '' ?>>Anulada ❌</option>
                                    </select>
                                    <button type="submit" style="padding: 4px 8px; cursor: pointer; background: #2563eb; color: white; border: none; border-radius: 4px;">Actualizar</button>
                                </form>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                Consultores Chiriquí S.A.<br>
                                Vía Interamericana<br>
                                David, Chiriquí
                            </td>
                            
                            <td>
                                <?= htmlspecialchars($factura['empresa_nombre']) ?><br>
                                RUC: <?= htmlspecialchars($factura['ruc']) ?><br>
                                <?= htmlspecialchars($factura['direccion']) ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="heading">
                <td>Descripcion</td>
                <td>Precio</td>
            </tr>
            
            <?php foreach($detalles as $det): ?>
            <tr class="item">
                <td>Servicios Interacción (<?= $det['cantidad_interacciones'] ?> x B/. <?= $det['tarifa_unitaria'] ?>)</td>
                <td>B/. <?= number_format($det['total_linea'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
            
            <tr class="total">
                <td></td>
                <td>
                   Subtotal: B/. <?= number_format($factura['subtotal'], 2) ?><br>
                   ITBMS (7%): B/. <?= number_format($factura['itbms'], 2) ?><br>
                   Total: B/. <?= number_format($factura['total'], 2) ?>
                </td>
            </tr>
        </table>
        
        <div class="qr">
            <p>Escanea para validar en DGI:</p>
            <img src="<?= $qrUrl ?>" alt="QR Code Factura" width="150" height="150" style="border: 2px solid #ddd; padding: 5px; border-radius: 8px;">
            <p style="font-size: 10px; color: #999;">CUFE: <?= $factura['token_publico'] ?></p>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pagar Factura</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div style="max-width: 500px; margin: 120px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
        
        <div style="margin-bottom: 20px;">
            <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/facturacion" style="text-decoration: none; color: #64748b; font-weight: 600; font-size: 0.9rem;">
                <i class="fas fa-arrow-left"></i> Volver a Facturación
            </a>
        </div>

        <div style="text-align: center; margin-bottom: 30px;">
            <i class="fas fa-secure" style="font-size: 3rem; color: #10b981;"></i>
            <h2 style="margin: 10px 0; color: #1e293b;">Pasarela de Pago Segura</h2>
            <p style="color: #64748b;">Estás pagando la factura <strong><?= $factura['numero_fiscal'] ?></strong></p>
        </div>

        <div style="background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
            <span style="display: block; font-size: 0.9rem; color: #64748b;">Total a Pagar</span>
            <span style="font-size: 2rem; font-weight: 800; color: #2563eb;">B/. <?= number_format($factura['total'], 2) ?></span>
        </div>

        <form action="<?= ENV_APP['BASE_URL'] ?>/pagos/procesar" method="POST" id="paymentForm">
            <input type="hidden" name="factura_id" value="<?= $factura['id'] ?>">

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; color: #334155; margin-bottom: 5px;">Titular de la Tarjeta</label>
                <input type="text" class="form-control" placeholder="Como aparece en la tarjeta" required 
                       style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; color: #334155; margin-bottom: 5px;">Número de Tarjeta</label>
                <div style="position: relative;">
                    <input type="text" class="form-control" placeholder="0000 0000 0000 0000" maxlength="19" required
                           style="width: 100%; padding: 10px 10px 10px 40px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    <i class="far fa-credit-card" style="position: absolute; left: 12px; top: 12px; color: #94a3b8;"></i>
                </div>
            </div>

            <div style="display: flex; gap: 15px; margin-bottom: 25px;">
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 600; color: #334155; margin-bottom: 5px;">Vencimiento</label>
                    <input type="text" placeholder="MM/YY" maxlength="5" required
                           style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 600; color: #334155; margin-bottom: 5px;">CVV</label>
                    <input type="password" placeholder="123" maxlength="3" required
                           style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; padding: 12px; font-size: 1rem; background: #10b981; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 700;">
                <i class="fas fa-lock"></i> Pagar B/. <?= number_format($factura['total'], 2) ?>
            </button>
            
            <p style="text-align: center; font-size: 0.8rem; color: #94a3b8; margin-top: 15px;">
                <i class="fas fa-shield-alt"></i> Transacción encriptada de extremo a extremo.
            </p>
        </form>
    </div>

</body>
</html>

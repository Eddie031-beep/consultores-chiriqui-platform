<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas - Consultora</title>
    <style>
        body{font-family:system-ui;background:#020617;color:#e5e7eb;padding:2rem;}
        .container{max-width:1200px;margin:0 auto;}
        h1{color:#38bdf8;margin-bottom:2rem;}
        .back-btn{display:inline-block;margin-bottom:1rem;color:#94a3b8;text-decoration:none;}
        .back-btn:hover{color:#38bdf8;}
        
        table {width: 100%; border-collapse: collapse; background: #1e293b; border-radius: 12px; overflow: hidden;}
        th, td {padding: 15px; text-align: left; border-bottom: 1px solid #334155;}
        th {background: #0f172a; color: #94a3b8; font-weight: 600;}
        tr:last-child td {border-bottom: none;}
        .amount {color: #4ade80; font-weight: bold;}
    </style>
</head>
<body>
    <div class="container">
        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/dashboard" class="back-btn">← Volver al Panel</a>
        <h1>📊 Estadísticas Detalladas</h1>
        
        <table>
            <thead>
                <tr>
                    <th>Empresa</th>
                    <th>Vistas</th>
                    <th>Aplicaciones</th>
                    <th>Facturación Estimada</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($topEmpresas)): ?>
                    <tr><td colspan="4" style="text-align:center;">No hay datos disponibles.</td></tr>
                <?php else: ?>
                    <?php foreach($topEmpresas as $emp): ?>
                    <tr>
                        <td><?= htmlspecialchars($emp['nombre']) ?></td>
                        <td><?= $emp['vistas'] ?></td>
                        <td><?= $emp['aplicaciones'] ?></td>
                        <td class="amount">B/. <?= $emp['facturacion_estimada'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

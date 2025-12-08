<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas - Consultora</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css?v=<?= time() ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-grid" style="display: block; max-width: 1000px; margin: 0 auto;">
        
        <div class="page-header">
            <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/dashboard" class="back-btn">
                <i class="fas fa-arrow-left"></i> Volver al Panel
            </a>
            <h2>📊 Estadísticas Detalladas</h2>
        </div>

        <div class="table-container">
            <div class="section-header">
                <div class="section-title">Rendimiento por Empresa</div>
                <div style="font-size: 0.85rem; color: #64748b;">Mes Actual</div>
            </div>

            <div class="table-responsive">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th>Empresa</th>
                            <th class="text-right">Vistas</th>
                            <th class="text-right">Aplicaciones</th>
                            <th class="text-right">Interacciones Chat</th>
                            <th class="text-right">Facturación Estimada</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($topEmpresas)): ?>
                            <tr><td colspan="5" style="text-align:center; padding: 2rem; color: #94a3b8;">No hay datos registrados aún.</td></tr>
                        <?php else: ?>
                            <?php foreach($topEmpresas as $emp): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($emp['nombre']) ?></div>
                                    <div style="font-size: 0.8rem; color: #94a3b8;"><?= $emp['sector'] ?? 'General' ?></div>
                                </td>
                                <td align="right" style="color: #64748b;"><?= $emp['vistas'] ?></td>
                                <td align="right" style="color: #64748b;"><?= $emp['aplicaciones'] ?></td>
                                <td align="right" style="color: #64748b;">-</td> <!-- Placeholder if not available -->
                                <td align="right">
                                    <span style="background: #ecfccb; color: #4d7c0f; padding: 2px 8px; border-radius: 4px; font-weight: 600; font-size: 0.85rem;">
                                        B/. <?= $emp['facturacion_estimada'] ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>

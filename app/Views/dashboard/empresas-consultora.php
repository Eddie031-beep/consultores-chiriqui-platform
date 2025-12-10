<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Empresas - Consultora</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css?v=<?= time() ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-grid" style="display: block; max-width: 1200px; margin: 0 auto;">
        
        <div class="page-header">
            <div>
                <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/dashboard" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Volver al Panel
                </a>
                <h2 style="margin-top: 10px;">🏢 Gestión de Empresas</h2>
            </div>
            <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/empresas/crear" class="btn-primary">
                <i class="fas fa-plus"></i> Nueva Empresa
            </a>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th width="30%">Nombre</th>
                            <th width="20%">RUC</th>
                            <th width="20%">Sector</th>
                            <th width="15%">Estado</th>
                            <th width="15%" style="text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($empresas)): ?>
                            <tr><td colspan="5" style="text-align:center; padding: 3rem; color: #94a3b8;">No hay empresas registradas.</td></tr>
                        <?php else: ?>
                            <?php foreach($empresas as $emp): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600; font-size: 1rem; color: #1e293b;"><?= htmlspecialchars($emp['nombre']) ?></div>
                                    <div style="font-size: 0.8rem; color: #94a3b8; margin-top: 2px;">ID: #<?= $emp['id'] ?></div>
                                </td>
                                <td style="font-family: monospace; color: #64748b; font-size: 0.95rem;">
                                    <?= htmlspecialchars($emp['ruc'] . '-' . $emp['dv']) ?>
                                </td>
                                <td><?= htmlspecialchars($emp['sector']) ?></td>
                                <td>
                                    <?php
                                        $statusClass = 'status-active';
                                        if($emp['estado'] === 'inactiva') $statusClass = 'status-inactive';
                                        if($emp['estado'] === 'pendiente') $statusClass = 'status-pending';
                                    ?>
                                    <span class="status-badge <?= $statusClass ?>">
                                        <?= ucfirst($emp['estado']) ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/empresas/<?= $emp['id'] ?>/editar" class="action-icon" title="Editar">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    
                                    <?php if (!empty($emp['contrato_id'])): ?>
                                        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/contratos/<?= $emp['id'] ?>" class="action-icon" title="Ver Contrato">
                                            <i class="fas fa-file-contract"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/contratos/generar/<?= $emp['id'] ?>" class="action-icon" title="Generar Contrato" style="color: #16a34a;" onclick="return confirm('¿Generar contrato comercial para esta empresa?');">
                                            <i class="fas fa-file-signature"></i>
                                        </a>
                                    <?php endif; ?>

                                    <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion/generar?empresa_id=<?= $emp['id'] ?>" class="action-icon" title="Generar Factura" style="color: #2563eb;">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </a>
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

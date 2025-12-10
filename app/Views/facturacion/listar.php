<?php
use App\Helpers\Auth;
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Facturación y Pagos | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-animations.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<?php include __DIR__ . '/../components/navbar.php'; ?>

<div class="dashboard-wrapper">
    
    <div class="page-header animate-slide-up">
        <div>
            <h2><i class="fas fa-file-invoice-dollar" style="color: #2563eb; margin-right: 10px;"></i> Gestión de Facturación</h2>
            <p style="color: #64748b; margin: 5px 0 0;">Genera cobros, revisa estadísticas y administra el historial de pagos.</p>
        </div>
        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/dashboard" class="back-btn">
            <i class="fas fa-arrow-left"></i> Volver al Panel
        </a>
    </div>

    <div class="dashboard-grid animate-slide-up delay-100" style="margin-bottom: 3rem;">
        
        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion/estadisticas" class="glass-card">
            <div class="card-icon-wrapper" style="background: #eff6ff; color: #2563eb;">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div class="card-content">
                <h3>Estadísticas de Consumo</h3>
                <p>Ver métricas de vistas y clicks antes de facturar.</p>
            </div>
            <div class="card-arrow"><i class="fas fa-arrow-right"></i></div>
        </a>

        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion/generar" class="glass-card">
            <div class="card-icon-wrapper" style="background: #f0fdf4; color: #16a34a;">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="card-content">
                <h3>Generar Nueva Factura</h3>
                <p>Crear corte de facturación manual por empresa.</p>
            </div>
            <div class="card-arrow"><i class="fas fa-arrow-right"></i></div>
        </a>

    </div>

    <div class="table-container animate-slide-up delay-200">
        <div class="section-header">
            <div class="section-title">
                <i class="fas fa-history" style="color: #64748b; margin-right: 8px;"></i> Historial de Facturas Emitidas
            </div>
            <form method="GET" style="display: flex; gap: 10px; align-items: center;">
                <select name="mes" class="form-select" style="padding: 0.4rem 2rem 0.4rem 0.8rem; font-size: 0.9rem;" onchange="this.form.submit()">
                    <?php for($m=1; $m<=12; $m++): ?>
                        <option value="<?= $m ?>" <?= ($mes == $m) ? 'selected' : '' ?>>Mes <?= $m ?></option>
                    <?php endfor; ?>
                </select>
                <select name="anio" class="form-select" style="padding: 0.4rem 2rem 0.4rem 0.8rem; font-size: 0.9rem;" onchange="this.form.submit()">
                    <option value="2025" <?= ($anio == 2025) ? 'selected' : '' ?>>2025</option>
                    <option value="2024">2024</option>
                </select>
            </form>
        </div>

        <?php if (empty($facturas)): ?>
            <div style="text-align: center; padding: 4rem; color: #94a3b8;">
                <i class="fas fa-file-invoice" style="font-size: 3rem; margin-bottom: 1rem; color: #e2e8f0;"></i>
                <p>No hay facturas generadas en este periodo.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th>Número Fiscal</th>
                            <th>Empresa Cliente</th>
                            <th>Periodo</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Fecha Emisión</th>
                            <th style="text-align: right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($facturas as $f): ?>
                        <tr>
                            <td style="font-family: monospace; font-weight: 600; color: #475569;">
                                <?= htmlspecialchars($f['numero_fiscal']) ?>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($f['empresa_nombre']) ?></div>
                            </td>
                            <td style="font-size: 0.85rem; color: #64748b;">
                                <?= date('d/m', strtotime($f['periodo_desde'])) ?> - 
                                <?= date('d/m/Y', strtotime($f['periodo_hasta'])) ?>
                            </td>
                            <td style="font-weight: 700; color: #1e293b;">
                                B/. <?= number_format($f['total'], 2) ?>
                            </td>
                            <td>
                                <?php 
                                    $estadoClass = match($f['estado']) {
                                        'pagada' => 'status-active', // Verde
                                        'anulada' => 'status-inactive', // Rojo
                                        'emitida' => 'status-pending', // Amarillo/Naranja
                                        default => ''
                                    };
                                ?>
                                <span class="status-badge <?= $estadoClass ?>">
                                    <?= ucfirst($f['estado']) ?>
                                </span>
                            </td>
                            <td style="font-size: 0.85rem;">
                                <?= date('d/m/Y H:i', strtotime($f['fecha_emision'])) ?>
                            </td>
                            <td style="text-align: right;">
                                <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion/ver/<?= $f['id'] ?>" class="btn-secondary" style="padding: 6px 12px; font-size: 0.8rem;">
                                    <i class="fas fa-eye"></i> Ver Detalle
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</div>

</body>
</html>

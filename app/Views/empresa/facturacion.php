<?php
use App\Helpers\Auth;
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Facturación y Pagos</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-animations.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../components/navbar.php'; ?>

<div class="dashboard-wrapper">
    <div class="page-header animate-slide-up">
        <div>
            <h2><i class="fas fa-file-invoice-dollar" style="color: #2563eb; margin-right: 10px;"></i> Mis Facturas</h2>
            <p style="color: #64748b; margin: 5px 0 0;">Historial de pagos y facturas pendientes generadas por la Consultora.</p>
        </div>
        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/dashboard" class="back-btn">Volver</a>
    </div>

    <div class="table-container animate-slide-up delay-100" style="margin-top: 0;">
        <?php if (empty($facturas)): ?>
            <div style="text-align: center; padding: 4rem; color: #94a3b8;">
                <i class="fas fa-check-circle fa-3x" style="color: #cbd5e1; margin-bottom: 1rem;"></i>
                <p>Estás al día. No hay facturas registradas.</p>
            </div>
        <?php else: ?>
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>Nº Factura</th>
                        <th>Fecha Emisión</th>
                        <th>Concepto / Periodo</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($facturas as $f): 
                        $isPending = $f['estado'] === 'emitida';
                    ?>
                    <tr style="<?= $isPending ? 'background-color: #fff1f2;' : '' ?>">
                        <td style="font-family: monospace; font-weight: 600;"><?= $f['numero_fiscal'] ?></td>
                        <td><?= date('d/m/Y', strtotime($f['fecha_emision'])) ?></td>
                        <td>
                            Consumo de vacantes <br>
                            <span style="font-size: 0.8rem; color: #64748b;">
                                <?= date('d M', strtotime($f['periodo_desde'])) ?> - <?= date('d M', strtotime($f['periodo_hasta'])) ?>
                            </span>
                        </td>
                        <td>
                            <?php if($isPending): ?>
                                <span class="status-badge status-inactive">PENDIENTE PAGO</span>
                            <?php elseif($f['estado'] === 'pagada'): ?>
                                <span class="status-badge status-active">PAGADA</span>
                            <?php else: ?>
                                <span class="status-badge"><?= strtoupper($f['estado']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight: 700; color: #1e293b;">B/. <?= number_format($f['total'], 2) ?></td>
                        <td>
                            <?php if($isPending): ?>
                                <button class="btn-primary" style="background: #16a34a; font-size: 0.85rem;" onclick="alert('Iniciando pasarela de pago...')">
                                    <i class="fas fa-credit-card"></i> Pagar Ahora
                                </button>
                            <?php else: ?>
                                <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion/descargar/<?= $f['id'] ?>" class="btn-secondary" style="font-size: 0.85rem;">
                                    <i class="fas fa-download"></i> PDF
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

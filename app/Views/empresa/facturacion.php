<?php
use App\Helpers\Auth;
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturación | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/style.css">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../components/navbar.php'; ?>

<div class="dashboard-wrapper">
    <div class="page-header">
    <div class="header-left">
        <h2><i class="fas fa-file-invoice-dollar" style="color: #4f46e5; margin-right: 10px;"></i> Facturación</h2>
        <p style="color: #64748b; margin: 5px 0 0;">Historial de facturas y pagos pendientes.</p>
    </div>
    <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/dashboard" class="back-btn"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<div class="table-container" style="margin-top: 0;">
    <?php if (empty($facturas)): ?>
        <div style="text-align: center; padding: 3rem; color: #94a3b8;">
            <i class="fas fa-check-circle" style="font-size: 3rem; margin-bottom: 1rem; color: #cbd5e1;"></i>
            <p>No tienes facturas pendientes ni historial.</p>
        </div>
    <?php else: ?>
        <table class="premium-table">
            <thead>
                <tr>
                    <th>No. Factura</th>
                    <th>Periodo</th>
                    <th>Estado</th>
                    <th>Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($facturas as $f): ?>
                <tr class="<?= $f['estado'] === 'emitida' ? 'highlight-row' : '' ?>">
                    <td>
                        <div style="font-weight: 600; color: #1e293b;"><?= $f['numero_fiscal'] ?></div>
                        <div style="font-size: 0.8rem; color: #64748b;">Emisión: <?= date('d/m/Y', strtotime($f['fecha_emision'])) ?></div>
                    </td>
                    <td>
                        <?= date('d/m', strtotime($f['periodo_desde'])) ?> - <?= date('d/m/Y', strtotime($f['periodo_hasta'])) ?>
                    </td>
                    <td>
                        <span class="status-badge status-<?= $f['estado'] ?>">
                            <?= strtoupper(str_replace('_', ' ', $f['estado'])) ?>
                        </span>
                    </td>
                    <td style="font-weight: 700;">
                        B/. <?= number_format($f['total'], 2) ?>
                    </td>
                    <td>
                        <div style="display: flex; gap: 10px;">
                            <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion/ver/<?= $f['id'] ?>" class="btn-secondary" style="padding: 5px 10px; font-size: 0.85rem;">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            <?php if ($f['estado'] === 'emitida'): ?>
                                <button class="btn-primary" style="padding: 5px 10px; font-size: 0.85rem; background: #16a34a;" onclick="alert('Redireccionando a pasarela de pago...')">
                                    <i class="fas fa-credit-card"></i> Pagar
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<style>
    .highlight-row td {
        background-color: #f0fdf4; /* Light green background for pending invoices */
    }
</style>

</div> <!-- End dashboard-wrapper -->
</body>
</html>

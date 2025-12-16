<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Facturas | Empresa</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <style>
        .dashboard-wrapper { max-width: 1200px; margin: 0 auto; padding: 2rem; padding-top: 100px; }
        .page-header { margin-bottom: 2rem; }
        .page-header h1 { font-size: 1.8rem; color: #1e293b; margin-bottom: 1rem; }
        
        .alert-error { background:#fee2e2; color:#991b1b; padding:15px; border-radius:8px; display:inline-block; border: 1px solid #fecaca; }
        .alert-success { background:#dcfce7; color:#166534; padding:15px; border-radius:8px; display:inline-block; border: 1px solid #bbf7d0; }

        .table-container { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #e2e8f0; }
        .premium-table { width: 100%; border-collapse: collapse; }
        .premium-table th { background: #f8fafc; padding: 1rem; text-align: left; font-weight: 600; color: #64748b; border-bottom: 1px solid #e2e8f0; }
        .premium-table td { padding: 1rem; border-bottom: 1px solid #f1f5f9; color: #334155; }
        .premium-table tr:last-child td { border-bottom: none; }

        .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; }
        .status-emitida { background: #fee2e2; color: #991b1b; }
        .status-pagada { background: #dcfce7; color: #166534; }
        .status-anulada { background: #f1f5f9; color: #64748b; }

        .btn-icon { text-decoration: none; padding: 6px 12px; border-radius: 6px; font-size: 0.85rem; background: #f1f5f9; color: #475569; display: inline-flex; align-items: center; gap: 5px; transition: all 0.2s; margin-right: 5px; }
        .btn-icon:hover { background: #e2e8f0; color: #1e293b; }
        .btn-icon.pay { background: #dbfafe; color: #0284c7; }
        .btn-icon.pay:hover { background: #bae6fd; color: #0369a1; }
    </style>

    <div class="dashboard-wrapper">
        
        <div class="page-header">
            <h1>Facturación y Pagos</h1>
            <?php if(isset($deuda) && $deuda > 0): ?>
                <div class="alert-error">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Saldo Pendiente:</strong> B/. <?= number_format($deuda, 2) ?>
                </div>
            <?php else: ?>
                <div class="alert-success">
                    <i class="fas fa-check-circle"></i> ¡Estás al día con tus pagos!
                </div>
            <?php endif; ?>
        </div>

        <div class="table-container" style="padding: 0; overflow: hidden;">
            <div style="max-height: 500px; overflow-y: auto;">
                <table class="premium-table">
                    <thead style="position: sticky; top: 0; z-index: 10; background: #f8fafc;">
                        <tr>
                            <th style="background: #f8fafc;">No. Fiscal</th>
                            <th style="background: #f8fafc;">Fecha Emisión</th>
                            <th style="background: #f8fafc;">Periodo</th>
                            <th style="background: #f8fafc;">Estado</th>
                            <th style="background: #f8fafc;">Total</th>
                            <th style="background: #f8fafc;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($facturas)): ?>
                            <tr><td colspan="6" style="text-align:center; padding: 2rem; color: #94a3b8;">No hay facturas registradas.</td></tr>
                        <?php else: ?>
                            <?php foreach($facturas as $f): ?>
                            <tr>
                                <td style="font-weight:700;"><?= $f['numero_fiscal'] ?></td>
                                <td><?= date('d/m/Y', strtotime($f['fecha_emision'])) ?></td>
                                <td><?= date('M Y', strtotime($f['periodo_desde'])) ?></td>
                                <td>
                                    <span class="status-badge status-<?= $f['estado'] ?>">
                                        <?= strtoupper($f['estado']) ?>
                                    </span>
                                </td>
                                <td style="font-weight:700;">B/. <?= number_format($f['total'], 2) ?></td>
                                <td>
                                    <a href="<?= ENV_APP['BASE_URL'] ?>/factura/ver/<?= $f['token_publico'] ?>" target="_blank" class="btn-icon">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>

                                    <?php if($f['estado'] === 'emitida'): ?>
                                        <form action="<?= ENV_APP['BASE_URL'] ?>/empresa/facturacion/confirmar" method="POST" style="display:inline;">
                                            <input type="hidden" name="factura_id" value="<?= $f['id'] ?>">
                                            <button type="submit" class="btn-icon mobile-full" style="background: #fff7ed; color: #ea580c; border: 1px solid #ffedd5; cursor: pointer;">
                                                <i class="fas fa-check-double"></i> Confirmar
                                            </button>
                                        </form>
                                    <?php elseif($f['estado'] === 'pendiente'): ?>
                                        <form action="<?= ENV_APP['BASE_URL'] ?>/empresa/facturacion/pagar" method="POST" style="display:inline;" onsubmit="return confirm('¿Confirma el pago de B/. <?= number_format($f['total'], 2) ?>?');">
                                            <input type="hidden" name="factura_id" value="<?= $f['id'] ?>">
                                            <button type="submit" class="btn-icon pay mobile-full" style="border: none; cursor: pointer;">
                                                <i class="fas fa-credit-card"></i> Pagar (+Info)
                                            </button>
                                        </form>
                                    <?php elseif($f['estado'] === 'pagada'): ?>
                                        <span style="font-size: 0.8rem; color: #166534;"><i class="fas fa-check"></i> Pagada</span>
                                    <?php endif; ?>
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

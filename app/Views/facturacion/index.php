<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Facturación</title>
    <style>
        body{font-family:system-ui;background:#020617;color:#e5e7eb;margin:0;padding:2rem;}
        .top{display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;}
        a.btn{display:inline-block;padding:.5rem .9rem;border-radius:.5rem;background:#22c55e;color:#022c22;text-decoration:none;font-weight:600;margin-right:.5rem;}
        a.btn-blue{background:#3b82f6;color:#fff;}
        table{width:100%;border-collapse:collapse;background:#0f172a;border-radius:.75rem;overflow:hidden;}
        th,td{padding:.6rem .75rem;font-size:.9rem;border-bottom:1px solid #1e293b;}
        th{background:#020617;text-align:left;}
        tr:last-child td{border-bottom:none;}
        .badge{padding:.15rem .5rem;border-radius:999px;font-size:.7rem;}
        .emitida{background:#3b82f633;color:#60a5fa;}
        .pagada{background:#16a34a33;color:#4ade80;}
        .anulada{background:#b91c1c33;color:#fca5a5;}
        a{color:#38bdf8;font-size:.85rem;text-decoration:none;}
        .amount{font-weight:600;color:#4ade80;}
    </style>
</head>
<body>
<div class="top">
    <div>
        <h1 style="margin:0;font-size:1.4rem;">📊 Facturación</h1>
        <p style="margin-top:.25rem;font-size:.9rem;color:#9ca3af;">
            Gestión de facturas y peajes
        </p>
    </div>
    <div>
        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion/estadisticas" class="btn btn-blue">Ver estadísticas</a>
        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/dashboard">Volver al dashboard</a>
    </div>
</div>

<?php if (empty($facturas)): ?>
    <p>No hay facturas generadas todavía.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Número Fiscal</th>
                <th>Empresa</th>
                <th>Período</th>
                <th>Subtotal</th>
                <th>ITBMS (7%)</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($facturas as $f): ?>
            <tr>
                <td><strong><?= htmlspecialchars($f['numero_fiscal']) ?></strong></td>
                <td><?= htmlspecialchars($f['empresa_nombre']) ?></td>
                <td style="font-size:.8rem;">
                    <?= date('d/m/Y', strtotime($f['periodo_desde'])) ?> - 
                    <?= date('d/m/Y', strtotime($f['periodo_hasta'])) ?>
                </td>
                <td>B/. <?= number_format($f['subtotal'], 2) ?></td>
                <td>B/. <?= number_format($f['itbms'], 2) ?></td>
                <td class="amount">B/. <?= number_format($f['total'], 2) ?></td>
                <td><span class="badge <?= $f['estado'] ?>"><?= htmlspecialchars($f['estado']) ?></span></td>
                <td><?= date('d/m/Y H:i', strtotime($f['fecha_emision'])) ?></td>
                <td>
                    <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion/ver/<?= htmlspecialchars($f['token_publico']) ?>" target="_blank">Ver</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<p style="margin-top:1.5rem;">
    <a href="<?= ENV_APP['BASE_URL'] ?>/logout" style="color:#f97316;">Cerrar sesión</a>
</p>
</body>
</html>
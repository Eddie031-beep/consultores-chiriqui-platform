<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Empresas</title>
    <style>
        body{font-family:system-ui;background:#020617;color:#e5e7eb;margin:0;padding:2rem;}
        .top{display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;}
        a.btn{display:inline-block;padding:.5rem .9rem;border-radius:.5rem;background:#22c55e;color:#022c22;text-decoration:none;font-weight:600;}
        table{width:100%;border-collapse:collapse;background:#0f172a;border-radius:.75rem;overflow:hidden;}
        th,td{padding:.6rem .75rem;font-size:.9rem;border-bottom:1px solid #1e293b;}
        th{background:#020617;text-align:left;}
        tr:last-child td{border-bottom:none;}
        .badge{padding:.15rem .5rem;border-radius:999px;font-size:.7rem;}
        .publica{background:#3b82f633;color:#60a5fa;}
        .privada{background:#8b5cf633;color:#c084fc;}
        .activa{background:#16a34a33;color:#4ade80;}
        .inactiva{background:#b91c1c33;color:#fca5a5;}
        a{color:#38bdf8;font-size:.85rem;text-decoration:none;margin-right:.5rem;}
    </style>
</head>
<body>
<div class="top">
    <div>
        <h1 style="margin:0;font-size:1.4rem;">Empresas Registradas</h1>
        <p style="margin-top:.25rem;font-size:.9rem;color:#9ca3af;">
            Gestión de empresas públicas y privadas
        </p>
    </div>
    <div>
        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/empresas/nueva" class="btn">+ Nueva empresa</a>
        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/dashboard" style="margin-left:.75rem;">Volver al dashboard</a>
    </div>
</div>

<?php if (empty($empresas)): ?>
    <p>No hay empresas registradas todavía.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>RUC-DV</th>
                <th>Tipo</th>
                <th>Provincia</th>
                <th>Email</th>
                <th>Estado</th>
                <th>Registro</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($empresas as $e): ?>
            <tr>
                <td><strong><?= htmlspecialchars($e['nombre']) ?></strong></td>
                <td><?= htmlspecialchars($e['ruc']) ?>-<?= htmlspecialchars($e['dv']) ?></td>
                <td><span class="badge <?= $e['tipo'] ?>"><?= htmlspecialchars($e['tipo']) ?></span></td>
                <td><?= htmlspecialchars($e['provincia']) ?></td>
                <td><?= htmlspecialchars($e['email_contacto'] ?? '-') ?></td>
                <td><span class="badge <?= $e['estado'] ?>"><?= htmlspecialchars($e['estado']) ?></span></td>
                <td><?= date('d/m/Y', strtotime($e['fecha_registro'])) ?></td>
                <td>
                    <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/empresas/editar?id=<?= (int)$e['id'] ?>">Editar</a>
                    <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/empresas/crear-usuario?id=<?= (int)$e['id'] ?>">+ Usuario</a>
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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis vacantes</title>
    <style>
        body{font-family:system-ui;background:#020617;color:#e5e7eb;margin:0;padding:2rem;}
        .top{display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;}
        a.btn{display:inline-block;padding:.5rem .9rem;border-radius:.5rem;background:#22c55e;color:#022c22;text-decoration:none;font-weight:600;}
        table{width:100%;border-collapse:collapse;background:#0f172a;border-radius:.75rem;overflow:hidden;}
        th,td{padding:.6rem .75rem;font-size:.9rem;border-bottom:1px solid #1e293b;}
        th{background:#020617;text-align:left;}
        tr:last-child td{border-bottom:none;}
        .badge{padding:.15rem .5rem;border-radius:999px;font-size:.7rem;}
        .abierta{background:#16a34a33;color:#4ade80;}
        .cerrada{background:#b91c1c33;color:#fca5a5;}
        form{display:inline;}
        button.link{background:none;border:none;color:#f97316;cursor:pointer;padding:0;font-size:.85rem;}
    </style>
</head>
<body>
<div class="top">
    <div>
        <h1 style="margin:0;font-size:1.4rem;">Vacantes de mi empresa</h1>
        <p style="margin-top:.25rem;font-size:.9rem;color:#9ca3af;">
            Usuario: <?= htmlspecialchars($user['email']) ?>
        </p>
    </div>
    <div>
        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes/nueva" class="btn">+ Nueva vacante</a>
        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/dashboard" style="margin-left:.75rem;color:#38bdf8;">Volver al dashboard</a>
    </div>
</div>

<?php if (empty($vacantes)): ?>
    <p>No tienes vacantes registradas todavía.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Título</th>
                <th>Ubicación</th>
                <th>Modalidad</th>
                <th>Estado</th>
                <th>F. publicación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($vacantes as $v): ?>
            <tr>
                <td><?= htmlspecialchars($v['titulo']) ?></td>
                <td><?= htmlspecialchars($v['ubicacion']) ?></td>
                <td><?= htmlspecialchars($v['modalidad']) ?></td>
                <td>
                    <span class="badge <?= $v['estado'] === 'cerrada' ? 'cerrada' : 'abierta' ?>">
                        <?= htmlspecialchars($v['estado']) ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($v['fecha_publicacion']) ?></td>
                <td>
                    <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes/editar?id=<?= (int)$v['id'] ?>" style="color:#38bdf8;font-size:.85rem;">Editar</a>
                    <?php if ($v['estado'] !== 'cerrada'): ?>
                        <form method="post" action="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes/cerrar" onsubmit="return confirm('¿Cerrar esta vacante?');">
                            <input type="hidden" name="id" value="<?= (int)$v['id'] ?>">
                            <button type="submit" class="link">Cerrar</button>
                        </form>
                    <?php endif; ?>
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

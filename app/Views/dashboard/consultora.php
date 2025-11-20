<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Consultora</title>
</head>
<body style="font-family:system-ui; background:#020617; color:#e5e7eb; padding:2rem;">
    <h1>Dashboard Consultora</h1>
    <p>Bienvenido, <?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?> (<?= htmlspecialchars($user['email']) ?>)</p>

    <ul>
        <li>Gestionar empresas</li>
        <li>Ver estadísticas de interacciones</li>
        <li>Generar facturas de peaje</li>
    </ul>

    <p>
        <a href="<?= ENV_APP['BASE_URL'] ?>/logout" style="color:#f97316;">Cerrar sesión</a>
    </p>
</body>
</html>

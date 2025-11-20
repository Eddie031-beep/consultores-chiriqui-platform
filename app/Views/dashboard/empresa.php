<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Empresa</title>
</head>
<body style="font-family:system-ui; background:#020617; color:#e5e7eb; padding:2rem;">
    <h1>Dashboard Empresa</h1>
    <p>Bienvenido, <?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?> (<?= htmlspecialchars($user['email']) ?>)</p>

    <ul>
        <li>Administrar vacantes de la empresa</li>
        <li>Ver interacciones generadas por el chatbot</li>
        <li>Consultar facturas emitidas</li>
    </ul>

    <p>
        <a href="<?= ENV_APP['BASE_URL'] ?>/logout" style="color:#f97316;">Cerrar sesión</a>
    </p>
</body>
</html>

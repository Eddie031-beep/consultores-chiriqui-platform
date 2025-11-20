<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Consultora</title>
    <style>
        body{font-family:system-ui;background:#020617;color:#e5e7eb;padding:2rem;}
        .header{margin-bottom:2rem;}
        .header h1{margin:0;font-size:1.8rem;color:#38bdf8;}
        .welcome{background:#0f172a;padding:1rem 1.5rem;border-radius:.75rem;margin:1rem 0;border-left:4px solid #3b82f6;}
        .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.5rem;margin-top:2rem;}
        .card{background:#0f172a;padding:1.5rem;border-radius:.75rem;border:1px solid #1e293b;transition:all .2s;text-decoration:none;color:inherit;display:block;}
        .card:hover{border-color:#38bdf8;transform:translateY(-2px);box-shadow:0 10px 30px rgba(56,189,248,.15);}
        .card-icon{font-size:2.5rem;margin-bottom:.75rem;}
        .card-title{font-size:1.2rem;font-weight:600;color:#cbd5e1;margin-bottom:.5rem;}
        .card-desc{font-size:.85rem;color:#9ca3af;line-height:1.5;}
        .logout{display:inline-block;margin-top:2rem;color:#f97316;font-size:.9rem;text-decoration:none;}
        .logout:hover{text-decoration:underline;}
    </style>
</head>
<body>
<div class="header">
    <h1>🏢 Panel de Administración - Consultora</h1>
    <div class="welcome">
        Bienvenido/a, <strong><?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?></strong>
        <br><span style="font-size:.9rem;color:#9ca3af;"><?= htmlspecialchars($user['email']) ?></span>
    </div>
</div>

<div class="grid">
    <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/empresas" class="card">
        <div class="card-icon">🏢</div>
        <div class="card-title">Gestionar Empresas</div>
        <div class="card-desc">Crear, editar y administrar empresas públicas y privadas registradas en la plataforma.</div>
    </a>

    <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion/estadisticas" class="card">
        <div class="card-icon">📊</div>
        <div class="card-title">Estadísticas</div>
        <div class="card-desc">Ver interacciones por empresa, calcular peajes y analizar métricas de uso.</div>
    </a>

    <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion" class="card">
        <div class="card-icon">🧾</div>
        <div class="card-title">Facturación</div>
        <div class="card-desc">Generar facturas fiscales, ver historial de pagos y emitir documentos tributarios.</div>
    </a>

    <a href="<?= ENV_APP['BASE_URL'] ?>/chatbot" class="card">
        <div class="card-icon">🤖</div>
        <div class="card-title">Chatbot Público</div>
        <div class="card-desc">Ver el asistente virtual que usan los candidatos para buscar vacantes.</div>
    </a>

    <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/info" class="card">
        <div class="card-icon">ℹ️</div>
        <div class="card-title">Info Consultora</div>
        <div class="card-desc">Información corporativa, servicios y datos de contacto de la empresa.</div>
    </a>

    <a href="<?= ENV_APP['BASE_URL'] ?>/" class="card">
        <div class="card-icon">🏠</div>
        <div class="card-title">Inicio Público</div>
        <div class="card-desc">Ver la página principal del sitio web que ven los visitantes.</div>
    </a>
</div>

<a href="<?= ENV_APP['BASE_URL'] ?>/logout" class="logout">🚪 Cerrar sesión</a>
</body>
</html>
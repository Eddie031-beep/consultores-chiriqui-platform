<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Consultora - Consultores Chiriquí</title>
    <!-- Premium Styles -->
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css">
    <!-- Animations -->
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-animations.css">
</head>
<body>

    <!-- Header -->
    <header class="dashboard-header">
        <div class="header-title">
            <h1>Panel de Control</h1>
            <div class="header-subtitle">
                Bienvenido de nuevo, <?= htmlspecialchars($user['nombre']) ?>
            </div>
        </div>
        <div class="admin-badge">
            <span style="font-size: 1.2rem;">🛡️</span> Administrador
        </div>
    </header>

    <!-- Main Grid -->
    <div class="dashboard-grid">
        
        <!-- Gestionar Empresas -->
        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/empresas" class="glass-card" style="animation-delay: 0.1s;">
            <div class="card-icon-wrapper">🏢</div>
            <div class="card-content">
                <h3>Gestionar Empresas</h3>
                <p>Administra registros, contratos y usuarios de empresas asociadas.</p>
            </div>
            <div class="card-arrow">➜</div>
        </a>

        <!-- Estadísticas -->
        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion/estadisticas" class="glass-card" style="animation-delay: 0.2s;">
            <div class="card-icon-wrapper">📊</div>
            <div class="card-content">
                <h3>Métricas de Uso</h3>
                <p>Analiza el tráfico, interacciones y rendimiento de vacantes.</p>
            </div>
            <div class="card-arrow">➜</div>
        </a>

        <!-- Facturación -->
        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion" class="glass-card" style="animation-delay: 0.3s;">
            <div class="card-icon-wrapper">🧾</div>
            <div class="card-content">
                <h3>Facturación</h3>
                <p>Genera reportes fiscales y monitorea los ingresos estimados.</p>
            </div>
            <div class="card-arrow">➜</div>
        </a>

        <!-- Chatbot -->
        <a href="<?= ENV_APP['BASE_URL'] ?>/chatbot" class="glass-card" style="animation-delay: 0.4s;">
            <div class="card-icon-wrapper">🤖</div>
            <div class="card-content">
                <h3>Asistente Virtual</h3>
                <p>Supervisa las interacciones del chatbot público en tiempo real.</p>
            </div>
            <div class="card-arrow">➜</div>
        </a>

        <!-- Configuración / Info -->
        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/info" class="glass-card" style="animation-delay: 0.5s;">
            <div class="card-icon-wrapper">⚙️</div>
            <div class="card-content">
                <h3>Configuración</h3>
                <p>Gestiona la información corporativa y ajustes del sistema.</p>
            </div>
            <div class="card-arrow">➜</div>
        </a>

        <!-- Ir al Sitio -->
        <a href="<?= ENV_APP['BASE_URL'] ?>/" class="glass-card" style="animation-delay: 0.6s;">
            <div class="card-icon-wrapper">🌐</div>
            <div class="card-content">
                <h3>Sitio Público</h3>
                <p>Visita la plataforma como la ven los candidatos y usuarios.</p>
            </div>
            <div class="card-arrow">➜</div>
        </a>
    </div>

    <!-- Financial Stats Section -->
    <div class="stats-section">
        <div class="section-header">
            <div class="section-title">
                📈 Rendimiento Financiero (Top 5)
            </div>
            <div style="color: var(--text-muted); font-size: 0.9rem;">
                Actualizado: <?= date('d M Y, H:i') ?>
            </div>
        </div>

        <div class="table-responsive">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th width="40%">Empresa</th>
                        <th width="20%" class="text-right">Vistas</th>
                        <th width="20%" class="text-right">Postulaciones</th>
                        <th width="20%" class="text-right">A Facturar (Est.)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($topEmpresas)): ?>
                        <tr><td colspan="4" style="text-align: center; color: var(--text-muted); padding: 2rem;">No hay actividad registrada este mes.</td></tr>
                    <?php else: ?>
                        <?php foreach($topEmpresas as $stat): ?>
                        <tr>
                            <td>
                                <div class="company-cell">
                                    <div class="company-logo-placeholder">
                                        <?= strtoupper(substr($stat['nombre'], 0, 1)) ?>
                                    </div>
                                    <?= htmlspecialchars($stat['nombre']) ?>
                                </div>
                            </td>
                            <td class="text-right" style="color: #94a3b8;"><?= $stat['vistas'] ?></td>
                            <td class="text-right" style="color: #94a3b8;"><?= $stat['aplicaciones'] ?></td>
                            <td class="text-right">
                                <span class="amount-badge">B/. <?= $stat['facturacion_estimada'] ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <a href="<?= ENV_APP['BASE_URL'] ?>/logout" class="logout-btn">
        🚪 Cerrar Sesión
    </a>

</body>
</html>
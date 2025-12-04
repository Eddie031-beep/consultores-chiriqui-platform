<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Empresa | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

    <!-- NAVBAR PROFESIONAL -->
    <nav class="navbar">
        <div class="container navbar-content">
            <div class="brand-logo">
                Consultores<span>Chiriquí</span>
            </div>
            <div class="nav-links">
                <a href="#" class="nav-item active">Dashboard</a>
                <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes" class="nav-item">Mis Vacantes</a>
                <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/candidatos" class="nav-item">Candidatos</a>
                <a href="<?= ENV_APP['BASE_URL'] ?>/logout" class="nav-item" style="color: var(--danger);">Salir</a>
            </div>
        </div>
    </nav>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="container dashboard-wrapper">
        
        <!-- HEADER DE BIENVENIDA -->
        <div class="welcome-section">
            <div class="welcome-text">
                <h1>Hola, <?= htmlspecialchars($user['nombre'] ?? 'Usuario') ?> 👋</h1>
                <p>Aquí tienes el resumen de actividad de <strong><?= htmlspecialchars($user['empresa_nombre'] ?? 'tu empresa') ?></strong></p>
            </div>
            <div class="company-id-badge">
                ID Empresa: <?= htmlspecialchars($user['empresa_id'] ?? '0') ?>
            </div>
        </div>

        <!-- TARJETAS DE ESTADÍSTICAS (Symmetrical Grid) -->
        <div class="stats-row">
            <!-- Card 1 -->
            <div class="stat-card">
                <div class="stat-icon icon-green">
                    💼
                </div>
                <div class="stat-data">
                    <h3><?= isset($vacantesActivas) ? $vacantesActivas : 0 ?></h3>
                    <p>Vacantes Activas</p>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="stat-card">
                <div class="stat-icon icon-blue">
                    👥
                </div>
                <div class="stat-data">
                    <h3><?= isset($totalCandidatos) ? $totalCandidatos : 0 ?></h3>
                    <p>Candidatos Totales</p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="stat-card">
                <div class="stat-icon icon-orange">
                    💰
                </div>
                <div class="stat-data">
                    <h3>B/. <?= isset($consumoActual) ? number_format($consumoActual, 2) : '0.00' ?></h3>
                    <p>Consumo Actual</p>
                </div>
            </div>
        </div>

        <!-- GRID DE CONTENIDO (2 Columnas: Principal y Lateral) -->
        <div class="content-grid">
            
            <!-- COLUMNA PRINCIPAL (IZQUIERDA) -->
            <main class="main-column">
                
                <!-- Acciones Rápidas -->
                <div class="card-box">
                    <div class="section-header">
                        <h3>⚡ Acciones Rápidas</h3>
                    </div>
                    <div class="actions-grid">
                        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes/crear" class="action-btn">
                            <i style="font-style: normal;">📢</i>
                            <span>Publicar Vacante</span>
                        </a>
                        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/candidatos" class="action-btn">
                            <i style="font-style: normal;">👥</i>
                            <span>Ver Candidatos</span>
                        </a>
                        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/facturacion" class="action-btn">
                            <i style="font-style: normal;">💳</i>
                            <span>Facturación</span>
                        </a>
                    </div>
                </div>

                <!-- Buscador (Placeholder visual) -->
                <div class="card-box">
                    <div class="section-header">
                        <h3>🔍 Buscar en tu panel</h3>
                    </div>
                    <input type="text" placeholder="Escribe el nombre de una vacante o candidato..." 
                           style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: var(--radius); background: var(--bg-body); color: var(--text-main);">
                </div>

            </main>

            <!-- COLUMNA LATERAL (DERECHA) -->
            <aside class="side-column">
                
                <!-- Actividad Reciente -->
                <div class="card-box">
                    <div class="section-header">
                        <h3>🔔 Actividad Reciente</h3>
                    </div>
                    
                    <div class="activity-list">
                        <?php if (empty($actividadReciente)): ?>
                            <div style="text-align: center; padding: 20px; color: var(--text-muted);">
                                <div style="font-size: 2rem; margin-bottom: 10px;">📭</div>
                                <p>No hay actividad nueva</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($actividadReciente as $act): ?>
                                <div class="activity-item">
                                    <div class="user-avatar">👤</div>
                                    <div class="activity-details">
                                        <p><strong><?= htmlspecialchars($act['nombre']) ?></strong> se postuló a <span><?= htmlspecialchars($act['titulo']) ?></span></p>
                                        <span class="activity-time"><?= date('d M, H:i', strtotime($act['fecha_postulacion'])) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($actividadReciente)): ?>
                        <div style="margin-top: 15px; text-align: center;">
                            <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/candidatos" style="font-size: 0.9rem; font-weight: 600;">Ver todo →</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Alerta de Costos -->
                <div class="cost-alert">
                    <h4>⚠️ Información de Costos</h4>
                    <p>Recuerde: Cada interacción (ver detalle, aplicar, chat) genera un costo de peaje automático.</p>
                </div>

            </aside>
        </div>
    </div>

    <!-- THEME TOGGLE -->
    <button class="theme-toggle" id="themeToggle" title="Cambiar Tema">🌙</button>

    <!-- MODAL DE CONTRATO (Mantenido igual funcionalmente, mejorado visualmente por CSS global) -->
    <?php if (isset($contratoAceptado) && !$contratoAceptado): ?>
    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 9999; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
        <div style="background: var(--bg-card); padding: 2.5rem; border-radius: 16px; max-width: 600px; width: 90%; border: 1px solid var(--danger); box-shadow: 0 25px 50px rgba(0,0,0,0.5);">
            <h2 style="color: var(--danger); margin-bottom: 1rem; display:flex; align-items:center; gap:10px;">
                ⚠️ Contrato Digital Requerido
            </h2>
            <p style="margin-bottom: 1.5rem; color: var(--text-main);">
                Para activar su panel y publicar vacantes, debe aceptar los términos de servicio.
            </p>
            
            <div style="background: rgba(220, 53, 69, 0.1); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid rgba(220, 53, 69, 0.2);">
                <strong style="color: var(--danger); display:block; margin-bottom:0.5rem;">Tarifas de Peaje:</strong>
                <ul style="margin-left: 1.5rem; color: var(--text-main);">
                    <li>👁️ Vista: <strong>B/. 0.10</strong></li>
                    <li>👆 Click: <strong>B/. 0.15</strong></li>
                    <li>🤖 Chat: <strong>B/. 0.05</strong></li>
                </ul>
            </div>

            <form method="POST" action="<?= ENV_APP['BASE_URL'] ?>/empresa/aceptar-contrato">
                <label style="display: flex; gap: 0.8rem; align-items: flex-start; margin-bottom: 2rem; cursor: pointer; padding: 1rem; background: var(--bg-body); border-radius: 8px;">
                    <input type="checkbox" required style="margin-top: 4px;">
                    <span style="font-size: 0.95rem; color: var(--text-main);">
                        Acepto las tarifas y condiciones de uso.
                    </span>
                </label>
                <button type="submit" style="width: 100%; padding: 12px; background: var(--danger); color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">
                    ✍️ Firmar y Acceder
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <script>
        // Theme Toggle Logic
        const toggleBtn = document.getElementById('themeToggle');
        const html = document.documentElement;
        
        const savedTheme = localStorage.getItem('theme') || 'light';
        html.setAttribute('data-theme', savedTheme);
        updateIcon(savedTheme);

        toggleBtn.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
        });

        function updateIcon(theme) {
            toggleBtn.textContent = theme === 'light' ? '🌙' : '☀️';
        }
    </script>
</body>
</html>
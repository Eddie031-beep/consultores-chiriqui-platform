<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Empresa | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-animations.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

    <!-- NAVBAR PROFESIONAL -->
    <nav class="navbar animate-fade-in">
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

    <!-- HELPER DE ESPACIADO -->
    <div style="height: 20px;"></div>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="container dashboard-wrapper animate-fade-in">
        
        <!-- HERO WELCOME: ELEGANT GRADIENT HEADER -->
        <div class="hero-welcome animate-slide-up delay-100">
            <div class="welcome-text">
                <h1>Hola, <?= htmlspecialchars($user['nombre'] ?? 'Usuario') ?></h1>
                <p>Bienvenido al hub de reclutamiento de <strong><?= htmlspecialchars($user['empresa_nombre'] ?? 'tu empresa') ?></strong>. Aquí tienes el pulso de tu actividad reciente.</p>
            </div>
            <span style="position: absolute; top: 20px; right: 20px; background: rgba(0,0,0,0.05); padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                ID: <?= htmlspecialchars($user['empresa_id'] ?? '0') ?>
            </span>
        </div>

        <!-- STATS ROW: NEW GRADIENT CARDS -->
        <div class="stats-row">
            <!-- Vacantes Card -->
            <div class="stat-card-v2 card-gradient-blue animate-slide-up delay-200">
                <span class="icon-wrap">💼</span>
                <h3><?= isset($vacantesActivas) ? $vacantesActivas : 0 ?></h3>
                <p>Vacantes Activas</p>
            </div>

            <!-- Candidatos Card -->
            <div class="stat-card-v2 card-gradient-purple animate-slide-up delay-300">
                <span class="icon-wrap">👥</span>
                <h3><?= isset($totalCandidatos) ? $totalCandidatos : 0 ?></h3>
                <p>Candidatos Totales</p>
            </div>

            <!-- Consumo Card -->
            <div class="stat-card-v2 card-gradient-orange animate-slide-up delay-400">
                <span class="icon-wrap">💰</span>
                <h3>B/. <?= isset($consumoActual) ? number_format($consumoActual, 2) : '0.00' ?></h3>
                <p>Consumo del Mes</p>
            </div>
        </div>

        <!-- MAIN GRID LAYOUT -->
        <div class="content-grid" style="margin-top: 30px;">
            
            <!-- LEFT: ACTIONS & TOOLS -->
            <main class="main-column">
                
                <!-- Quick Actions Grid -->
                <div class="glass-card animate-slide-up delay-500" style="padding: 25px;">
                    <div class="section-header">
                        <h3>⚡ Acciones Rápidas</h3>
                    </div>
                    <div class="actions-grid">
                        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes/crear" class="action-card">
                            <i style="font-style: normal;">📢</i>
                            <span>Publicar Vacante</span>
                        </a>
                        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/candidatos" class="action-card">
                            <i style="font-style: normal;">👀</i>
                            <span>Ver Candidatos</span>
                        </a>
                        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/facturacion" class="action-card">
                            <i style="font-style: normal;">💳</i>
                            <span>Ver Facturación</span>
                        </a>
                    </div>
                </div>

                <!-- Search Component -->
                <div class="glass-card animate-slide-up delay-500" style="padding: 25px; margin-top: 25px;">
                    <div class="section-header">
                        <h3>🔍 Búsqueda Rápida</h3>
                    </div>
                    <div style="position: relative;">
                        <input type="text" placeholder="Buscar vacante, candidato, ID..." 
                               style="width: 100%; padding: 15px 20px; padding-left: 45px; border: 1px solid var(--border-color); border-radius: 12px; background: var(--bg-body); color: var(--text-main); font-size: 1rem;">
                        <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); opacity: 0.5;">🔍</span>
                    </div>
                </div>

            </main>

            <!-- RIGHT: ACTIVITY & NOTICES -->
            <aside class="side-column">
                
                <!-- Activity Feed -->
                <div class="glass-card animate-slide-in-right delay-400" style="padding: 25px;">
                    <div class="section-header">
                        <h3>🔔 Última Actividad</h3>
                    </div>
                    
                    <div class="activity-list">
                        <?php if (empty($actividadReciente)): ?>
                            <div style="text-align: center; padding: 30px; color: var(--text-muted);">
                                <div style="font-size: 2.5rem; opacity: 0.5; margin-bottom: 10px;">💤</div>
                                <p>Sin actividad reciente</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($actividadReciente as $act): ?>
                                <div class="activity-feed-item">
                                    <div class="activity-avatar">👤</div>
                                    <div class="activity-details">
                                        <p><strong><?= htmlspecialchars($act['nombre']) ?></strong> aplicó a <strong style="color: var(--primary-color);"><?= htmlspecialchars($act['titulo']) ?></strong></p>
                                        <span class="activity-time" style="font-size: 0.8rem; color: var(--text-light); display: flex; align-items: center; gap: 5px;">
                                            ⏱️ <?= date('d M, h:i A', strtotime($act['fecha_postulacion'])) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/candidatos" style="display: block; text-align: center; margin-top: 15px; font-weight: 600; font-size: 0.9rem;">Ver todo el historial →</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Cost Info -->
                <div class="cost-alert animate-fade-in delay-500" style="background: rgba(245, 158, 11, 0.1); border-left: 4px solid #f59e0b; padding: 15px; border-radius: 8px;">
                    <h4 style="color: #d97706; margin-bottom: 5px; font-size: 0.95rem;">⚠️ Control de Costos</h4>
                    <p style="font-size: 0.85rem; opacity: 0.8;">Recuerde que cada interacción genera un micro-peaje. Revise su facturación periódicamente.</p>
                </div>

            </aside>
        </div>
    </div>

    <!-- ELEGANT THEME TOGGLE (Floating Pill) -->
    <button class="theme-toggle" id="themeToggle" title="Modo Oscuro/Claro" 
            style="position: fixed; bottom: 30px; right: 30px; width: 55px; height: 55px; border-radius: 50%; background: var(--text-main); color: var(--bg-body); border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.2); cursor: pointer; display: grid; place-items: center; z-index: 1000; font-size: 1.5rem; transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
        🌙
    </button>

    <!-- MODAL DE CONTRATO (Estética Mejorada) -->
    <?php if (isset($contratoAceptado) && !$contratoAceptado): ?>
    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
        <div class="glass-card" style="padding: 3rem; max-width: 550px; width: 90%; border-top: 5px solid var(--danger);">
            <h2 style="color: var(--danger); margin-bottom: 1rem; display:flex; align-items:center; gap:10px; font-size: 1.8rem;">
                📜 Firma Requerida
            </h2>
            <p style="margin-bottom: 2rem; color: var(--text-main); font-size: 1.1rem; line-height: 1.5;">
                Para comenzar a reclutar en <strong>Consultores Chiriquí</strong>, necesitamos su conformidad con las tarifas vigentes.
            </p>
            
            <div style="background: rgba(220, 53, 69, 0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
                <strong style="color: var(--danger); display:block; margin-bottom:1rem; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">Tarifario Actual:</strong>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; text-align: center;">
                    <div style="background: var(--bg-card); padding: 10px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.05);">
                        <div style="font-size: 1.5rem;">👁️</div>
                        <div style="font-weight: bold; color: var(--text-main);">B/. 0.10</div>
                        <div style="font-size: 0.75rem;">Vista</div>
                    </div>
                    <div style="background: var(--bg-card); padding: 10px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.05);">
                        <div style="font-size: 1.5rem;">👆</div>
                        <div style="font-weight: bold; color: var(--text-main);">B/. 0.15</div>
                        <div style="font-size: 0.75rem;">Click</div>
                    </div>
                    <div style="background: var(--bg-card); padding: 10px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.05);">
                        <div style="font-size: 1.5rem;">🤖</div>
                        <div style="font-weight: bold; color: var(--text-main);">B/. 0.05</div>
                        <div style="font-size: 0.75rem;">AI Chat</div>
                    </div>
                </div>
            </div>

            <form method="POST" action="<?= ENV_APP['BASE_URL'] ?>/empresa/aceptar-contrato">
                <label style="display: flex; gap: 1rem; align-items: center; margin-bottom: 2rem; cursor: pointer;">
                    <input type="checkbox" required style="width: 20px; height: 20px; accent-color: var(--primary-color);">
                    <span style="font-size: 1rem; color: var(--text-main);">
                        He leído y acepto los términos de servicio.
                    </span>
                </label>
                <button type="submit" style="width: 100%; padding: 15px; background: var(--text-main); color: var(--bg-body); border: none; border-radius: 12px; font-weight: 800; font-size: 1.1rem; cursor: pointer; transition: opacity 0.2s;">
                    ✍️ FIRMAR CONTRATO DIGITAL
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <script>
        // Theme Toggle Logic
        const toggleBtn = document.getElementById('themeToggle');
        const html = document.documentElement;
        
        // Check saved theme
        const savedTheme = localStorage.getItem('theme') || 'light';
        html.setAttribute('data-theme', savedTheme);
        updateIcon(savedTheme);

        toggleBtn.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            // Add rotation animation
            toggleBtn.style.transform = 'rotate(360deg)';
            setTimeout(() => toggleBtn.style.transform = 'none', 300);

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
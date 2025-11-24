<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Empresa | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* ===== WELCOME BANNER ===== */
        .welcome-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 2.5rem;
            color: white;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }

        .welcome-content {
            position: relative;
            z-index: 1;
        }

        .welcome-banner h1 {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
            color: white !important;
        }

        .welcome-banner p {
            font-size: 1.1rem;
            opacity: 0.95;
            color: rgba(255,255,255,0.9) !important;
        }

        .welcome-meta {
            position: relative;
            z-index: 1;
            text-align: right;
        }

        .empresa-badge {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,0.3);
        }

        /* ===== STATS GRID ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: var(--bg-card);
            padding: 2rem;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 1.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--stat-color, #667eea);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px var(--shadow-color);
            border-color: var(--stat-color, #667eea);
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            flex-shrink: 0;
            background: var(--stat-bg, rgba(102, 126, 234, 0.1));
            position: relative;
        }

        .stat-icon::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 16px;
            background: var(--stat-color, #667eea);
            opacity: 0.1;
        }

        .stat-info {
            flex: 1;
        }

        .stat-info h3 {
            font-size: 2.5rem;
            margin: 0 0 0.25rem 0;
            color: var(--text-heading);
            font-weight: 700;
            line-height: 1;
        }

        .stat-info p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* Colores específicos por stat */
        .stat-card.vacantes {
            --stat-color: #10b981;
            --stat-bg: rgba(16, 185, 129, 0.1);
        }

        .stat-card.candidatos {
            --stat-color: #667eea;
            --stat-bg: rgba(102, 126, 234, 0.1);
        }

        .stat-card.consumo {
            --stat-color: #f59e0b;
            --stat-bg: rgba(245, 158, 11, 0.1);
        }

        /* ===== MAIN GRID ===== */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 2rem;
        }

        /* ===== SEARCH BAR ===== */
        .search-container {
            margin-bottom: 2rem;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 1.2rem 1.2rem 1.2rem 3.5rem;
            border-radius: 16px;
            border: 2px solid var(--border-color);
            background: var(--bg-card);
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.3rem;
            color: var(--text-secondary);
        }

        /* ===== QUICK ACTIONS ===== */
        .section-header {
            margin-bottom: 1.5rem;
        }

        .section-header h3 {
            font-size: 1.4rem;
            color: var(--text-heading);
            font-weight: 700;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .action-btn {
            background: var(--bg-card);
            border: 2px solid var(--border-color);
            padding: 1.5rem;
            border-radius: 16px;
            text-align: center;
            text-decoration: none;
            color: var(--text-heading);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            position: relative;
            overflow: hidden;
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .action-btn:hover {
            transform: translateY(-4px);
            border-color: #667eea;
            box-shadow: 0 8px 30px rgba(102, 126, 234, 0.2);
        }

        .action-btn:hover::before {
            opacity: 0.05;
        }

        .action-btn-icon {
            font-size: 2.5rem;
            position: relative;
            z-index: 1;
        }

        .action-btn-text {
            font-weight: 600;
            font-size: 0.95rem;
            position: relative;
            z-index: 1;
        }

        /* ===== ALERT BOX ===== */
        .alert-box {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.05) 100%);
            border: 2px solid rgba(239, 68, 68, 0.2);
            padding: 1.5rem;
            border-radius: 16px;
            margin-top: 2rem;
        }

        .alert-box strong {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
            color: #dc2626;
            font-size: 1rem;
        }

        .alert-box p {
            color: var(--text-secondary) !important;
            font-size: 0.9rem;
            line-height: 1.6;
            margin: 0;
        }

        /* ===== ACTIVITY PANEL ===== */
        .activity-panel {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            padding: 2rem;
            height: fit-content;
        }

        .activity-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .activity-header h3 {
            font-size: 1.3rem;
            color: var(--text-heading);
            font-weight: 700;
        }

        .activity-badge {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .activity-item {
            display: flex;
            gap: 1rem;
            padding: 1.25rem 0;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.2s ease;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-item:hover {
            background: rgba(102, 126, 234, 0.03);
            margin: 0 -1rem;
            padding-left: 1rem;
            padding-right: 1rem;
            border-radius: 8px;
        }

        .activity-avatar {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .activity-content {
            flex: 1;
            min-width: 0;
        }

        .activity-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--text-heading);
            margin-bottom: 0.25rem;
        }

        .activity-action {
            font-size: 0.85rem;
            color: var(--text-secondary) !important;
            margin-bottom: 0.25rem;
        }

        .activity-action span {
            color: #667eea !important;
            font-weight: 600;
        }

        .activity-time {
            font-size: 0.75rem;
            color: var(--text-secondary) !important;
        }

        .activity-footer {
            margin-top: 1.5rem;
            text-align: center;
        }

        .activity-footer a {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            background: rgba(102, 126, 234, 0.1);
            transition: all 0.3s ease;
        }

        .activity-footer a:hover {
            background: rgba(102, 126, 234, 0.2);
            transform: translateX(4px);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-secondary);
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1200px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }

            .welcome-banner {
                flex-direction: column;
                text-align: center;
                padding: 2rem 1.5rem;
            }

            .welcome-banner h1 {
                font-size: 1.75rem;
            }

            .welcome-meta {
                text-align: center;
                margin-top: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-card {
                padding: 1.5rem;
            }

            .quick-actions {
                grid-template-columns: 1fr;
            }
        }

        /* ===== DARK MODE IMPROVEMENTS ===== */
        [data-theme="dark"] .welcome-banner {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        [data-theme="dark"] .stat-card:hover {
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
        }

        [data-theme="dark"] .activity-badge {
            background: rgba(129, 140, 248, 0.2);
            color: #a5b4fc;
        }

        [data-theme="dark"] .alert-box {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(220, 38, 38, 0.1) 100%);
            border-color: rgba(239, 68, 68, 0.3);
        }

        [data-theme="dark"] .alert-box strong {
            color: #fca5a5;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="dashboard-container">
        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <div class="welcome-content">
                <h1>👋 Hola, <?= htmlspecialchars($user['nombre'] ?? 'Usuario') ?></h1>
                <p>Gestiona el talento de <strong><?= htmlspecialchars($user['empresa_nombre'] ?? 'tu empresa') ?></strong></p>
            </div>
            <div class="welcome-meta">
                <div class="empresa-badge">
                    ID: <?= htmlspecialchars($user['empresa_id'] ?? '0') ?>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card vacantes">
                <div class="stat-icon">💼</div>
                <div class="stat-info">
                    <h3><?= isset($vacantesActivas) ? $vacantesActivas : 0 ?></h3>
                    <p>Vacantes Activas</p>
                </div>
            </div>

            <div class="stat-card candidatos">
                <div class="stat-icon">👥</div>
                <div class="stat-info">
                    <h3><?= isset($totalCandidatos) ? $totalCandidatos : 0 ?></h3>
                    <p>Candidatos Totales</p>
                </div>
            </div>

            <div class="stat-card consumo">
                <div class="stat-icon">💰</div>
                <div class="stat-info">
                    <h3>B/. <?= isset($consumoActual) ? number_format($consumoActual, 2) : '0.00' ?></h3>
                    <p>Consumo del Mes (Peaje)</p>
                </div>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="main-grid">
            <!-- Left Column -->
            <div>
                <!-- Search Bar -->
                <div class="search-container">
                    <span class="search-icon">🔍</span>
                    <input 
                        type="text" 
                        class="search-input" 
                        placeholder="Buscar vacantes, candidatos por nombre..."
                        id="globalSearch"
                    >
                </div>

                <!-- Quick Actions -->
                <div class="section-header">
                    <h3>⚡ Acciones Rápidas</h3>
                </div>
                <div class="quick-actions">
                    <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes/crear" class="action-btn">
                        <span class="action-btn-icon">📢</span>
                        <span class="action-btn-text">Publicar Vacante</span>
                    </a>
                    <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/candidatos" class="action-btn">
                        <span class="action-btn-icon">👥</span>
                        <span class="action-btn-text">Ver Candidatos</span>
                    </a>
                    <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/facturacion" class="action-btn">
                        <span class="action-btn-icon">💳</span>
                        <span class="action-btn-text">Facturación</span>
                    </a>
                </div>

                <!-- Alert Box -->
                <div class="alert-box">
                    <strong>
                        <span>⚠️</span>
                        <span>Información de Costos</span>
                    </strong>
                    <p>Recuerde: Cada interacción de un candidato (ver detalle, aplicar o consultar en Chatbot) genera un costo de peaje automático que se reflejará en su facturación mensual.</p>
                </div>
            </div>

            <!-- Right Column - Activity Panel -->
            <div>
                <div class="activity-panel">
                    <div class="activity-header">
                        <h3>🔔 Actividad Reciente</h3>
                        <?php if (!empty($actividadReciente)): ?>
                            <span class="activity-badge"><?= count($actividadReciente) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="activity-list">
                        <?php if (empty($actividadReciente)): ?>
                            <div class="empty-state">
                                <div class="empty-state-icon">📭</div>
                                <p>No hay actividad reciente</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($actividadReciente as $act): ?>
                                <div class="activity-item">
                                    <div class="activity-avatar">👤</div>
                                    <div class="activity-content">
                                        <div class="activity-name">
                                            <?= htmlspecialchars($act['nombre'] . ' ' . $act['apellido']) ?>
                                        </div>
                                        <div class="activity-action">
                                            Se postuló a: <span><?= htmlspecialchars($act['titulo']) ?></span>
                                        </div>
                                        <div class="activity-time">
                                            <?= date('d M, H:i', strtotime($act['fecha_postulacion'])) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($actividadReciente)): ?>
                        <div class="activity-footer">
                            <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/candidatos">
                                Ver toda la actividad
                                <span>→</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Simple search functionality
        document.getElementById('globalSearch')?.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            // Aquí podrías implementar búsqueda en tiempo real
            console.log('Buscando:', searchTerm);
        });

        // Animación de entrada para las cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card, .action-btn');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>
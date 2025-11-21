<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Empresa | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Welcome Section */
        .welcome-banner {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 16px;
            padding: 2rem;
            color: white;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 25px rgba(118, 75, 162, 0.2);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--bg-card);
            padding: 1.5rem;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform 0.3s;
        }

        .stat-card:hover { transform: translateY(-5px); }

        .stat-icon {
            width: 50px; height: 50px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
        }

        .stat-info h3 { font-size: 2rem; margin: 0; color: var(--text-heading); }
        .stat-info p { margin: 0; color: var(--text-secondary); font-size: 0.9rem; }

        /* Main Content Grid */
        .main-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .action-btn {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            text-decoration: none;
            color: var(--text-heading);
            transition: all 0.3s;
            display: flex; flex-direction: column; align-items: center; gap: 0.5rem;
        }

        .action-btn:hover {
            border-color: var(--primary);
            background: rgba(102, 126, 234, 0.05);
        }

        .action-btn i { font-size: 1.5rem; color: var(--primary); }

        /* Search Bar */
        .search-container {
            margin-bottom: 2rem;
            position: relative;
        }
        .search-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            background: var(--bg-card);
            color: var(--text-primary);
            font-size: 1rem;
        }
        .search-icon {
            position: absolute; left: 1rem; top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }

        /* Activity Panel */
        .activity-panel {
            background: var(--bg-card);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            padding: 1.5rem;
        }

        .activity-item {
            display: flex;
            align-items: start;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        .activity-item:last-child { border-bottom: none; }
        
        .activity-time {
            font-size: 0.8rem;
            color: var(--text-secondary);
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .main-grid { grid-template-columns: 1fr; }
            .quick-actions { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="dashboard-container">
        <div class="welcome-banner">
            <div>
                <h1 style="color: white; margin:0;">Hola, <?= htmlspecialchars($user['nombre']) ?></h1>
                <p style="margin: 0.5rem 0 0; opacity: 0.9;">Gestiona el talento de <strong><?= htmlspecialchars($user['empresa_nombre']) ?></strong></p>
            </div>
            <div style="text-align: right;">
                <span style="font-size: 0.9rem; opacity: 0.8;">ID Empresa: <?= $user['empresa_id'] ?></span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">briefcase</div>
                <div class="stat-info">
                    <h3><?= $vacantesActivas ?></h3>
                    <p>Vacantes Activas</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(102, 126, 234, 0.1); color: var(--primary);">users</div>
                <div class="stat-info">
                    <h3><?= $totalCandidatos ?></h3>
                    <p>Candidatos Totales</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);">receipt</div>
                <div class="stat-info">
                    <h3>B/. <?= number_format($consumoActual, 2) ?></h3>
                    <p>Consumo del Mes (Peaje)</p>
                </div>
            </div>
        </div>

        <div class="main-grid">
            <div>
                <div class="search-container">
                    <span class="search-icon">🔍</span>
                    <input type="text" class="search-input" placeholder="Buscar vacantes, candidatos por nombre...">
                </div>

                <h3 style="margin-bottom: 1rem; color: var(--text-heading);">Acciones Rápidas</h3>
                <div class="quick-actions">
                    <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes/crear" class="action-btn">
                        <i>📢</i>
                        <span>Publicar Vacante</span>
                    </a>
                    <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/candidatos" class="action-btn">
                        <i>👥</i>
                        <span>Revisar Postulaciones</span>
                    </a>
                    <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/facturacion" class="action-btn">
                        <i>💳</i>
                        <span>Facturación</span>
                    </a>
                </div>

                <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); padding: 1rem; border-radius: 8px; color: var(--danger); font-size: 0.9rem;">
                    <strong style="display: block; margin-bottom: 0.5rem;">⚠️ Información de Costos</strong>
                    Recuerde: Cada interacción de un candidato (ver detalle, aplicar o consultar en Chatbot) genera un costo de peaje automático que se reflejará en su facturación mensual.
                </div>
            </div>

            <div>
                <h3 style="margin-bottom: 1rem; color: var(--text-heading);">Actividad Reciente</h3>
                <div class="activity-panel">
                    <?php if (empty($actividadReciente)): ?>
                        <p style="text-align: center; padding: 1rem; color: var(--text-secondary);">No hay actividad reciente.</p>
                    <?php else: ?>
                        <?php foreach ($actividadReciente as $act): ?>
                            <div class="activity-item">
                                <div style="background: var(--bg-primary); padding: 0.5rem; border-radius: 50%;">👤</div>
                                <div>
                                    <div style="font-weight: 600; font-size: 0.9rem;">
                                        <?= htmlspecialchars($act['nombre'] . ' ' . $act['apellido']) ?>
                                    </div>
                                    <div style="font-size: 0.85rem; color: var(--text-secondary);">
                                        Se postuló a: <span style="color: var(--primary);"><?= htmlspecialchars($act['titulo']) ?></span>
                                    </div>
                                    <div class="activity-time">
                                        <?= date('d M, H:i', strtotime($act['fecha_postulacion'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/candidatos" style="display: block; text-align: center; margin-top: 1rem; font-size: 0.9rem; color: var(--primary);">
                        Ver toda la actividad →
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
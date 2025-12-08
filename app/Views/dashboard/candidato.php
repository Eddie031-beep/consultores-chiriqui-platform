<?php
use App\Helpers\Auth;

$user = Auth::user();
$postulaciones = $postulaciones ?? [];
// Filter strictly for correct count if not already done in controller
$acceptedCount = count(array_filter($postulaciones, fn($p) => $p['estado'] === 'aceptado'));
$pendingCount = count(array_filter($postulaciones, fn($p) => $p['estado'] === 'pendiente'));
?>

<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Candidato | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem 1rem; }
        
        /* Hero Section - Elegant Dark Theme */
        .welcome-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); /* Dark Slate Gradient */
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 3rem 2rem;
            color: white;
            margin-bottom: 2rem;
            text-align: center;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.3);
            position: relative;
            overflow: hidden;
        }

        .welcome-header::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle at top right, rgba(99, 102, 241, 0.15), transparent 40%);
            opacity: 0.6;
        }

        .welcome-title { font-size: 2.5rem; font-weight: 800; margin-bottom: 0.5rem; position: relative; color: #f8fafc; }
        .welcome-subtitle { font-size: 1.1rem; opacity: 0.9; margin-bottom: 1.5rem; position: relative; color: #cbd5e1; }

        .btn-edit-profile {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px 24px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            position: relative;
        }
        .btn-edit-profile:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px -1px var(--shadow-color);
            text-align: center;
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-3px); }

        .stat-value { font-size: 3rem; font-weight: 800; color: #4f46e5; line-height: 1; margin-bottom: 0.5rem; }
        .stat-label { color: var(--text-secondary); font-weight: 500; font-size: 0.95rem; }

        /* Recent Applications Section */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        .section-title { font-size: 1.5rem; font-weight: 700; color: var(--text-heading); display: flex; align-items: center; gap: 10px; }
        
        .recent-card {
            background: var(--bg-card);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px -1px var(--shadow-color);
            overflow: hidden;
        }

        .table-responsive { width: 100%; overflow-x: auto; }
        .dashboard-table { width: 100%; border-collapse: collapse; min-width: 600px; }
        
        .dashboard-table th {
            text-align: left;
            padding: 1rem 1.5rem;
            background: var(--bg-secondary);
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            border-bottom: 1px solid var(--border-color);
        }

        .dashboard-table td {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
            vertical-align: middle;
        }
        .dashboard-table tr:last-child td { border-bottom: none; }
        .dashboard-table tr:hover { background-color: rgba(125, 125, 125, 0.05); }

        .company-name { font-weight: 600; color: #4f46e5; display: block; margin-bottom: 2px; }
        .job-role { font-weight: 700; color: var(--text-heading); font-size: 1rem; }
        .job-location { font-size: 0.85rem; color: var(--text-secondary); }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35em 0.85em;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .status-badge.pendiente { background: rgba(245, 158, 11, 0.1); color: #b45309; }
        .status-badge.revisado { background: rgba(37, 99, 235, 0.1); color: #1d4ed8; }
        .status-badge.aceptado { background: rgba(16, 185, 129, 0.1); color: #047857; }
        .status-badge.rechazado { background: rgba(239, 68, 68, 0.1); color: #b91c1c; }

        .btn-view-all {
            color: #4f46e5;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.95rem;
        }
        .btn-view-all:hover { text-decoration: underline; }

        .empty-dash { text-align: center; padding: 3rem; color: var(--text-secondary); }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="container">
        
        <!-- Welcome Hero -->
        <div class="welcome-header">
            <h1 class="welcome-title">👋 ¡Hola, <?= htmlspecialchars($user['nombre']) ?>!</h1>
            <p class="welcome-subtitle">Aquí tienes un resumen de tu actividad reciente</p>
            <a href="<?= ENV_APP['BASE_URL'] ?>/candidato/editar-perfil" class="btn-edit-profile">
                <i class="fas fa-user-edit"></i> Editar Perfil
            </a>
        </div>

        <!-- KPI Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= count($postulaciones) ?></div>
                <div class="stat-label">Postulaciones Totales</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #10b981;"><?= $acceptedCount ?></div>
                <div class="stat-label">Aceptadas</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #f59e0b;"><?= $pendingCount ?></div>
                <div class="stat-label">En Revisión</div>
            </div>
        </div>

        <!-- Recent Applications -->
        <div class="section-header">
            <div class="section-title">
                <i class="fas fa-history" style="color: #4f46e5;"></i> Postulaciones Recientes
            </div>
            <a href="<?= ENV_APP['BASE_URL'] ?>/candidato/postulaciones" class="btn-view-all">
                Ver todas <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="recent-card">
            <?php if (empty($postulaciones)): ?>
                <div class="empty-dash">
                    <i class="fas fa-folder-open fa-3x mb-3" style="opacity:0.3"></i>
                    <p>Aún no te has postulado a ninguna vacante.</p>
                    <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes" class="btn btn-primary mt-2">Buscar Empleo</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Empresa / Vacante</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Show only top 5 -->
                            <?php foreach (array_slice($postulaciones, 0, 5) as $post): ?>
                            <tr>
                                <td>
                                    <span class="company-name"><?= htmlspecialchars($post['empresa_nombre']) ?></span>
                                    <div class="job-role"><?= htmlspecialchars($post['titulo']) ?></div>
                                    <div class="job-location">
                                        <i class="fas fa-map-marker-alt" style="font-size:0.8em"></i> <?= htmlspecialchars($post['ubicacion'] ?? 'Panamá') ?>
                                    </div>
                                </td>
                                <td>
                                    <?= date('d/m/Y', strtotime($post['fecha_postulacion'])) ?>
                                    <br>
                                    <small class="text-muted"><?= date('H:i', strtotime($post['fecha_postulacion'])) ?></small>
                                </td>
                                <td>
                                    <?php if ($post['estado'] === 'pendiente'): ?>
                                        <form action="<?= ENV_APP['BASE_URL'] ?>/candidato/cancelar-postulacion" method="POST" onsubmit="return confirm('¿Cancelar postulación?');" style="display:inline; float:right;">
                                            <input type="hidden" name="vacante_id" value="<?= $post['vacante_id'] ?>">
                                            <input type="hidden" name="redirect" value="/candidato/dashboard">
                                            <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer;" title="Cancelar">
                                                <i class="fas fa-times-circle"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <span class="status-badge <?= $post['estado'] ?>">
                                        <?= ucfirst($post['estado']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Mis Postulaciones | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem 1rem; }
        
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .page-title {
            font-size: 1.8rem;
            color: var(--text-heading);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-back {
            color: var(--text-secondary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
            transition: color 0.2s;
        }
        .btn-back:hover { color: #2563eb; }

        /* Table Card */
        .table-card {
            background: var(--bg-card);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .styled-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px; /* Force scroll on small screens */
        }

        .styled-table th {
            text-align: left;
            padding: 1rem 1.5rem;
            background: var(--bg-secondary);
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border-color);
        }

        .styled-table td {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
            vertical-align: middle;
        }

        .styled-table tr:last-child td { border-bottom: none; }
        .styled-table tr:hover { background-color: rgba(0,0,0,0.01); }

        /* Columns */
        .col-empresa { font-weight: 600; color: #2563eb; }
        .col-vacante { font-weight: 600; font-size: 1.05rem; display: block; margin-bottom: 4px; }
        .col-meta { font-size: 0.85rem; color: var(--text-secondary); display: flex; align-items: center; gap: 5px; }
        
        /* Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35em 0.85em;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: capitalize;
        }
        .status-badge.pendiente { background: #fffbeb; color: #b45309; border: 1px solid #fcd34d; }
        .status-badge.revisado { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .status-badge.aceptado { background: #ecfdf5; color: #047857; border: 1px solid #6ee7b7; }
        .status-badge.rechazado { background: #fef2f2; color: #b91c1c; border: 1px solid #fca5a5; }

        /* Action Button */
        .btn-view {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 16px;
            background: white;
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s;
            white-space: nowrap; /* Prevent breaking */
        }
        .btn-view:hover {
            border-color: #2563eb;
            color: #2563eb;
            background: #f0f7ff;
            transform: translateY(-1px);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-secondary);
        }
        .empty-icon { font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; }

    </style>
</head>
<body>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-clipboard-list" style="color: #2563eb;"></i> 
                Mis Postulaciones
            </h1>
            <a href="<?= ENV_APP['BASE_URL'] ?>/candidato/dashboard" class="btn-back">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
        </div>

        <div class="table-card">
            <?php if (empty($postulaciones)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📭</div>
                    <h3>No tienes postulaciones activas</h3>
                    <p style="margin-bottom: 20px;">Explora las vacantes disponibles y postúlate a tu próximo empleo.</p>
                    <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes" class="btn-view" style="background: #2563eb; color: white; border:none;">
                        Buscar Empleos
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th width="25%">Empresa</th>
                                <th width="30%">Vacante / Ubicación</th>
                                <th>Fecha</th>
                                <th>Modalidad</th>
                                <th>Estado</th>
                                <th style="text-align: right;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($postulaciones as $post): ?>
                                <tr>
                                    <td>
                                        <div class="col-empresa">
                                            <i class="fas fa-building" style="margin-right: 5px; opacity: 0.7;"></i>
                                            <?= htmlspecialchars($post['empresa_nombre']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="col-vacante"><?= htmlspecialchars($post['titulo']) ?></span>
                                        <div class="col-meta">
                                            <i class="fas fa-map-marker-alt"></i> 
                                            <?= htmlspecialchars($post['ubicacion'] ?? 'Panamá') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="col-meta">
                                            <i class="far fa-calendar-alt"></i>
                                            <?= date('d/m/Y', strtotime($post['fecha_postulacion'])) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.9rem; text-transform: capitalize;">
                                            <?= htmlspecialchars($post['modalidad'] ?? 'Presencial') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= $post['estado'] ?>">
                                            <?= ucfirst($post['estado']) ?>
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        <div style="display: flex; justify-content: flex-end; gap: 5px;">
                                            <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes/<?= $post['slug'] ?>" class="btn-view" target="_blank" title="Ver Vacante">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($post['estado'] === 'pendiente'): ?>
                                                <form action="<?= ENV_APP['BASE_URL'] ?>/candidato/cancelar-postulacion" method="POST" onsubmit="return confirm('¿Estás seguro de cancelar esta postulación?');" style="display:inline;">
                                                    <input type="hidden" name="vacante_id" value="<?= $post['vacante_id'] ?? $post['id'] // Fallback if id is not vacante_id logic depending on join ?>">
                                                    <!-- Correction: post['vacante_id'] is the right field from the SELECT query -->
                                                    <input type="hidden" name="vacante_id" value="<?= $post['vacante_id'] ?>">
                                                    <button type="submit" class="btn-view" style="color: #ef4444; border-color: #fecaca; background: #fef2f2;" title="Cancelar Postulación">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
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
<?php
// app/Views/dashboard/postulaciones.php
$postulaciones = $postulaciones ?? [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Postulaciones | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            padding-bottom: 2rem;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        .header-page {
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1rem;
        }
        .header-page h1 {
            color: var(--text-heading);
            font-size: 1.8rem;
        }
        .btn-volver {
            display: inline-block;
            margin-top: 0.5rem;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        /* Tabla de postulaciones */
        .table-responsive {
            overflow-x: auto;
            background: var(--bg-card);
            border-radius: 10px;
            box-shadow: 0 4px 6px var(--shadow-color);
            border: 1px solid var(--border-color);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        th {
            background: var(--bg-secondary);
            color: var(--text-heading);
            font-weight: 600;
        }
        tr:last-child td {
            border-bottom: none;
        }
        tr:hover {
            background: rgba(102, 126, 234, 0.05);
        }
        
        /* Badges */
        .badge {
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }
        .badge-pendiente { background: rgba(251, 191, 36, 0.2); color: #d97706; }
        .badge-revisado { background: rgba(59, 130, 246, 0.2); color: #2563eb; }
        .badge-aceptado { background: rgba(34, 197, 94, 0.2); color: #16a34a; }
        .badge-rechazado { background: rgba(239, 68, 68, 0.2); color: #dc2626; }
        
        /* Modo Oscuro Badges overrides */
        [data-theme="dark"] .badge-pendiente { color: #fcd34d; }
        [data-theme="dark"] .badge-revisado { color: #60a5fa; }
        [data-theme="dark"] .badge-aceptado { color: #4ade80; }
        [data-theme="dark"] .badge-rechazado { color: #fca5a5; }

        .btn-ver {
            padding: 0.4rem 0.8rem;
            background: #667eea;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.3s;
        }
        .btn-ver:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="container">
        <?php if (isset($_SESSION['message'])): ?>
            <div style="padding: 1rem; margin-bottom: 1.5rem; border-radius: 8px; 
                background: <?= $_SESSION['message']['type'] === 'success' ? 'rgba(34, 197, 94, 0.2)' : 'rgba(239, 68, 68, 0.2)' ?>; 
                color: <?= $_SESSION['message']['type'] === 'success' ? '#16a34a' : '#dc2626' ?>;">
                <?= htmlspecialchars($_SESSION['message']['text']) ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <div class="header-page">
            <h1>📋 Mis Postulaciones</h1>
            <a href="<?= ENV_APP['BASE_URL'] ?>/candidato/dashboard" class="btn-volver">← Volver al Dashboard</a>
        </div>

        <?php if (empty($postulaciones)): ?>
            <div style="text-align: center; padding: 3rem; background: var(--bg-card); border-radius: 10px; border: 1px solid var(--border-color);">
                <h3>Aún no te has postulado a ninguna vacante</h3>
                <p style="margin-top: 0.5rem; color: var(--text-secondary);">Explora las oportunidades disponibles y aplica hoy mismo.</p>
                <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes" class="btn-ver" style="display: inline-block; margin-top: 1rem;">Explorar Vacantes</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Empresa</th>
                            <th>Vacante</th>
                            <th>Fecha Aplicación</th>
                            <th>Modalidad</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($postulaciones as $p): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($p['empresa_nombre']) ?></strong></td>
                                <td>
                                    <span style="display:block; font-weight:600;"><?= htmlspecialchars($p['titulo']) ?></span>
                                    <span style="font-size:0.85rem; color:var(--text-secondary);">
                                        <?= htmlspecialchars($p['ubicacion']) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($p['fecha_postulacion'])) ?></td>
                                <td><?= ucfirst(htmlspecialchars($p['modalidad'])) ?></td>
                                <td>
                                    <span class="badge badge-<?= htmlspecialchars($p['estado']) ?>">
                                        <?= ucfirst(htmlspecialchars($p['estado'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes/<?= htmlspecialchars($p['slug']) ?>" class="btn-ver" target="_blank">
                                        Ver Oferta
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
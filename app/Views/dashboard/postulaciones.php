<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Mis Postulaciones</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem 1rem; padding-top: 100px; }
        
        .page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid #e2e8f0;
        }
        
        /* ALERTA DE ÉXITO */
        .alert-box {
            padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 2rem;
            display: flex; align-items: center; gap: 10px; font-weight: 500;
            animation: slideDown 0.5s ease;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        
        @keyframes slideDown { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }

        /* Estilos Tabla */
        .table-card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .styled-table { width: 100%; border-collapse: collapse; }
        .styled-table th { background: #f8fafc; padding: 1rem; text-align: left; font-weight: 600; color: #64748b; border-bottom: 1px solid #e2e8f0; }
        .styled-table td { padding: 1.2rem 1rem; border-bottom: 1px solid #e2e8f0; color: #1e293b; }
        .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: capitalize; }
        .pendiente { background: #fffbeb; color: #b45309; }
        .aceptado { background: #dcfce7; color: #166534; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="container">
        
        <?php if(isset($_SESSION['mensaje'])): ?>
            <div class="alert-box <?= $_SESSION['mensaje']['tipo'] === 'success' ? 'alert-success' : 'alert-error' ?>">
                <i class="fas <?= $_SESSION['mensaje']['tipo'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
                <?= $_SESSION['mensaje']['texto'] ?>
            </div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>

        <div class="page-header">
            <h1 style="font-size: 1.8rem; color: #1e293b; margin: 0;">
                <i class="fas fa-clipboard-list" style="color: #2563eb;"></i> Mis Postulaciones
            </h1>
            <a href="<?= ENV_APP['BASE_URL'] ?>/candidato/dashboard" style="color: #64748b; text-decoration: none; font-weight: 500;">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
        </div>

        <div class="table-card">
            <?php if (empty($postulaciones)): ?>
                <div style="text-align: center; padding: 4rem;">
                    <i class="fas fa-folder-open" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                    <p style="color: #64748b;">No tienes postulaciones activas.</p>
                    <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes" style="color: #2563eb; font-weight: 600;">Buscar Empleo</a>
                </div>
            <?php else: ?>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Empresa</th>
                            <th>Vacante</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($postulaciones as $post): ?>
                            <tr>
                                <td style="font-weight: 600; color: #2563eb;"><?= htmlspecialchars($post['empresa_nombre']) ?></td>
                                <td><?= htmlspecialchars($post['titulo']) ?></td>
                                <td><?= date('d/m/Y', strtotime($post['fecha_postulacion'])) ?></td>
                                <td><span class="status-badge <?= $post['estado'] ?>"><?= $post['estado'] ?></span></td>
                                <td>
                                    <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes/<?= $post['slug'] ?>" style="color: #64748b; text-decoration: none;">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
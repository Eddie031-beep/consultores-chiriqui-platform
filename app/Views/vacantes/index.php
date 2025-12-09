<?php
use App\Helpers\Auth;
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Vacantes | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/style.css">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../components/navbar.php'; ?>

<div class="dashboard-wrapper">
    <div class="page-header" style="background: white; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
        <div class="header-left">
            <h2><i class="fas fa-briefcase" style="color: #4f46e5; margin-right: 10px;"></i> Mis Vacantes</h2>
            <p style="color: #64748b; margin: 5px 0 0;">Gestiona las ofertas de empleo publicadas.</p>
        </div>
        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes/crear" class="btn-primary">
            <i class="fas fa-plus"></i> Nueva Vacante
        </a>
    </div>

<div class="table-container" style="margin-top: 0;">
    <?php if (empty($vacantes)): ?>
        <div style="text-align: center; padding: 3rem; color: #94a3b8;">
            <i class="fas fa-folder-open" style="font-size: 3rem; margin-bottom: 1rem; color: #cbd5e1;"></i>
            <h3 style="color: #64748b; font-size: 1.2rem; margin-bottom: 0.5rem;">No tienes vacantes publicadas</h3>
            <p>¡Crea la primera para empezar a recibir candidatos!</p>
        </div>
    <?php else: ?>
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Ubicación</th>
                    <th>Modalidad</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vacantes as $v): ?>
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($v['titulo']) ?></div>
                    </td>
                    <td><i class="fas fa-map-marker-alt" style="color: #94a3b8; font-size: 0.8rem;"></i> <?= htmlspecialchars($v['ubicacion']) ?></td>
                    <td><span style="background: #f1f5f9; padding: 2px 8px; border-radius: 6px; font-size: 0.85rem; color: #475569;"><?= ucfirst($v['modalidad']) ?></span></td>
                    <td>
                        <?php if($v['estado'] === 'abierta'): ?>
                            <span class="status-badge status-active">Abierta</span>
                        <?php else: ?>
                            <span class="status-badge status-inactive">Cerrada</span>
                        <?php endif; ?>
                    </td>
                    <td style="color: #64748b; font-size: 0.9rem;"><?= date('d/m/Y', strtotime($v['fecha_publicacion'])) ?></td>
                    <td>
                        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes/<?= $v['id'] ?>" class="btn-secondary" style="padding: 5px 10px; font-size: 0.85rem;">
                            <i class="fas fa-pencil-alt"></i> Editar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</div> <!-- End dashboard-wrapper -->
</body>
</html>

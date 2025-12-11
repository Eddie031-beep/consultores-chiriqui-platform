<?php
use App\Helpers\Auth;
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Corporativo | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-animations.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<?php include __DIR__ . '/../components/navbar.php'; ?>

<div class="dashboard-wrapper">
    
    <div class="dashboard-header animate-slide-up">
        <div class="header-title">
            <h1>Panel Corporativo</h1>
            <p class="header-subtitle">Bienvenido, <?= htmlspecialchars($user['nombre']) ?></p>
        </div>
        <div class="user-menu" style="display: flex; gap: 10px;">
            <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/perfil" class="btn-secondary" style="border-radius: 50px;">
                <i class="fas fa-cog"></i> Configuración
            </a>
        </div>
    </div>

    <?php if ($facturacionInfo && $facturacionInfo['pendientes'] > 0): ?>
    <div class="cost-alert animate-slide-up delay-100" style="margin-bottom: 2rem; border-left: 4px solid #ef4444; background: #fff5f5; padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h4 style="color: #991b1b; margin: 0 0 5px 0; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-bell"></i> Facturación Pendiente
                </h4>
                <p style="color: #7f1d1d; margin: 0; font-size: 0.95rem;">
                    La consultora ha generado <strong><?= $facturacionInfo['pendientes'] ?> factura(s)</strong> por un total de <strong>B/. <?= number_format($facturacionInfo['deuda'], 2) ?></strong>.
                </p>
            </div>
            <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/facturacion" class="btn-primary" style="background: #ef4444; border: none;">
                Ver
            </a>
        </div>
    </div>
    <?php endif; ?>

    <div class="dashboard-grid">
        <div class="glass-card animate-slide-up delay-100">
            <div class="card-icon-wrapper" style="background: #eff6ff; color: #2563eb;">
                <i class="fas fa-briefcase"></i>
            </div>
            <div class="card-content">
                <h3><?= $vacantesActivas ?></h3>
                <p>Vacantes Activas</p>
            </div>
            <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes" class="card-arrow"><i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="glass-card animate-slide-up delay-200">
            <div class="card-icon-wrapper" style="background: #f0fdf4; color: #16a34a;">
                <i class="fas fa-users"></i>
            </div>
            <div class="card-content">
                <h3><?= $totalCandidatos ?></h3>
                <p>Candidatos Totales</p>
            </div>
            <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/postulantes" class="card-arrow"><i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="glass-card animate-slide-up delay-300">
            <div class="card-icon-wrapper" style="background: #fff7ed; color: #ea580c;">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div class="card-content">
                <h3>Facturación</h3>
                <p>Historial y Pagos</p>
            </div>
            <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/facturacion" class="card-arrow"><i class="fas fa-arrow-right"></i></a>
        </div>
    </div>

    <div class="table-container animate-slide-up delay-400">
        <div class="section-header">
            <div class="section-title"><i class="fas fa-history" style="margin-right: 10px; color: #64748b;"></i> Últimas Postulaciones</div>
            <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/postulantes" class="action-icon">Ver todo</a>
        </div>
        
        <?php if(empty($actividadReciente)): ?>
            <p style="text-align: center; color: #94a3b8; padding: 2rem;">Aún no hay actividad reciente.</p>
        <?php else: ?>
            <div class="activity-list">
                <?php foreach($actividadReciente as $act): ?>
                <div class="activity-item" style="padding: 10px 0; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #64748b; font-weight: bold;">
                            <?= strtoupper(substr($act['nombre'], 0, 1)) ?>
                        </div>
                        <div>
                            <div style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($act['nombre'] . ' ' . $act['apellido']) ?></div>
                            <div style="font-size: 0.85rem; color: #64748b;">Aplicó a: <span style="color: #2563eb;"><?= htmlspecialchars($act['titulo']) ?></span></div>
                        </div>
                    </div>
                    <div style="font-size: 0.8rem; color: #94a3b8;">
                        <?= date('d M, H:i', strtotime($act['fecha_postulacion'])) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>
</body>
</html>
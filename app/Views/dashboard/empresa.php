<?php
use App\Helpers\Auth;
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Empresa | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/style.css">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<?php include __DIR__ . '/../components/navbar.php'; ?>

<div class="dashboard-wrapper">
    <div class="dashboard-header">
        <div class="header-title">
            <h1>Bienvenido, <?= htmlspecialchars($user['nombre']) ?></h1>
            <div class="header-subtitle">Panel de Gestión Empresarial</div>
        </div>
        <div class="user-menu">
            <div class="user-menu-trigger">
                <i class="fas fa-building"></i>
                <span><?= htmlspecialchars($user['nombre']) ?></span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="user-menu-dropdown">
                <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/perfil" class="user-menu-item">
                    <i class="fas fa-cog"></i> Configuración
                </a>
                <a href="<?= ENV_APP['BASE_URL'] ?>/logout" class="user-menu-item">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Card: Vacantes Activas -->
        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes" class="glass-card">
            <div class="card-icon-wrapper" style="background: #e0f2fe; color: #0284c7;">
                <i class="fas fa-briefcase"></i>
            </div>
            <div class="card-content">
                <h3>Vacantes Activas</h3>
                <p><?= $vacantesActivas ?> publicadas actualmente</p>
            </div>
            <div class="card-arrow"><i class="fas fa-arrow-right"></i></div>
        </a>

        <!-- Card: Postulantes -->
        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/postulantes" class="glass-card">
            <div class="card-icon-wrapper" style="background: #dcfce7; color: #166534;">
                <i class="fas fa-users"></i>
            </div>
            <div class="card-content">
                <h3>Candidatos</h3>
                <p><?= $totalCandidatos ?> perfiles interesados</p>
            </div>
            <div class="card-arrow"><i class="fas fa-arrow-right"></i></div>
        </a>

        <!-- Card: Consumo -->
        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/facturacion" class="glass-card">
            <div class="card-icon-wrapper" style="background: #fef9c3; color: #854d0e;">
                <i class="fas fa-coins"></i>
            </div>
            <div class="card-content">
                <h3>Consumo del Mes</h3>
                <p>B/. <?= number_format($consumoActual, 2) ?> acumulado</p>
            </div>
            <div class="card-arrow"><i class="fas fa-arrow-right"></i></div>
        </a>

        <!-- Card: Facturación -->
        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/facturacion" class="glass-card">
            <div class="card-icon-wrapper" style="background: #fee2e2; color: #991b1b;">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="card-content">
                <h3>Facturación</h3>
                <p>Ver historial y pagos pendientes</p>
            </div>
            <div class="card-arrow"><i class="fas fa-arrow-right"></i></div>
        </a>
    </div>

    <!-- Actividad Reciente -->
    <div class="table-container">
        <div class="section-header">
            <div class="section-title">
                <i class="fas fa-clock" style="color: #64748b;"></i>
                Actividad Reciente
            </div>
        </div>
        
        <?php if(empty($actividadReciente)): ?>
            <p style="text-align: center; color: #94a3b8; padding: 2rem;">No hay actividad reciente.</p>
        <?php else: ?>
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>Candidato</th>
                        <th>Vacante</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($actividadReciente as $act): ?>
                    <tr>
                        <td style="font-weight: 500;">
                            <?= htmlspecialchars($act['nombre'] . ' ' . $act['apellido']) ?>
                        </td>
                        <td><?= htmlspecialchars($act['titulo']) ?></td>
                        <td style="color: #64748b;"><?= date('d M, Y', strtotime($act['fecha_postulacion'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    </div> <!-- End dashboard-wrapper -->
</body>
</html>
<?php
use App\Helpers\Auth;
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postulantes | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/style.css">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../components/navbar.php'; ?>

<div class="dashboard-wrapper">
    <div class="page-header">
    <div class="header-left">
        <h2><i class="fas fa-users" style="color: #4f46e5; margin-right: 10px;"></i> Gestión de Postulantes</h2>
        <p style="color: #64748b; margin: 5px 0 0;">Selecciona una vacante para ver los candidatos interesados.</p>
    </div>
    <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/dashboard" class="back-btn"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<div class="form-row">
    <!-- Sidebar: Lista de Vacantes -->
    <div style="background: white; padding: 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0; height: fit-content;">
        <h3 style="font-size: 1rem; color: #64748b; text-transform: uppercase; margin-bottom: 1rem; letter-spacing: 0.05em;">Vacantes</h3>
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            <?php foreach ($vacantes as $v): ?>
                <a href="?vacante_id=<?= $v['vacante_id'] ?>" 
                   style="display: flex; justify-content: space-between; padding: 10px; border-radius: 6px; text-decoration: none; color: #334155; <?= $selectedVacante == $v['vacante_id'] ? 'background: #eff6ff; color: #2563eb; font-weight: 600;' : 'background: #f8fafc;' ?>">
                    <span><?= htmlspecialchars($v['titulo']) ?></span>
                    <span style="background: <?= $selectedVacante == $v['vacante_id'] ? '#bfdbfe' : '#e2e8f0' ?>; padding: 2px 8px; border-radius: 10px; font-size: 0.8rem;">
                        <?= $v['cantidad_postulantes'] ?>
                    </span>
                </a>
            <?php endforeach; ?>
            <?php if (empty($vacantes)): ?>
                <p style="color: #94a3b8; font-size: 0.9rem;">No tienes vacantes con postulantes aún.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main: Lista de Candidatos -->
    <div class="table-container" style="margin-top: 0;">
        <?php if (!$selectedVacante): ?>
            <div style="text-align: center; padding: 3rem; color: #94a3b8;">
                <i class="fas fa-mouse-pointer" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                <p>Selecciona una vacante de la izquierda para ver los detalles.</p>
            </div>
        <?php else: ?>
            <h3 style="margin-bottom: 1.5rem; color: #1e293b;">Postulantes para esta vacante</h3>
            <?php if (empty($detalles)): ?>
                <p style="color: #64748b;">No hay candidatos para mostrar.</p>
            <?php else: ?>
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th>Candidato</th>
                            <th>Contacto</th>
                            <th>Habilidades</th>
                            <th>CV</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalles as $candidato): ?>
                        <tr>
                            <td>
                                <div style="font-weight: 500;"><?= htmlspecialchars($candidato['nombre'] . ' ' . $candidato['apellido']) ?></div>
                                <div style="font-size: 0.85rem; color: #64748b;">Postulado: <?= date('d/m/Y', strtotime($candidato['fecha_postulacion'])) ?></div>
                            </td>
                            <td>
                                <div style="font-size: 0.9rem;"><i class="fas fa-envelope" style="width: 15px;"></i> <?= htmlspecialchars($candidato['email']) ?></div>
                                <div style="font-size: 0.9rem;"><i class="fas fa-phone" style="width: 15px;"></i> <?= htmlspecialchars($candidato['telefono']) ?></div>
                            </td>
                            <td>
                                <div style="max-width: 200px; font-size: 0.85rem; color: #475569;">
                                    <?= htmlspecialchars($candidato['habilidades'] ?: 'No especificadas') ?>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($candidato['cv_path'])): ?>
                                    <a href="<?= ENV_APP['BASE_URL'] . $candidato['cv_path'] ?>" target="_blank" class="status-badge status-active" style="text-decoration: none;">
                                        <i class="fas fa-file-pdf"></i> Ver CV
                                    </a>
                                <?php else: ?>
                                    <span class="status-badge status-inactive">Sin CV</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn-secondary" disabled title="Próximamente"><i class="fas fa-comment"></i> Chat</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

</div> <!-- End dashboard-wrapper -->
</body>
</html>

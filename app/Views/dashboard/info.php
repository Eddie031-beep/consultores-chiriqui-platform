<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-animations.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="dashboard-wrapper">
        
        <div class="page-header animate-slide-up">
            <div>
                <h2><i class="fas fa-info-circle" style="color: #2563eb; margin-right: 10px;"></i> Información Corporativa</h2>
                <p style="color: #64748b; margin: 5px 0 0;">Datos globales de la organización visibles en el sistema.</p>
            </div>
            <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/dashboard" class="back-btn">
                <i class="fas fa-arrow-left"></i> Volver al Panel
            </a>
        </div>

        <div class="dashboard-grid animate-slide-up delay-100" style="display: block; max-width: 800px; margin: 0 auto;">
            
            <div class="form-card" style="padding: 2.5rem;">
                
                <div style="text-align: center; margin-bottom: 2.5rem;">
                    <div style="width: 80px; height: 80px; background: #eff6ff; color: #2563eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 1rem auto;">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3 style="margin: 0; color: #1e293b; font-size: 1.5rem;">Consultores Chiriquí S.A.</h3>
                    <p style="color: #64748b;">Plataforma de Gestión de Vacantes</p>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-map-marker-alt" style="color: #94a3b8;"></i> Dirección Fiscal
                        </label>
                        <div style="padding: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; color: #334155; font-weight: 500;">
                            <?= htmlspecialchars($info['direccion']) ?>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-phone" style="color: #94a3b8;"></i> Teléfono de Soporte
                        </label>
                        <div style="padding: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; color: #334155; font-weight: 500;">
                            <?= htmlspecialchars($info['telefono']) ?>
                        </div>
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1; margin-bottom: 0;">
                        <label class="form-label" style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-envelope" style="color: #94a3b8;"></i> Correo Administrativo
                        </label>
                        <div style="padding: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; color: #334155; font-weight: 500;">
                            <?= htmlspecialchars($info['email']) ?>
                        </div>
                    </div>

                </div>

                <div style="margin-top: 2.5rem; padding: 1rem; background: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 4px; display: flex; gap: 10px; align-items: start;">
                    <i class="fas fa-lock" style="color: #d97706; margin-top: 3px;"></i>
                    <div>
                        <strong style="display: block; color: #92400e; font-size: 0.9rem; margin-bottom: 2px;">Información de Solo Lectura</strong>
                        <p style="margin: 0; color: #b45309; font-size: 0.85rem;">
                            Estos datos son utilizados globalmente en las facturas y el pie de página. Para modificarlos, contacte al soporte técnico de base de datos.
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</body>
</html>
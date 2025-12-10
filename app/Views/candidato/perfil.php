<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/style.css">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../components/navbar.php'; ?>

<style>
    body { padding-top: 90px; }
    .alert-box {
        padding: 1rem 1.5rem; 
        border-radius: 12px; 
        margin-bottom: 2rem;
        display: flex; 
        align-items: center; 
        gap: 12px; 
        font-weight: 600;
        animation: slideDown 0.4s ease-out;
    }
    .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    @keyframes slideDown { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
</style>
<div class="dashboard-wrapper">
    <?php if(isset($_SESSION['mensaje'])): ?>
        <div class="animate-slide-up" style="
            background: <?= $_SESSION['mensaje']['tipo'] === 'success' ? '#dcfce7' : '#fee2e2' ?>;
            color: <?= $_SESSION['mensaje']['tipo'] === 'success' ? '#166534' : '#991b1b' ?>;
            border: 1px solid <?= $_SESSION['mensaje']['tipo'] === 'success' ? '#bbf7d0' : '#fecaca' ?>;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex; align-items: center; gap: 10px;
            font-weight: 500;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        ">
            <i class="fas <?= $_SESSION['mensaje']['tipo'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> fa-lg"></i>
            <div><?= $_SESSION['mensaje']['texto'] ?></div>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>
    <div class="page-header">
    <div class="header-left">
        <h2><i class="fas fa-user-circle" style="color: #4f46e5; margin-right: 10px;"></i> Mi Perfil</h2>
        <p style="color: #64748b; margin: 5px 0 0;">Gestiona tu información personal y currículum.</p>
    </div>
    <a href="<?= ENV_APP['BASE_URL'] ?>/candidato/dashboard" class="back-btn"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<div class="dashboard-grid" style="grid-template-columns: 1fr 1fr;">
    
    <!-- Info Personal -->
    <div class="form-card" style="margin: 0; max-width: 100%;">
        <div class="section-header">
            <h3 style="margin:0; color:#1e293b;">Información Personal</h3>
        </div>
        
        <form action="<?= ENV_APP['BASE_URL'] ?>/candidato/actualizar" method="POST">
            <div class="form-group">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-input" value="<?= htmlspecialchars($perfil['nombre']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Apellido</label>
                <input type="text" name="apellido" class="form-input" value="<?= htmlspecialchars($perfil['apellido']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" value="<?= htmlspecialchars($perfil['email']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Teléfono</label>
                <input type="tel" name="telefono" class="form-input" value="<?= htmlspecialchars($perfil['telefono']) ?>">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
            </div>
        </form>
    </div>

    <!-- CV Upload -->
    <div class="form-card" style="margin: 0; max-width: 100%;">
        <div class="section-header">
            <h3 style="margin:0; color:#1e293b;">Currículum Vitae</h3>
        </div>
        
        <div style="text-align: center; padding: 2rem; border: 2px dashed #cbd5e1; border-radius: 12px; margin-bottom: 2rem;">
            <?php if (!empty($perfil['cv_ruta'])): ?>
                <i class="fas fa-file-pdf" style="font-size: 3rem; color: #ef4444; margin-bottom: 1rem;"></i>
                <p style="margin-bottom: 1rem;">CV Actual: <strong><?= basename($perfil['cv_ruta']) ?></strong></p>
                
                <a href="<?= ENV_APP['BASE_URL'] . $perfil['cv_ruta'] ?>" target="_blank" class="btn-secondary" style="display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-eye"></i> Ver / Descargar
                </a>
            <?php else: ?>
                <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                <p>No tienes un CV subido aún.</p>
            <?php endif; ?>
        </div>

        <form action="<?= ENV_APP['BASE_URL'] ?>/candidato/subir-cv" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label class="form-label">Subir nuevo PDF</label>
                <input type="file" name="cv_file" class="form-input" accept="application/pdf" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-primary" style="background: #0f172a;"><i class="fas fa-upload"></i> Subir CV</button>
            </div>
        </form>

        <div style="margin-top: 15px; border-top: 1px solid #e2e8f0; padding-top: 15px;">
            <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 10px;">¿No tienes el archivo a mano?</p>
            <a href="<?= ENV_APP['BASE_URL'] ?>/candidato/descargar-cv-generado" target="_blank" class="btn-secondary" style="width: 100%; display: block; text-align: center;">
                <i class="fas fa-magic"></i> Generar y Descargar CV Automático
            </a>
        </div>
    </div>
</div>

</div> <!-- End dashboard-wrapper -->
</body>
</html>

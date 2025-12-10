<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil | Consultora</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css?v=<?= time() ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="dashboard-wrapper">
        <div class="page-header animate-slide-up">
            <div>
                <h2><i class="fas fa-user-shield" style="color: #2563eb; margin-right: 10px;"></i> Mi Perfil Administrativo</h2>
                <p style="color: #64748b; margin: 5px 0 0;">Gestiona tus credenciales de acceso al sistema.</p>
            </div>
            <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/dashboard" class="back-btn">Volver al Panel</a>
        </div>

        <?php if(isset($_SESSION['mensaje'])): ?>
            <div style="padding: 1rem; border-radius: 8px; margin-bottom: 2rem; background: <?= $_SESSION['mensaje']['tipo'] === 'success' ? '#dcfce7' : '#fee2e2' ?>; color: <?= $_SESSION['mensaje']['tipo'] === 'success' ? '#166534' : '#991b1b' ?>;">
                <?= $_SESSION['mensaje']['texto'] ?>
            </div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>

        <div class="form-card animate-slide-up delay-100" style="max-width: 600px; margin: 0 auto;">
            
            <form action="<?= ENV_APP['BASE_URL'] ?>/consultora/perfil" method="POST">
                <div class="form-group">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-input" value="<?= htmlspecialchars($perfil['nombre']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Apellido</label>
                    <input type="text" name="apellido" class="form-input" value="<?= htmlspecialchars($perfil['apellido']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" name="email" class="form-input" value="<?= htmlspecialchars($perfil['email']) ?>" required>
                </div>

                <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                    <h4 style="margin-bottom: 1rem; color: #1e293b;">Cambiar Contraseña</h4>
                    <div class="form-group">
                        <label class="form-label">Nueva Contraseña (Opcional)</label>
                        <input type="password" name="password" class="form-input" placeholder="Deja en blanco para no cambiar">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

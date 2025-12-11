<?php
use App\Helpers\Auth;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de Empresa</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-animations.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../components/navbar.php'; ?>

<div class="dashboard-wrapper">
    
    <?php if(isset($_SESSION['mensaje'])): ?>
        <div class="animate-slide-up" style="
            padding: 1rem 1.5rem; 
            border-radius: 8px; 
            margin-bottom: 2rem; 
            display: flex; 
            align-items: center; 
            gap: 10px;
            font-weight: 500;
            background: <?= $_SESSION['mensaje']['tipo'] === 'success' ? '#dcfce7' : '#fee2e2' ?>; 
            color: <?= $_SESSION['mensaje']['tipo'] === 'success' ? '#166534' : '#991b1b' ?>;
            border: 1px solid <?= $_SESSION['mensaje']['tipo'] === 'success' ? '#bbf7d0' : '#fecaca' ?>;">
            
            <i class="fas <?= $_SESSION['mensaje']['tipo'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
            <?= $_SESSION['mensaje']['texto'] ?>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <div class="page-header animate-slide-up">
        <div>
            <h2><i class="fas fa-building" style="color: #2563eb; margin-right: 10px;"></i> Perfil Corporativo</h2>
            <p style="color: #64748b; margin: 5px 0 0;">Gestiona la información pública de tu empresa y datos de acceso.</p>
        </div>
        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/dashboard" class="back-btn">Volver</a>
    </div>

    <form action="<?= ENV_APP['BASE_URL'] ?>/empresa/guardar-perfil" method="POST" class="animate-slide-up delay-100">
        <div class="dashboard-grid" style="grid-template-columns: 2fr 1fr;">
            
            <div class="form-card" style="margin: 0; max-width: 100%;">
                <h3 style="color: #1e293b; margin-bottom: 1.5rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">Datos Públicos de la Empresa</h3>
                
                <div class="form-group">
                    <label class="form-label">Nombre Comercial (No editable)</label>
                    <input type="text" class="form-input" value="<?= htmlspecialchars($data['nombre']) ?>" disabled style="background: #f1f5f9; color: #64748b;">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">RUC</label>
                        <input type="text" class="form-input" value="<?= htmlspecialchars($data['ruc'] . '-' . $data['dv']) ?>" disabled style="background: #f1f5f9; color: #64748b;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sector</label>
                        <select name="sector" class="form-input" required>
                            <option value="">Seleccione un sector...</option>
                            <?php foreach ($sectores as $sec): ?>
                                <option value="<?= htmlspecialchars($sec) ?>" <?= ($data['sector'] === $sec) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sec) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Dirección Física</label>
                    <input type="text" name="direccion" class="form-input" value="<?= htmlspecialchars($data['direccion']) ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Teléfono Público</label>
                        <input type="text" name="telefono" class="form-input" value="<?= htmlspecialchars($data['telefono']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sitio Web</label>
                        <input type="url" name="sitio_web" class="form-input" value="<?= htmlspecialchars($data['sitio_web']) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email de Contacto (Para candidatos)</label>
                    <input type="email" name="email_contacto" class="form-input" value="<?= htmlspecialchars($data['email_contacto']) ?>">
                </div>
            </div>

            <div class="form-card" style="margin: 0; max-width: 100%; height: fit-content;">
                <h3 style="color: #1e293b; margin-bottom: 1.5rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">Usuario Administrador</h3>
                
                <div class="form-group">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="user_nombre" class="form-input" value="<?= htmlspecialchars($data['user_nombre']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Apellido</label>
                    <input type="text" name="user_apellido" class="form-input" value="<?= htmlspecialchars($data['user_apellido']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email de Acceso (No editable)</label>
                    <input type="email" class="form-input" value="<?= htmlspecialchars($data['user_email']) ?>" disabled style="background: #f1f5f9;">
                </div>

                <div style="margin-top: 2rem;">
                    <button type="submit" class="btn-primary" style="width: 100%; justify-content: center;">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
</body>
</html>

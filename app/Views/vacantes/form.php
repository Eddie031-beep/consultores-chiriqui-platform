<?php
use App\Helpers\Auth;
$user = Auth::user();

// Helpers
$v = $vacante ?? [];
$valor = function(string $campo, $default = '') use ($v) {
    return $v[$campo] ?? $_POST[$campo] ?? $default;
};
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $modo === 'editar' ? 'Editar Vacante' : 'Nueva Vacante' ?> | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/style.css">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../components/navbar.php'; ?>

<div class="dashboard-wrapper">
    <div class="page-header" style="padding: 1rem 0; margin-bottom: 2rem;">
    <div class="header-left">
        <h2 style="font-size: 1.5rem; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-briefcase" style="color: #4f46e5;"></i>
            <?= $modo === 'editar' ? 'Editar Vacante' : 'Nueva Vacante' ?>
        </h2>
    </div>
    <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes" class="back-btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<div class="dashboard-grid" style="grid-template-columns: 1fr; margin-top: 0;"> <!-- Full width -->
    <div class="form-card" style="max-width: 900px; margin: 0 auto; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        
        <form method="post" action="">
            <?php if ($modo === 'editar'): ?>
                <input type="hidden" name="id" value="<?= (int)($v['id'] ?? 0) ?>">
            <?php endif; ?>

            <div class="form-group">
                <label class="form-label" for="titulo">Título de la vacante</label>
                <input type="text" id="titulo" name="titulo" class="form-input" value="<?= htmlspecialchars($valor('titulo')) ?>" required placeholder="Ej: Desarrollador Senior PHP">
            </div>

            <div class="form-group">
                <label class="form-label" for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" class="form-input" style="min-height: 150px;" required placeholder="Describe las responsabilidades y requisitos..."><?= htmlspecialchars($valor('descripcion')) ?></textarea>
            </div>

            <div class="form-row"> <!-- Two columns -->
                <div class="form-group">
                    <label class="form-label" for="tipo_contrato">Tipo de contrato</label>
                    <input type="text" id="tipo_contrato" name="tipo_contrato" class="form-input" placeholder="Tiempo completo, medio tiempo..." value="<?= htmlspecialchars($valor('tipo_contrato')) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="ubicacion">Ubicación</label>
                    <input type="text" id="ubicacion" name="ubicacion" class="form-input" value="<?= htmlspecialchars($valor('ubicacion')) ?>" required placeholder="Ej: Ciudad de Panamá / Remoto">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="cantidad_plazas">Cantidad de Plazas</label>
                    <input type="number" id="cantidad_plazas" name="cantidad_plazas" class="form-input" value="<?= htmlspecialchars($valor('cantidad_plazas', '1')) ?>" min="1" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="fecha_cierre">Fecha de Cierre</label>
                    <input type="date" id="fecha_cierre" name="fecha_cierre" class="form-input" value="<?= htmlspecialchars($valor('fecha_cierre')) ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="modalidad">Modalidad</label>
                    <select id="modalidad" name="modalidad" class="form-select">
                        <?php $mod = $valor('modalidad', 'presencial'); ?>
                        <option value="presencial" <?= $mod === 'presencial' ? 'selected' : '' ?>>Presencial</option>
                        <option value="remoto" <?= $mod === 'remoto' ? 'selected' : '' ?>>Remoto</option>
                        <option value="hibrido" <?= $mod === 'hibrido' ? 'selected' : '' ?>>Híbrido</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="salario_min">Salario Mínimo (Opcional)</label>
                    <input type="number" step="0.01" id="salario_min" name="salario_min" class="form-input" value="<?= htmlspecialchars($valor('salario_min')) ?>" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label class="form-label" for="salario_max">Salario Máximo (Opcional)</label>
                    <input type="number" step="0.01" id="salario_max" name="salario_max" class="form-input" value="<?= htmlspecialchars($valor('salario_max')) ?>" placeholder="0.00">
                </div>
            </div>

            <!-- Costo por Vista Highlight -->
            <div style="background: #f0f9ff; padding: 1.5rem; border-radius: 8px; border: 1px solid #bae6fd; margin-bottom: 2rem;">
                <label class="form-label" style="color: #0369a1; font-weight: 600;">Costo por Vista ($)</label>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 1.2rem; font-weight: bold; color: #0284c7;">B/.</span>
                    <input type="number" id="costo" name="costo_por_vista" step="0.01" min="1.00" max="10.00" value="<?= htmlspecialchars($valor('costo_por_vista', '1.00')) ?>" required class="form-input" style="width: 150px; margin-bottom: 0;">
                </div>
                <p style="margin: 5px 0 0; font-size: 0.85rem; color: #0284c7;">Tarifa por cada candidato que vea los detalles. (Mín: 1.00, Máx: 10.00)</p>
            </div>

            <div class="form-actions" style="margin-top: 2rem;">
                <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; font-size: 1rem; padding: 1rem;">
                    <i class="fas fa-save"></i> <?= $modo === 'editar' ? 'Guardar Cambios' : 'Publicar Vacante' ?>
                </button>
            </div>
        </form>
    </div>
</div>

</div> <!-- End dashboard-wrapper -->
</body>
</html>

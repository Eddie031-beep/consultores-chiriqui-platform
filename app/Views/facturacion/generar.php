<?php
use App\Helpers\Auth;
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generar Factura | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css?v=<?= time() ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-grid" style="display: block; max-width: 900px; margin: 0 auto;">
        
        <!-- Header -->
        <div class="page-header">
            <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion" class="back-btn">
                <i class="fas fa-arrow-left"></i> Volver a Facturación
            </a>
            <h2>🧾 Nueva Factura Fiscal</h2>
        </div>

        <!-- Error Message -->
        <?php if (!empty($error)): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #fecaca; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="form-card">
            
            <div style="margin-bottom: 2rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem;">
                <h3 style="margin: 0; color: #1e293b; font-size: 1.1rem;">Detalles de Facturación</h3>
                <p style="margin: 5px 0 0 0; color: #64748b; font-size: 0.9rem;">Selecciona la empresa y el período para calcular los cargos.</p>
            </div>

            <form action="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion/generar" method="POST">
                
                <div class="form-group">
                    <label class="form-label">Empresa o Cliente</label>
                    <div style="position: relative;">
                        <i class="fas fa-building" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                        <select name="empresa_id" required class="form-select" style="padding-left: 2.5rem;">
                            <option value="">Seleccione una empresa...</option>
                            <?php foreach ($empresas as $empresa): ?>
                                <option value="<?= $empresa['id'] ?>" <?= (isset($selectedEmpresa) && $selectedEmpresa == $empresa['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($empresa['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Fecha Inicial</label>
                        <input type="date" name="periodo_desde" required class="form-input" value="<?= date('Y-m-01') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha Final</label>
                        <input type="date" name="periodo_hasta" required class="form-input" value="<?= date('Y-m-t') ?>">
                    </div>
                </div>

                <div style="background: #f0f9ff; padding: 1rem; border-radius: 8px; border: 1px dashed #bae6fd; margin-top: 1rem;">
                    <div style="display: flex; gap: 10px;">
                        <i class="fas fa-info-circle" style="color: #0284c7; margin-top: 3px;"></i>
                        <div>
                            <strong style="display: block; color: #0369a1; font-size: 0.9rem; margin-bottom: 2px;">Información de Tarifas</strong>
                            <p style="margin: 0; color: #0c4a6e; font-size: 0.85rem;">
                                El cálculo se realizará automáticamente según las interacciones registradas:<br>
                                • Vistas: B/. 1.50<br>
                                • Clicks: B/. 5.00
                            </p>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion" class="btn-secondary">Cancelar</a>
                    <button type="submit" class="btn-primary" style="border: none; cursor: pointer;">
                        Generar Factura <i class="fas fa-chevron-right" style="margin-left: 5px;"></i>
                    </button>
                </div>

            </form>
        </div>
    </div>
    <script src="<?= ENV_APP['ASSETS_URL'] ?>/js/transitions.js"></script>
</body>
</html>

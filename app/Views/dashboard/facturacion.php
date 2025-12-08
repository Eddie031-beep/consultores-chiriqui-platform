<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Facturación - Consultora</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css?v=<?= time() ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-grid" style="display: block; max-width: 900px; margin: 0 auto;">
        
        <div class="page-header">
            <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/dashboard" class="back-btn">
                <i class="fas fa-arrow-left"></i> Volver al Panel
            </a>
            <h2>💳 Módulo de Facturación</h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            
            <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion/generar" class="glass-card" style="text-align: center; justify-content: center; min-height: 200px;">
                <div class="card-icon-wrapper" style="margin: 0 auto 1.5rem auto;">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <h3>Generar Nueva Factura</h3>
                <p>Crear facturación manual seleccionando empresa y rango de fechas.</p>
            </a>

            <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion" class="glass-card" style="text-align: center; justify-content: center; min-height: 200px;">
                <div class="card-icon-wrapper" style="margin: 0 auto 1.5rem auto;">
                    <i class="fas fa-list-alt"></i>
                </div>
                <h3>Historial de Facturas</h3>
                <p>Ver todas las facturas emitidas, estados y descargar PDF.</p>
            </a>

            <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion/estadisticas" class="glass-card" style="text-align: center; justify-content: center; min-height: 200px;">
                <div class="card-icon-wrapper" style="margin: 0 auto 1.5rem auto;">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Estadísticas de Consumo</h3>
                <p>Ver métricas de vistas y aplicaciones antes de facturar.</p>
            </a>
            
        </div>

    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Facturación</title>
    <style>
        body{font-family:system-ui;background:#020617;color:#e5e7eb;padding:2rem;}
        .container{max-width:1200px;margin:0 auto;}
        h1{color:#38bdf8;margin-bottom:2rem;}
        .back-btn{display:inline-block;margin-bottom:1rem;color:#94a3b8;text-decoration:none;}
        .back-btn:hover{color:#38bdf8;}
        
        .card{background:#0f172a;padding:2rem;border-radius:12px;border:1px solid #1e293b;text-align:center;}
        .actions{margin-top: 20px;}
        .btn{padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block; margin: 0 10px;}
        .btn-primary{background: #2563eb; color: white;}
        .btn-secondary{background: #334155; color: white;}
    </style>
</head>
<body>
    <div class="container">
        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/dashboard" class="back-btn">← Volver al Panel</a>
        <h1>💳 Facturación y Pagos</h1>
        
        <div class="card">
            <h3>Módulo de Facturación</h3>
            <p>Seleccione una opción para continuar</p>
            
            <div class="actions">
                <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion/estadisticas" class="btn btn-primary">📊 Ver Estadísticas de Consumo</a>
                <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion/generar" class="btn btn-secondary">📝 Generar Factura Manual</a>
            </div>
        </div>
    </div>
</body>
</html>

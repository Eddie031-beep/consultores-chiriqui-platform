<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Info Consultora</title>
    <style>
        body{font-family:system-ui;background:#020617;color:#e5e7eb;padding:2rem;}
        .container{max-width:800px;margin:0 auto;}
        h1{color:#38bdf8;}
        .card{background:#0f172a;padding:2rem;border-radius:12px;border:1px solid #1e293b;}
        .back-btn{display:inline-block;margin-bottom:1rem;color:#94a3b8;text-decoration:none;}
        .back-btn:hover{color:#38bdf8;}
        .info-item{margin-bottom:1rem;padding-bottom:1rem;border-bottom:1px solid #1e293b;}
        .label{color:#94a3b8;font-size:0.9rem;display:block;margin-bottom:0.3rem;}
        .value{font-size:1.1rem;}
    </style>
</head>
<body>
    <div class="container">
        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/dashboard" class="back-btn">← Volver al Panel</a>
        <h1>ℹ️ Información Corporativa</h1>
        
        <div class="card">
            <div class="info-item">
                <span class="label">Empresa</span>
                <div class="value"><?= htmlspecialchars($info['nombre']) ?></div>
            </div>
            <div class="info-item">
                <span class="label">Dirección Fiscal</span>
                <div class="value"><?= htmlspecialchars($info['direccion']) ?></div>
            </div>
            <div class="info-item">
                <span class="label">Teléfono</span>
                <div class="value"><?= htmlspecialchars($info['telefono']) ?></div>
            </div>
            <div class="info-item">
                <span class="label">Email de Administración</span>
                <div class="value"><?= htmlspecialchars($info['email']) ?></div>
            </div>
        </div>
    </div>
</body>
</html>

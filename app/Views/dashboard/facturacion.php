<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Facturación - Consultora</title>
    <style>
        body{font-family:system-ui;background:#020617;color:#e5e7eb;padding:2rem;}
        .container{max-width:1200px;margin:0 auto;}
        h1{color:#38bdf8;}
        .card{background:#0f172a;padding:2rem;border-radius:12px;border:1px solid #1e293b;text-align:center;}
        .back-btn{display:inline-block;margin-bottom:1rem;color:#94a3b8;text-decoration:none;}
        .back-btn:hover{color:#38bdf8;}
    </style>
</head>
<body>
    <div class="container">
        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/dashboard" class="back-btn">← Volver al Panel</a>
        <h1>💳 Gestión de Facturación</h1>
        <div class="card">
            <p>El módulo de facturación está en construcción.</p>
            <p style="color:#94a3b8;margin-top:1rem;">Aquí podrá generar facturas fiscales y gestionar cobros a empresas.</p>
        </div>
    </div>
</body>
</html>

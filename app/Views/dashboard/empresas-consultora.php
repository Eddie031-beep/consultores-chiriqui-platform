<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Empresas - Consultora</title>
    <style>
        body{font-family:system-ui;background:#020617;color:#e5e7eb;padding:2rem;}
        .container{max-width:1200px;margin:0 auto;}
        h1{color:#38bdf8;margin-bottom:2rem;}
        .back-btn{display:inline-block;margin-bottom:1rem;color:#94a3b8;text-decoration:none;}
        .back-btn:hover{color:#38bdf8;}
        .btn-create{background:#2563eb;color:white;padding:10px 20px;text-decoration:none;border-radius:8px;float:right;}
        
        table {width: 100%; border-collapse: collapse; background: #1e293b; border-radius: 12px; overflow: hidden; margin-top: 1rem;}
        th, td {padding: 15px; text-align: left; border-bottom: 1px solid #334155;}
        th {background: #0f172a; color: #94a3b8; font-weight: 600;}
        tr:last-child td {border-bottom: none;}
        .action-link {color: #38bdf8; text-decoration: none; margin-right: 10px;}
        .status-active {color: #4ade80;}
        .status-inactive {color: #f87171;}
    </style>
</head>
<body>
    <div class="container">
        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/dashboard" class="back-btn">← Volver al Panel</a>
        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/empresas/crear" class="btn-create">+ Nueva Empresa</a>
        
        <h1>🏢 Gestión de Empresas</h1>
        
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>RUC</th>
                    <th>Sector</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($empresas)): ?>
                    <tr><td colspan="5" style="text-align:center;">No hay empresas registradas.</td></tr>
                <?php else: ?>
                    <?php foreach($empresas as $emp): ?>
                    <tr>
                        <td><?= htmlspecialchars($emp['nombre']) ?></td>
                        <td><?= htmlspecialchars($emp['ruc'] . '-' . $emp['dv']) ?></td>
                        <td><?= htmlspecialchars($emp['sector']) ?></td>
                        <td class="<?= $emp['estado'] === 'activa' ? 'status-active' : 'status-inactive' ?>">
                            <?= ucfirst($emp['estado']) ?>
                        </td>
                        <td>
                            <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/empresas/<?= $emp['id'] ?>/editar" class="action-link">Editar</a>
                            <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/contratos/<?= $emp['id'] ?>" class="action-link">Contrato</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

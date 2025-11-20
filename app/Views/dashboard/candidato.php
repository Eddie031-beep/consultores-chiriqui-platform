<?php
use App\Helpers\Auth;

$user = Auth::user();
$postulaciones = $postulaciones ?? [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Candidato | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2em;
            margin-bottom: 10px;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .card-numero {
            font-size: 2.5em;
            font-weight: 700;
            color: #667eea;
        }

        .card-titulo {
            color: #666;
            margin-top: 10px;
        }

        .postulaciones {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .postulaciones h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .postulacion-item {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .postulacion-item:last-child {
            border-bottom: none;
        }

        .postulacion-info h3 {
            color: #333;
            margin-bottom: 5px;
        }

        .postulacion-empresa {
            color: #667eea;
            font-size: 0.9em;
            margin-bottom: 8px;
        }

        .postulacion-fecha {
            color: #999;
            font-size: 0.85em;
        }

        .badge-estado {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }

        .badge-pendiente {
            background: #fff3cd;
            color: #856404;
        }

        .badge-revisado {
            background: #cfe2ff;
            color: #084298;
        }

        .badge-aceptado {
            background: #d1e7dd;
            color: #0f5132;
        }

        .badge-rechazado {
            background: #f8d7da;
            color: #842029;
        }

        .btn-explorar {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
            transition: background 0.3s;
        }

        .btn-explorar:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <?php include '../components/navbar.php'; ?>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>👋 ¡Bienvenido, <?= htmlspecialchars($user['nombre']) ?>!</h1>
            <p>Panel de control de candidato</p>
        </div>

        <!-- Stats -->
        <div class="cards-grid">
            <div class="card">
                <div class="card-numero"><?= count($postulaciones) ?></div>
                <div class="card-titulo">Postulaciones Enviadas</div>
            </div>

            <div class="card">
                <div class="card-numero">
                    <?= count(array_filter($postulaciones, fn($p) => $p['estado'] === 'aceptado')) ?>
                </div>
                <div class="card-titulo">Aceptadas</div>
            </div>

            <div class="card">
                <div class="card-numero">
                    <?= count(array_filter($postulaciones, fn($p) => $p['estado'] === 'pendiente')) ?>
                </div>
                <div class="card-titulo">En Revisión</div>
            </div>
        </div>

        <!-- Postulaciones -->
        <div class="postulaciones">
            <h2>📋 Mis Postulaciones Recientes</h2>

            <?php if (empty($postulaciones)): ?>
                <p style="text-align: center; color: #999; padding: 40px;">
                    Aún no has realizado postulaciones. 
                    <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes" class="btn-explorar">Explorar vacantes disponibles</a>
                </p>
            <?php else: ?>
                <?php foreach ($postulaciones as $post): ?>
                    <div class="postulacion-item">
                        <div class="postulacion-info">
                            <div class="postulacion-empresa"><?= htmlspecialchars($post['empresa_nombre']) ?></div>
                            <h3><?= htmlspecialchars($post['titulo']) ?></h3>
                            <div class="postulacion-fecha">
                                📅 <?= date('d/m/Y H:i', strtotime($post['fecha_postulacion'])) ?>
                            </div>
                        </div>
                        <span class="badge-estado badge-<?= htmlspecialchars($post['estado']) ?>">
                            <?= ucfirst(htmlspecialchars($post['estado'])) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
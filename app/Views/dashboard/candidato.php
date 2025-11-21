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
            background: var(--bg-primary);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* ===== HEADER ===== */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            padding: 40px 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }

        [data-theme="dark"] .header {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        }

        .header h1 {
            font-size: 2em;
            margin-bottom: 10px;
            color: white !important;
        }

        .header p {
            color: rgba(255, 255, 255, 0.9) !important;
            font-size: 1.1em;
        }

        /* ===== CARDS GRID ===== */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            background: var(--bg-card);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px var(--shadow-color);
            text-align: center;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px var(--shadow-color);
        }

        [data-theme="dark"] .card {
            background: var(--bg-card);
            border: 1px solid #334155;
        }

        .card-numero {
            font-size: 2.5em;
            font-weight: 700;
            color: #667eea;
        }

        [data-theme="dark"] .card-numero {
            color: #818cf8;
        }

        .card-titulo {
            color: var(--text-secondary);
            margin-top: 10px;
            font-weight: 600;
        }

        /* ===== POSTULACIONES ===== */
        .postulaciones {
            background: var(--bg-card);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px var(--shadow-color);
            border: 1px solid var(--border-color);
        }

        [data-theme="dark"] .postulaciones {
            background: var(--bg-card);
            border: 1px solid #334155;
        }

        .postulaciones h2 {
            margin-bottom: 20px;
            color: var(--text-heading);
        }

        .postulacion-item {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.2s ease;
        }

        .postulacion-item:hover {
            background: var(--bg-secondary);
        }

        [data-theme="dark"] .postulacion-item:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .postulacion-item:last-child {
            border-bottom: none;
        }

        .postulacion-info h3 {
            color: var(--text-heading);
            margin-bottom: 5px;
            font-size: 1.1em;
        }

        .postulacion-empresa {
            color: #667eea;
            font-size: 0.9em;
            margin-bottom: 8px;
            font-weight: 600;
        }

        [data-theme="dark"] .postulacion-empresa {
            color: #818cf8;
        }

        .postulacion-fecha {
            color: var(--text-secondary);
            font-size: 0.85em;
        }

        /* ===== BADGES ===== */
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

        /* Badges en modo oscuro */
        [data-theme="dark"] .badge-pendiente {
            background: rgba(251, 191, 36, 0.2);
            color: #fcd34d;
        }

        [data-theme="dark"] .badge-revisado {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
        }

        [data-theme="dark"] .badge-aceptado {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
        }

        [data-theme="dark"] .badge-rechazado {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
        }

        /* ===== BOTÓN EXPLORAR ===== */
        .btn-explorar {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            padding: 12px 25px;
            border-radius: 50px;
            text-decoration: none;
            margin-top: 20px;
            transition: all 0.3s ease;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .btn-explorar:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        /* Mensaje sin postulaciones */
        .sin-postulaciones {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }

        .sin-postulaciones p {
            font-size: 1.1em;
            margin-bottom: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.5em;
            }

            .cards-grid {
                grid-template-columns: 1fr;
            }

            .postulacion-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <?php include __DIR__ . '/../components/navbar.php'; ?>

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
                <div class="sin-postulaciones">
                    <p>Aún no has realizado postulaciones.</p>
                    <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes" class="btn-explorar">
                        🔍 Explorar vacantes disponibles
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($postulaciones as $post): ?>
                    <div class="postulacion-item">
                        <div class="postulacion-info">
                            <div class="postulacion-empresa">
                                <?= htmlspecialchars($post['empresa_nombre']) ?>
                            </div>
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
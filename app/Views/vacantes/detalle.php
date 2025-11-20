<?php
$vacante = $vacante ?? [];
$isAuthenticated = isset($_SESSION['user']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($vacante['titulo']) ?> | Consultores Chiriquí</title>
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
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        .nav-top {
            background: white;
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .nav-top a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .vacante-container {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }

        .vacante-header {
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 30px;
            margin-bottom: 30px;
        }

        .vacante-empresa {
            color: #667eea;
            font-weight: 600;
            font-size: 0.95em;
            margin-bottom: 10px;
        }

        .vacante-titulo {
            font-size: 2.5em;
            font-weight: 700;
            color: #333;
            margin: 15px 0;
        }

        .badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }

        .badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }

        .badge-modalidad {
            background: #e3f2fd;
            color: #1976d2;
        }

        .vacante-info-rapida {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-item-valor {
            font-weight: 600;
            color: #333;
        }

        .section {
            margin-bottom: 35px;
        }

        .section-titulo {
            font-size: 1.4em;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
            padding-left: 15px;
        }

        .section-contenido {
            color: #666;
            line-height: 1.8;
            font-size: 1em;
        }

        .empresa-info {
            background: #f9f9f9;
            padding: 25px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        .acciones {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #f0f0f0;
        }

        .btn-postular {
            flex: 1;
            background: #4caf50;
            color: white;
            padding: 15px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1.05em;
            text-decoration: none;
            text-align: center;
            transition: background 0.3s;
        }

        .btn-postular:hover {
            background: #45a049;
        }

        .btn-volver {
            flex: 1;
            background: #667eea;
            color: white;
            padding: 15px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1.05em;
            text-decoration: none;
            text-align: center;
            transition: background 0.3s;
        }

        .btn-volver:hover {
            background: #5568d3;
        }

        .alerta {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: #856404;
        }

        @media (max-width: 768px) {
            .vacante-titulo {
                font-size: 1.8em;
            }

            .acciones {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <?php include '../components/navbar.php'; ?>

    <div class="container">
        <!-- Nav superior -->
        <div class="nav-top">
            <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes">← Volver al listado</a>
            <a href="<?= ENV_APP['BASE_URL'] ?>/auth">Iniciar Sesión</a>
        </div>

        <!-- Contenido principal -->
        <div class="vacante-container">
            <!-- Header -->
            <div class="vacante-header">
                <div class="vacante-empresa"><?= htmlspecialchars($vacante['empresa_nombre']) ?></div>
                <h1 class="vacante-titulo"><?= htmlspecialchars($vacante['titulo']) ?></h1>

                <div class="badges">
                    <span class="badge badge-modalidad">
                        <?php 
                            $mods = ['presencial' => '🏢 Presencial', 'remoto' => '🏠 Remoto', 'hibrido' => '🔄 Híbrido'];
                            echo $mods[$vacante['modalidad']] ?? $vacante['modalidad'];
                        ?>
                    </span>
                </div>

                <div class="vacante-info-rapida">
                    <div class="info-item">
                        <span>📍</span>
                        <div class="info-item-valor"><?= htmlspecialchars($vacante['ubicacion']) ?></div>
                    </div>

                    <?php if ($vacante['salario_min']): ?>
                        <div class="info-item">
                            <span>💰</span>
                            <div class="info-item-valor">B/. <?= number_format($vacante['salario_min'], 2) ?> - B/. <?= number_format($vacante['salario_max'], 2) ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="info-item">
                        <span>📅</span>
                        <div class="info-item-valor"><?= date('d/m/Y', strtotime($vacante['fecha_publicacion'])) ?></div>
                    </div>
                </div>
            </div>

            <!-- Alerta de registro -->
            <div class="alerta">
                <strong>⚠️ Nota importante:</strong> Para postularte a esta vacante deberás registrarte en la plataforma. El registro es rápido y obligatorio.
            </div>

            <!-- Descripción -->
            <div class="section">
                <h2 class="section-titulo">📋 Descripción de la Vacante</h2>
                <div class="section-contenido">
                    <?= nl2br(htmlspecialchars($vacante['descripcion'])) ?>
                </div>
            </div>

            <!-- Información de la Empresa -->
            <div class="section">
                <h2 class="section-titulo">🏢 Información de la Empresa</h2>
                <div class="empresa-info">
                    <h3><?= htmlspecialchars($vacante['empresa_nombre']) ?></h3>
                    
                    <?php if ($vacante['sector']): ?>
                        <p><strong>📊 Sector:</strong> <?= htmlspecialchars($vacante['sector']) ?></p>
                    <?php endif; ?>

                    <?php if ($vacante['telefono']): ?>
                        <p><strong>📞 Teléfono:</strong> <a href="tel:<?= htmlspecialchars($vacante['telefono']) ?>" style="color: #667eea; text-decoration: none;"><?= htmlspecialchars($vacante['telefono']) ?></a></p>
                    <?php endif; ?>

                    <?php if ($vacante['email_contacto']): ?>
                        <p><strong>📧 Email:</strong> <a href="mailto:<?= htmlspecialchars($vacante['email_contacto']) ?>" style="color: #667eea; text-decoration: none;"><?= htmlspecialchars($vacante['email_contacto']) ?></a></p>
                    <?php endif; ?>

                    <?php if ($vacante['sitio_web']): ?>
                        <p><strong>🌐 Sitio Web:</strong> <a href="<?= htmlspecialchars($vacante['sitio_web']) ?>" target="_blank" style="color: #667eea; text-decoration: none;"><?= htmlspecialchars($vacante['sitio_web']) ?></a></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Acciones -->
            <div class="acciones">
                <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes" class="btn-volver">← Volver al Listado</a>
                <a href="<?= ENV_APP['BASE_URL'] ?>/postular/<?= $vacante['id'] ?>" class="btn-postular">✓ Postularme</a>
            </div>
        </div>
    </div>
</body>
</html>
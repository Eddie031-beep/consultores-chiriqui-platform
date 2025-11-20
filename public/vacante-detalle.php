<?php
session_start();
require_once '../config/database.php';

// Validar que el ID esté presente
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: vacantes.php');
    exit;
}

$vacante_id = (int)$_GET['id'];

// Obtener detalle de la vacante
$stmt = $pdo->prepare("
    SELECT v.*, e.nombre as empresa_nombre, e.sector, e.telefono, e.email_contacto, e.sitio_web
    FROM vacantes v
    JOIN empresas e ON v.empresa_id = e.id
    WHERE v.id = ? AND v.estado = 'abierta'
");
$stmt->execute([$vacante_id]);
$vacante = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no existe, redirigir
if(!$vacante) {
    header('Location: vacantes.php');
    exit;
}

// Registrar interacción (vista de detalle)
$stmt_interaccion = $pdo->prepare("
    INSERT INTO interacciones_vacante (vacante_id, tipo_interaccion, origen, ip, session_id)
    VALUES (?, 'ver_detalle', 'web', ?, ?)
");
$stmt_interaccion->execute([$vacante_id, $_SERVER['REMOTE_ADDR'], session_id()]);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($vacante['titulo']); ?> - Consultores Chiriquí</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
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
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .nav-top a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        .nav-top a:hover {
            color: #5568d3;
        }
        .vacante-container {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
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
        .info-item-icono {
            font-size: 1.5em;
        }
        .info-item-contenido {
            display: flex;
            flex-direction: column;
        }
        .info-item-label {
            color: #999;
            font-size: 0.85em;
            font-weight: 600;
        }
        .info-item-valor {
            color: #333;
            font-size: 1em;
            font-weight: 600;
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
        .empresa-info h3 {
            margin-top: 0;
            color: #333;
        }
        .empresa-info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .empresa-info-item:last-child {
            margin-bottom: 0;
        }
        .badges-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
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
        .badge-tipo {
            background: #f3e5f5;
            color: #7b1fa2;
        }
        .badge-sector {
            background: #e8f5e9;
            color: #388e3c;
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
            transition: background 0.3s;
            text-decoration: none;
            text-align: center;
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
            transition: background 0.3s;
            text-decoration: none;
            text-align: center;
        }
        .btn-volver:hover {
            background: #5568d3;
        }
        .alerta-registro {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            color: #856404;
        }
        .alerta-registro strong {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Navegación superior -->
        <div class="nav-top">
            <a href="vacantes.php">← Volver al listado de vacantes</a>
            <div>
                <a href="../auth/login.php">Iniciar Sesión</a>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="vacante-container">
            <!-- Header -->
            <div class="vacante-header">
                <div class="vacante-empresa">
                    <?php echo htmlspecialchars($vacante['empresa_nombre']); ?>
                </div>
                <h1 class="vacante-titulo">
                    <?php echo htmlspecialchars($vacante['titulo']); ?>
                </h1>

                <div class="badges-group">
                    <span class="badge badge-modalidad">
                        <?php 
                            $modalidades = ['presencial' => '🏢 Presencial', 'remoto' => '🏠 Remoto', 'hibrido' => '🔄 Híbrido'];
                            echo $modalidades[$vacante['modalidad']] ?? $vacante['modalidad'];
                        ?>
                    </span>
                    <span class="badge badge-tipo">
                        <?php echo htmlspecialchars($vacante['tipo_contrato']); ?>
                    </span>
                    <?php if($vacante['sector']): ?>
                        <span class="badge badge-sector">
                            📊 <?php echo htmlspecialchars($vacante['sector']); ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="vacante-info-rapida">
                    <div class="info-item">
                        <div class="info-item-icono">📍</div>
                        <div class="info-item-contenido">
                            <div class="info-item-label">Ubicación</div>
                            <div class="info-item-valor"><?php echo htmlspecialchars($vacante['ubicacion']); ?></div>
                        </div>
                    </div>

                    <?php if($vacante['salario_min'] && $vacante['salario_max']): ?>
                        <div class="info-item">
                            <div class="info-item-icono">💰</div>
                            <div class="info-item-contenido">
                                <div class="info-item-label">Salario</div>
                                <div class="info-item-valor">
                                    B/. <?php echo number_format($vacante['salario_min'], 2); ?> - 
                                    B/. <?php echo number_format($vacante['salario_max'], 2); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="info-item">
                        <div class="info-item-icono">📅</div>
                        <div class="info-item-contenido">
                            <div class="info-item-label">Publicada</div>
                            <div class="info-item-valor">
                                <?php echo date('d/m/Y', strtotime($vacante['fecha_publicacion'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerta de registro -->
            <div class="alerta-registro">
                <strong>⚠️ Nota importante:</strong> Para postularte a esta vacante deberás registrarte en la plataforma. El registro es rápido y obligatorio para poder aplicar a cualquier posición.
            </div>

            <!-- Descripción -->
            <div class="section">
                <h2 class="section-titulo">📋 Descripción de la Vacante</h2>
                <div class="section-contenido">
                    <?php echo nl2br(htmlspecialchars($vacante['descripcion'])); ?>
                </div>
            </div>

            <!-- Información de la Empresa -->
            <div class="section">
                <h2 class="section-titulo">🏢 Información de la Empresa</h2>
                <div class="empresa-info">
                    <h3><?php echo htmlspecialchars($vacante['empresa_nombre']); ?></h3>
                    
                    <div class="empresa-info-item">
                        <span>📊 Sector:</span>
                        <strong><?php echo htmlspecialchars($vacante['sector'] ?? 'No especificado'); ?></strong>
                    </div>

                    <?php if($vacante['telefono']): ?>
                        <div class="empresa-info-item">
                            <span>📞 Teléfono:</span>
                            <a href="tel:<?php echo htmlspecialchars($vacante['telefono']); ?>" style="color: #667eea; text-decoration: none;">
                                <?php echo htmlspecialchars($vacante['telefono']); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if($vacante['email_contacto']): ?>
                        <div class="empresa-info-item">
                            <span>📧 Email:</span>
                            <a href="mailto:<?php echo htmlspecialchars($vacante['email_contacto']); ?>" style="color: #667eea; text-decoration: none;">
                                <?php echo htmlspecialchars($vacante['email_contacto']); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if($vacante['sitio_web']): ?>
                        <div class="empresa-info-item">
                            <span>🌐 Sitio Web:</span>
                            <a href="<?php echo htmlspecialchars($vacante['sitio_web']); ?>" target="_blank" style="color: #667eea; text-decoration: none;">
                                <?php echo htmlspecialchars($vacante['sitio_web']); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Acciones -->
            <div class="acciones">
                <a href="vacantes.php" class="btn-volver">← Volver al Listado</a>
                <a href="../auth/postular.php?vacante_id=<?php echo $vacante['id']; ?>" class="btn-postular">
                    ✓ Postularme a esta Vacante
                </a>
            </div>
        </div>
    </div>
</body>
</html>
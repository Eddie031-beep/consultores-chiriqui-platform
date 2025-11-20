<?php
// Obtener vacantes disponibles
$vacantes = [];
try {
    $db = db_connect('local');
    $busqueda = $_GET['busqueda'] ?? '';
    $modalidad = $_GET['modalidad'] ?? '';
    $ubicacion = $_GET['ubicacion'] ?? '';

    $sql = "SELECT v.*, e.nombre as empresa_nombre FROM vacantes v 
            JOIN empresas e ON v.empresa_id = e.id 
            WHERE v.estado = 'abierta'";
    
    if (!empty($busqueda)) {
        $sql .= " AND (v.titulo LIKE '%' || ? || '%' OR v.descripcion LIKE '%' || ? || '%')";
    }
    if (!empty($modalidad)) {
        $sql .= " AND v.modalidad = ?";
    }
    if (!empty($ubicacion)) {
        $sql .= " AND v.ubicacion LIKE '%' || ? || '%'";
    }

    $sql .= " ORDER BY v.fecha_publicacion DESC LIMIT 12";

    $stmt = $db->prepare($sql);
    $params = [];
    if (!empty($busqueda)) $params = array_merge($params, [$busqueda, $busqueda]);
    if (!empty($modalidad)) $params[] = $modalidad;
    if (!empty($ubicacion)) $params[] = $ubicacion;

    $stmt->execute($params);
    $vacantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacantes - Consultores Chiriquí</title>
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

        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 20px;
            text-align: center;
        }

        .hero h1 {
            font-size: 3em;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.2em;
            margin-bottom: 30px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 50px 20px;
        }

        .filtros {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .filtros-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .filtro-grupo {
            display: flex;
            flex-direction: column;
        }

        .filtro-grupo label {
            font-weight: 600;
            margin-bottom: 8px;
        }

        .filtro-grupo input,
        .filtro-grupo select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .btn-buscar {
            background: #667eea;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-buscar:hover {
            background: #5568d3;
        }

        .vacantes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
        }

        .vacante-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .vacante-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .vacante-empresa {
            color: #667eea;
            font-weight: 600;
            font-size: 0.85em;
            margin-bottom: 10px;
        }

        .vacante-titulo {
            font-size: 1.3em;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            margin-right: 8px;
            margin-bottom: 15px;
        }

        .badge-modalidad {
            background: #e3f2fd;
            color: #1976d2;
        }

        .vacante-info {
            color: #666;
            font-size: 0.95em;
            margin-bottom: 15px;
        }

        .vacante-acciones {
            display: flex;
            gap: 10px;
        }

        .btn-detalle,
        .btn-postular {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
        }

        .btn-detalle {
            background: #667eea;
            color: white;
        }

        .btn-postular {
            background: #4caf50;
            color: white;
        }

        .btn-detalle:hover {
            background: #5568d3;
        }

        .btn-postular:hover {
            background: #45a049;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2em;
            }

            .vacantes-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <?php include 'components/navbar.php'; ?>

    <!-- HERO -->
    <section class="hero">
        <h1>🌟 Encuentra Tu Próxima Oportunidad</h1>
        <p>Explora cientos de vacantes en empresas líderes de Panamá</p>
    </section>

    <div class="container">
        <!-- FILTROS -->
        <div class="filtros">
            <h3>🔍 Filtrar Vacantes</h3>
            <form method="GET">
                <div class="filtros-grid">
                    <div class="filtro-grupo">
                        <label>Buscar</label>
                        <input type="text" name="busqueda" value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>">
                    </div>
                    <div class="filtro-grupo">
                        <label>Modalidad</label>
                        <select name="modalidad">
                            <option value="">Todas</option>
                            <option value="presencial" <?= ($_GET['modalidad'] ?? '') === 'presencial' ? 'selected' : '' ?>>Presencial</option>
                            <option value="remoto" <?= ($_GET['modalidad'] ?? '') === 'remoto' ? 'selected' : '' ?>>Remoto</option>
                            <option value="hibrido" <?= ($_GET['modalidad'] ?? '') === 'hibrido' ? 'selected' : '' ?>>Híbrido</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-buscar">🔍 Buscar</button>
            </form>
        </div>

        <!-- VACANTES -->
        <div class="vacantes-grid">
            <?php foreach ($vacantes as $vacante): ?>
                <div class="vacante-card">
                    <div class="vacante-empresa"><?= htmlspecialchars($vacante['empresa_nombre']) ?></div>
                    <h2 class="vacante-titulo"><?= htmlspecialchars($vacante['titulo']) ?></h2>
                    
                    <span class="badge badge-modalidad"><?= ucfirst($vacante['modalidad']) ?></span>
                    
                    <div class="vacante-info">
                        📍 <?= htmlspecialchars($vacante['ubicacion']) ?><br>
                        <?php if ($vacante['salario_min']): ?>
                            💰 B/. <?= number_format($vacante['salario_min'], 2) ?> - B/. <?= number_format($vacante['salario_max'], 2) ?>
                        <?php endif; ?>
                    </div>

                    <div class="vacante-acciones">
                        <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes/<?= htmlspecialchars($vacante['slug']) ?>" class="btn-detalle">Ver Detalles</a>
                        <a href="<?= ENV_APP['BASE_URL'] ?>/postular/<?= $vacante['id'] ?>" class="btn-postular">Postularme</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
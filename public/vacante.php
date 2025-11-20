<?php
session_start();
require_once '../config/database.php';

// Obtener filtros
$filtro_empresa = isset($_GET['empresa']) ? $_GET['empresa'] : '';
$filtro_ubicacion = isset($_GET['ubicacion']) ? $_GET['ubicacion'] : '';
$filtro_modalidad = isset($_GET['modalidad']) ? $_GET['modalidad'] : '';
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';

// Construir query base
$query = "SELECT v.*, e.nombre as empresa_nombre, e.sector 
          FROM vacantes v 
          JOIN empresas e ON v.empresa_id = e.id 
          WHERE v.estado = 'abierta'";

$params = [];

// Aplicar filtros
if (!empty($filtro_empresa)) {
    $query .= " AND v.empresa_id = ?";
    $params[] = $filtro_empresa;
}

if (!empty($filtro_ubicacion)) {
    $query .= " AND v.ubicacion LIKE ?";
    $params[] = "%$filtro_ubicacion%";
}

if (!empty($filtro_modalidad)) {
    $query .= " AND v.modalidad = ?";
    $params[] = $filtro_modalidad;
}

if (!empty($busqueda)) {
    $query .= " AND (v.titulo LIKE ? OR v.descripcion LIKE ?)";
    $params[] = "%$busqueda%";
    $params[] = "%$busqueda%";
}

$query .= " ORDER BY v.fecha_publicacion DESC";

// Ejecutar query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$vacantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener listados para filtros
$stmt_empresas = $pdo->query("SELECT DISTINCT id, nombre FROM empresas WHERE estado = 'activa' ORDER BY nombre");
$empresas = $stmt_empresas->fetchAll(PDO::FETCH_ASSOC);

$stmt_ubicaciones = $pdo->query("SELECT DISTINCT ubicacion FROM vacantes WHERE estado = 'abierta' ORDER BY ubicacion");
$ubicaciones = $stmt_ubicaciones->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacantes Disponibles - Consultores Chiriquí</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header-vacantes {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        .header-vacantes h1 {
            margin: 0;
            font-size: 2.5em;
        }
        .header-vacantes p {
            margin: 10px 0 0 0;
            font-size: 1.1em;
            opacity: 0.9;
        }
        .filtros {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .filtros h3 {
            margin-top: 0;
            color: #333;
        }
        .filtros-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        .filtro-grupo {
            display: flex;
            flex-direction: column;
        }
        .filtro-grupo label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #555;
            font-size: 0.9em;
        }
        .filtro-grupo input,
        .filtro-grupo select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.95em;
        }
        .filtro-grupo input:focus,
        .filtro-grupo select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }
        .btn-buscar {
            background: #667eea;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95em;
            transition: background 0.3s;
        }
        .btn-buscar:hover {
            background: #5568d3;
        }
        .btn-limpiar {
            background: #999;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95em;
            margin-left: 10px;
            transition: background 0.3s;
        }
        .btn-limpiar:hover {
            background: #777;
        }
        .vacantes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }
        .vacante-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
        }
        .vacante-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        }
        .vacante-header {
            margin-bottom: 15px;
        }
        .vacante-empresa {
            color: #667eea;
            font-weight: 600;
            font-size: 0.85em;
            margin-bottom: 5px;
        }
        .vacante-titulo {
            font-size: 1.3em;
            font-weight: 700;
            color: #333;
            margin: 10px 0;
            word-wrap: break-word;
        }
        .vacante-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 15px;
        }
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8em;
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
        .vacante-ubicacion {
            color: #666;
            font-size: 0.95em;
            margin-bottom: 10px;
        }
        .vacante-ubicacion::before {
            content: "📍 ";
        }
        .vacante-salario {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 0.95em;
        }
        .vacante-salario strong {
            color: #667eea;
        }
        .vacante-descripcion {
            color: #666;
            font-size: 0.95em;
            margin-bottom: 15px;
            flex-grow: 1;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .vacante-acciones {
            display: flex;
            gap: 10px;
            margin-top: auto;
        }
        .btn-detalles {
            flex: 1;
            background: #667eea;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
            text-decoration: none;
            text-align: center;
        }
        .btn-detalles:hover {
            background: #5568d3;
        }
        .btn-postular {
            flex: 1;
            background: #4caf50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
            text-decoration: none;
            text-align: center;
        }
        .btn-postular:hover {
            background: #45a049;
        }
        .sin-resultados {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px 20px;
            background: white;
            border-radius: 10px;
            color: #666;
        }
        .sin-resultados h3 {
            margin-top: 0;
            color: #333;
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
        .contador-vacantes {
            color: #666;
            font-size: 0.95em;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Navegación superior -->
        <div class="nav-top">
            <div>
                <strong>Consultores Chiriquí</strong> - Portal de Empleo
            </div>
            <div>
                <a href="../auth/login.php">Iniciar Sesión</a>
            </div>
        </div>

        <!-- Header -->
        <div class="header-vacantes">
            <h1>🌟 Vacantes Disponibles</h1>
            <p>Explora todas las oportunidades laborales que tenemos para ti</p>
        </div>

        <!-- Filtros -->
        <div class="filtros">
            <h3>🔍 Filtrar Vacantes</h3>
            <form method="GET" action="">
                <div class="filtros-grid">
                    <div class="filtro-grupo">
                        <label for="busqueda">Buscar por título o descripción</label>
                        <input type="text" id="busqueda" name="busqueda" 
                               value="<?php echo htmlspecialchars($busqueda); ?>" 
                               placeholder="Ej: Desarrollador, Analista...">
                    </div>

                    <div class="filtro-grupo">
                        <label for="empresa">Empresa</label>
                        <select id="empresa" name="empresa">
                            <option value="">Todas las empresas</option>
                            <?php foreach($empresas as $emp): ?>
                                <option value="<?php echo $emp['id']; ?>" 
                                    <?php echo $filtro_empresa == $emp['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($emp['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filtro-grupo">
                        <label for="ubicacion">Ubicación</label>
                        <select id="ubicacion" name="ubicacion">
                            <option value="">Todas las ubicaciones</option>
                            <?php foreach($ubicaciones as $ubi): ?>
                                <option value="<?php echo htmlspecialchars($ubi['ubicacion']); ?>" 
                                    <?php echo $filtro_ubicacion == $ubi['ubicacion'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ubi['ubicacion']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filtro-grupo">
                        <label for="modalidad">Modalidad</label>
                        <select id="modalidad" name="modalidad">
                            <option value="">Todas las modalidades</option>
                            <option value="presencial" <?php echo $filtro_modalidad == 'presencial' ? 'selected' : ''; ?>>
                                Presencial
                            </option>
                            <option value="remoto" <?php echo $filtro_modalidad == 'remoto' ? 'selected' : ''; ?>>
                                Remoto
                            </option>
                            <option value="hibrido" <?php echo $filtro_modalidad == 'hibrido' ? 'selected' : ''; ?>>
                                Híbrido
                            </option>
                        </select>
                    </div>
                </div>

                <div style="margin-top: 15px;">
                    <button type="submit" class="btn-buscar">🔍 Buscar</button>
                    <a href="vacantes.php" class="btn-limpiar">✕ Limpiar Filtros</a>
                </div>
            </form>
        </div>

        <!-- Contador -->
        <div class="contador-vacantes">
            Mostrando <strong><?php echo count($vacantes); ?></strong> vacante(s) disponible(s)
        </div>

        <!-- Listado de Vacantes -->
        <div class="vacantes-grid" style="margin-top: 20px;">
            <?php if(empty($vacantes)): ?>
                <div class="sin-resultados">
                    <h3>No se encontraron vacantes</h3>
                    <p>Intenta con otros criterios de búsqueda o regresa más tarde para nuevas oportunidades.</p>
                    <a href="vacantes.php" class="btn-buscar" style="display: inline-block; margin-top: 15px;">
                        Ver Todas las Vacantes
                    </a>
                </div>
            <?php else: ?>
                <?php foreach($vacantes as $vacante): ?>
                    <div class="vacante-card">
                        <div class="vacante-header">
                            <div class="vacante-empresa">
                                <?php echo htmlspecialchars($vacante['empresa_nombre']); ?>
                            </div>
                            <h2 class="vacante-titulo">
                                <?php echo htmlspecialchars($vacante['titulo']); ?>
                            </h2>
                        </div>

                        <div class="vacante-badges">
                            <span class="badge badge-modalidad">
                                <?php 
                                    $modalidades = ['presencial' => '🏢 Presencial', 'remoto' => '🏠 Remoto', 'hibrido' => '🔄 Híbrido'];
                                    echo $modalidades[$vacante['modalidad']] ?? $vacante['modalidad'];
                                ?>
                            </span>
                            <span class="badge badge-tipo">
                                <?php echo htmlspecialchars($vacante['tipo_contrato']); ?>
                            </span>
                        </div>

                        <div class="vacante-ubicacion">
                            <?php echo htmlspecialchars($vacante['ubicacion']); ?>
                        </div>

                        <?php if($vacante['salario_min'] && $vacante['salario_max']): ?>
                            <div class="vacante-salario">
                                <strong>💰 Salario:</strong> B/. <?php echo number_format($vacante['salario_min'], 2); ?> - 
                                B/. <?php echo number_format($vacante['salario_max'], 2); ?>
                            </div>
                        <?php endif; ?>

                        <p class="vacante-descripcion">
                            <?php echo htmlspecialchars(substr($vacante['descripcion'], 0, 150)); ?>...
                        </p>

                        <div class="vacante-acciones">
                            <a href="vacante-detalle.php?id=<?php echo $vacante['id']; ?>&slug=<?php echo htmlspecialchars($vacante['slug']); ?>" 
                               class="btn-detalles">
                                Ver Detalles
                            </a>
                            <a href="../auth/postular.php?vacante_id=<?php echo $vacante['id']; ?>" 
                               class="btn-postular">
                                Postularme
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
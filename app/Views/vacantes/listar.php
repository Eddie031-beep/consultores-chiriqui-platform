<?php
$vacantes = $vacantes ?? [];
$empresas = $empresas ?? [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacantes Disponibles - Consultores Chiriquí</title>
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

        .filtros h3 {
            margin-bottom: 20px;
            color: #333;
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
            color: #333;
        }

        .filtro-grupo input,
        .filtro-grupo select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.95em;
        }

        .btn-buscar {
            background: #667eea;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1em;
        }

        .btn-buscar:hover {
            background: #5568d3;
        }

        .btn-limpiar {
            background: #6c757d;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            margin-left: 10px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-limpiar:hover {
            background: #5a6268;
        }

        .contador-vacantes {
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #666;
            font-size: 0.95em;
        }

        .contador-vacantes strong {
            color: #667eea;
            font-size: 1.1em;
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
            transition: transform 0.3s, box-shadow 0.3s;
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
            color: #333;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            margin-right: 8px;
            margin-bottom: 15px;
            font-weight: 600;
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

        .vacante-descripcion {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
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
            transition: background 0.3s;
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

        .sin-resultados {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
        }

        .sin-resultados h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.5em;
        }

        .sin-resultados p {
            color: #666;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2em;
            }

            .vacantes-grid {
                grid-template-columns: 1fr;
            }

            .filtros-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <!-- HERO -->
    <section class="hero">
        <h1>🌟 Encuentra Tu Próxima Oportunidad</h1>
        <p>Explora vacantes en empresas líderes de Panamá</p>
    </section>

    <div class="container">
        <!-- FILTROS -->
        <div class="filtros">
            <h3>🔍 Filtrar Vacantes</h3>
            <form method="GET" action="<?= ENV_APP['BASE_URL'] ?>/vacantes">
                <div class="filtros-grid">
                    <div class="filtro-grupo">
                        <label for="busqueda">Buscar</label>
                        <input 
                            type="text" 
                            id="busqueda" 
                            name="busqueda" 
                            value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>"
                            placeholder="Título o descripción..."
                        >
                    </div>

                    <div class="filtro-grupo">
                        <label for="modalidad">Modalidad</label>
                        <select id="modalidad" name="modalidad">
                            <option value="">Todas</option>
                            <option value="presencial" <?= ($_GET['modalidad'] ?? '') === 'presencial' ? 'selected' : '' ?>>
                                Presencial
                            </option>
                            <option value="remoto" <?= ($_GET['modalidad'] ?? '') === 'remoto' ? 'selected' : '' ?>>
                                Remoto
                            </option>
                            <option value="hibrido" <?= ($_GET['modalidad'] ?? '') === 'hibrido' ? 'selected' : '' ?>>
                                Híbrido
                            </option>
                        </select>
                    </div>

                    <div class="filtro-grupo">
                        <label for="ubicacion">Ubicación</label>
                        <input 
                            type="text" 
                            id="ubicacion" 
                            name="ubicacion" 
                            value="<?= htmlspecialchars($_GET['ubicacion'] ?? '') ?>"
                            placeholder="Ciudad o provincia..."
                        >
                    </div>

                    <?php if (!empty($empresas)): ?>
                    <div class="filtro-grupo">
                        <label for="empresa">Empresa</label>
                        <select id="empresa" name="empresa">
                            <option value="">Todas</option>
                            <?php foreach ($empresas as $empresa): ?>
                                <option value="<?= $empresa['id'] ?>" 
                                    <?= ($_GET['empresa'] ?? '') == $empresa['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($empresa['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>

                <div>
                    <button type="submit" class="btn-buscar">🔍 Buscar</button>
                    <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes" class="btn-limpiar">✕ Limpiar Filtros</a>
                </div>
            </form>
        </div>

        <!-- CONTADOR -->
        <div class="contador-vacantes">
            Mostrando <strong><?= count($vacantes) ?></strong> vacante(s) disponible(s)
        </div>

        <!-- VACANTES -->
        <div class="vacantes-grid">
            <?php if (empty($vacantes)): ?>
                <div class="sin-resultados" style="grid-column: 1 / -1;">
                    <h3>😔 No se encontraron vacantes</h3>
                    <p>Intenta con otros criterios de búsqueda o regresa más tarde para nuevas oportunidades.</p>
                    <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes" class="btn-buscar">
                        Ver Todas las Vacantes
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($vacantes as $vacante): ?>
                    <div class="vacante-card">
                        <div class="vacante-empresa">
                            <?= htmlspecialchars($vacante['empresa_nombre']) ?>
                        </div>
                        <h2 class="vacante-titulo">
                            <?= htmlspecialchars($vacante['titulo']) ?>
                        </h2>
                        
                        <div>
                            <span class="badge badge-modalidad">
                                <?php 
                                    $mods = [
                                        'presencial' => '🏢 Presencial',
                                        'remoto' => '🏠 Remoto',
                                        'hibrido' => '🔄 Híbrido'
                                    ];
                                    echo $mods[$vacante['modalidad']] ?? ucfirst($vacante['modalidad']);
                                ?>
                            </span>
                        </div>
                        
                        <div class="vacante-info">
                            📍 <?= htmlspecialchars($vacante['ubicacion']) ?>
                            <?php if ($vacante['salario_min']): ?>
                                <br>💰 B/. <?= number_format($vacante['salario_min'], 2) ?> 
                                - B/. <?= number_format($vacante['salario_max'], 2) ?>
                            <?php endif; ?>
                        </div>

                        <p class="vacante-descripcion">
                            <?= htmlspecialchars($vacante['descripcion']) ?>
                        </p>

                        <div class="vacante-acciones">
                            <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes/<?= htmlspecialchars($vacante['slug']) ?>" 
                               class="btn-detalle">
                                Ver Detalles
                            </a>
                            <a href="<?= ENV_APP['BASE_URL'] ?>/postular/<?= $vacante['id'] ?>" 
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
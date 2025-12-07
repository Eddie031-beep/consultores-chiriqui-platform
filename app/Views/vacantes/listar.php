<?php
$vacantes = $vacantes ?? [];
$empresas = $empresas ?? [];
?>

<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacantes Disponibles - Consultores Chiriquí</title>
    
    <!-- Shared Styles -->
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-animations.css">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/home-elegant.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <!-- NAVBAR -->
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <!-- HERO SECTION (Matches Home) -->
    <section class="hero-extended animate-fade-in" style="padding-bottom: 200px;"> <!-- Extra padding for overlap -->
        <div class="hero-bg-anim">
            <div class="floating-shape shape-1"></div>
            <div class="floating-shape shape-2"></div>
        </div>

        <div class="hero-content">
            <h1 class="hero-title animate-slide-up delay-100">
                Encuentra Tu Próxima Oportunidad
            </h1>
            <p class="hero-subtitle animate-slide-up delay-200">
                Explora vacantes en empresas líderes de Panamá
            </p>
        </div>
    </section>

    <!-- FILTERS (Overlapping Search Bar Style) -->
    <div class="container search-container-overlap animate-slide-up delay-300">
        <div class="glass-search-bar">
            <div class="search-title">
                Filtrar Vacantes
            </div>
            
            <form method="GET" action="<?= ENV_APP['BASE_URL'] ?>/vacantes">
                <!-- Grid Layout for Filters -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; width: 100%;">
                    
                    <div class="input-group">
                        <label class="input-label" for="busqueda">Buscar</label>
                        <input type="text" id="busqueda" name="busqueda" class="custom-input"
                               value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>"
                               placeholder="Título o descripción...">
                    </div>

                    <div class="input-group">
                        <label class="input-label" for="modalidad">Modalidad</label>
                        <select id="modalidad" name="modalidad" class="custom-input">
                            <option value="">Todas</option>
                            <option value="presencial" <?= ($_GET['modalidad'] ?? '') === 'presencial' ? 'selected' : '' ?>>Presencial</option>
                            <option value="remoto" <?= ($_GET['modalidad'] ?? '') === 'remoto' ? 'selected' : '' ?>>Remoto</option>
                            <option value="hibrido" <?= ($_GET['modalidad'] ?? '') === 'hibrido' ? 'selected' : '' ?>>Híbrido</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label class="input-label" for="ubicacion">Ubicación</label>
                        <input type="text" id="ubicacion" name="ubicacion" class="custom-input"
                               value="<?= htmlspecialchars($_GET['ubicacion'] ?? '') ?>"
                               placeholder="Ciudad o provincia...">
                    </div>

                    <?php if (!empty($empresas)): ?>
                    <div class="input-group">
                        <label class="input-label" for="empresa">Empresa</label>
                        <select id="empresa" name="empresa" class="custom-input">
                            <option value="">Todas</option>
                            <?php foreach ($empresas as $empresa): ?>
                                <option value="<?= $empresa['id'] ?>" <?= ($_GET['empresa'] ?? '') == $empresa['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($empresa['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="input-group" style="justify-content: flex-end;">
                        <label class="input-label">&nbsp;</label>
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="custom-btn" style="flex: 1;">Buscar</button>
                            <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes" class="btn-outline" style="display: grid; place-items: center; width: auto; padding: 0 20px;">
                                Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="container" style="margin-top: 50px;">
        
        <div style="margin-bottom: 30px; color: var(--text-muted);">
            Mostrando <strong><?= count($vacantes) ?></strong> vacante(s) disponible(s)
        </div>

        <div class="vacantes-grid-v2">
            <?php if (empty($vacantes)): ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: var(--bg-card); border-radius: 16px; border: 1px solid var(--border-color);">
                    <h3 style="font-size: 1.5rem; margin-bottom: 10px; color: var(--text-main);">No se encontraron vacantes</h3>
                    <p style="color: var(--text-muted); margin-bottom: 20px;">Intenta con otros criterios de búsqueda.</p>
                    <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes" class="custom-btn">Ver Todas</a>
                </div>
            <?php else: ?>
                <?php foreach ($vacantes as $index => $vacante): 
                    $delay = min(($index + 1) * 100, 500);
                ?>
                    <div class="vacancy-card-elegant animate-slide-up" style="animation-delay: <?= $delay ?>ms;">
                        <div class="card-header-v2">
                            <div class="company-name"><?= htmlspecialchars($vacante['empresa_nombre']) ?></div>
                            <h2 class="job-title"><?= htmlspecialchars($vacante['titulo']) ?></h2>
                        </div>
                        
                        <div class="tags-row" style="margin-bottom: 15px; gap: 8px;">
                            <span class="btn-outline" style="font-size: 0.8rem; padding: 4px 10px; flex: none;">
                                <?= ucfirst($vacante['modalidad']) ?>
                            </span>
                            <?php if (!empty($vacante['ubicacion'])): ?>
                                <span class="btn-outline" style="font-size: 0.8rem; padding: 4px 10px; flex: none;">
                                    <?= htmlspecialchars($vacante['ubicacion']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="vacante-info" style="margin-bottom: 20px; font-size: 0.9rem; color: var(--text-muted); line-height: 1.5; flex-grow: 1;">
                            <?= substr(strip_tags($vacante['descripcion']), 0, 100) ?>...
                        </div>

                        <div style="display: flex; gap: 10px; margin-top: auto;">
                            <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes/<?= htmlspecialchars($vacante['slug']) ?>" class="btn-outline">
                                Detalles
                            </a>
                            <a href="<?= ENV_APP['BASE_URL'] ?>/postular/<?= $vacante['id'] ?>" class="btn-fill">
                                Postularme
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Theme Toggle (Consistent) -->
    <button class="theme-toggle" id="themeToggle" title="Modo Oscuro/Claro">🌙</button>

    <script>
        const toggleBtn = document.getElementById('themeToggle');
        const html = document.documentElement;
        const savedTheme = localStorage.getItem('theme') || 'light';
        html.setAttribute('data-theme', savedTheme);
        updateIcon(savedTheme);

        toggleBtn.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            toggleBtn.style.transform = 'rotate(360deg)';
            setTimeout(() => toggleBtn.style.transform = 'none', 300);
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
        });

        function updateIcon(theme) {
            toggleBtn.textContent = theme === 'light' ? '🌙' : '☀️';
        }
    </script>
</body>
</html>
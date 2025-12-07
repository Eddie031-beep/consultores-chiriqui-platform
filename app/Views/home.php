<?php
// Obtener vacantes disponibles
$vacantes = [];
try {
    $db = db_connect('local');
    $busqueda = $_GET['busqueda'] ?? '';
    $modalidad = $_GET['modalidad'] ?? '';
    // $ubicacion = $_GET['ubicacion'] ?? ''; // Removed per new design focus, can be re-added if needed

    $sql = "SELECT v.*, e.nombre as empresa_nombre FROM vacantes v 
            JOIN empresas e ON v.empresa_id = e.id 
            WHERE v.estado = 'abierta'";
    
    if (!empty($busqueda)) {
        $sql .= " AND (v.titulo LIKE '%' || ? || '%' OR v.descripcion LIKE '%' || ? || '%')";
    }
    if (!empty($modalidad)) {
        $sql .= " AND v.modalidad = ?";
    }

    $sql .= " ORDER BY v.fecha_publicacion DESC LIMIT 12";

    $stmt = $db->prepare($sql);
    $params = [];
    if (!empty($busqueda)) $params = array_merge($params, [$busqueda, $busqueda]);
    if (!empty($modalidad)) $params[] = $modalidad;

    $stmt->execute($params);
    $vacantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacantes - Consultores Chiriquí</title>
    <!-- Shared Styles (Colors, Base) -->
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <!-- Animations Library -->
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-animations.css">
    <!-- Home Page Specific Elegance -->
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/home-elegant.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <!-- NAVBAR (Assumed to be styled globally or in component) -->
    <?php include 'components/navbar.php'; ?>

    <!-- HERO SECTION REIMAGINED -->
    <section class="hero-extended animate-fade-in">
        <!-- Animated Background Elements -->
        <div class="hero-bg-anim">
            <div class="floating-shape shape-1"></div>
            <div class="floating-shape shape-2"></div>
            <div class="floating-shape shape-3"></div>
        </div>

        <div class="hero-content">
            <h1 class="hero-title animate-slide-up delay-100">
                Encuentra Tu Próxima <br>
                <span class="hero-highlight">Oportunidad Profesional</span>
            </h1>
            <p class="hero-subtitle animate-slide-up delay-200">
                Conectamos el mejor talento de Chiriquí con las empresas líderes. 
                Tu futuro empieza con una búsqueda.
            </p>
        </div>
    </section>

    <!-- SEARCH BAR OVERLAP -->
    <div class="container search-container-overlap animate-slide-up delay-300">
        <div class="glass-search-bar">
            <div class="search-title">
                🔍 <span>Filtrar Vacantes</span>
            </div>
            
            <form method="GET">
                <div class="search-grid">
                    <!-- Search Input -->
                    <div class="input-group">
                        <label class="input-label">¿Qué estás buscando?</label>
                        <input type="text" name="busqueda" class="custom-input" 
                               placeholder="Ej: Desarrollador, Contador, Vendedor..." 
                               value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>">
                    </div>

                    <!-- Modalidad Select -->
                    <div class="input-group">
                        <label class="input-label">Modalidad</label>
                        <select name="modalidad" class="custom-input">
                            <option value="">Todas</option>
                            <option value="presencial" <?= ($_GET['modalidad'] ?? '') === 'presencial' ? 'selected' : '' ?>>Presencial</option>
                            <option value="remoto" <?= ($_GET['modalidad'] ?? '') === 'remoto' ? 'selected' : '' ?>>Remoto</option>
                            <option value="hibrido" <?= ($_GET['modalidad'] ?? '') === 'hibrido' ? 'selected' : '' ?>>Híbrido</option>
                        </select>
                    </div>

                    <!-- Button -->
                    <button type="submit" class="custom-btn">
                        Buscar Empleo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="container">
        
        <?php if (empty($vacantes)): ?>
             <div style="text-align: center; padding: 60px 20px; color: var(--text-muted);" class="animate-fade-in delay-400">
                <div style="font-size: 4rem; opacity: 0.3; margin-bottom: 20px;">🕵️‍♀️</div>
                <h2>No encontramos vacantes exactas</h2>
                <p>Intenta con otros términos o limpia los filtros para ver todo.</p>
                <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes" style="display: inline-block; margin-top: 15px; color: var(--primary-color); font-weight: 600;">Ver todas las vacantes</a>
             </div>
        <?php else: ?>
            <div class="vacantes-grid-v2">
                <?php foreach ($vacantes as $index => $vacante): 
                    // Staggered animation delay based on index
                    $delay = min(($index + 4) * 100, 1000); 
                ?>
                    <div class="vacancy-card-elegant card-portal-style animate-slide-up" style="animation-delay: <?= $delay ?>ms;">
                        <!-- Top Meta -->
                        <div class="portal-meta">
                            <span class="portal-date">Publicado: <?= date('d/m/Y', strtotime($vacante['fecha_publicacion'])) ?></span>
                            <?php if ($index % 2 != 0): ?> 
                                <!-- Simulating "Featured/Confidential" -->
                                <span class="portal-badge-alert">Destacado</span>
                            <?php endif; ?>
                        </div>

                        <div class="card-header-v2">
                            <h2 class="job-title" style="margin-bottom: 5px;"><?= htmlspecialchars($vacante['titulo']) ?></h2>
                            <div class="company-name"><?= htmlspecialchars($vacante['empresa_nombre']) ?></div>
                        </div>

                        <div class="vacante-info portal-desc">
                            <?= substr(strip_tags($vacante['descripcion']), 0, 120) ?>...
                        </div>
                        
                        <div class="tags-row" style="margin-top: auto;">
                            <span class="tag-pill tag-blue">
                                🏢 <?= ucfirst($vacante['modalidad']) ?>
                            </span>
                            <?php if (!empty($vacante['ubicacion'])): ?>
                                <span class="tag-pill tag-gray">📍 <?= htmlspecialchars($vacante['ubicacion']) ?></span>
                            <?php endif; ?>
                            <!-- SALARY REMOVED as per request -->
                        </div>

                        <div class="card-actions-v2" style="margin-top: 15px;">
                            <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes/<?= htmlspecialchars($vacante['slug']) ?>" class="btn-outline">Ver Detalles</a>
                            <a href="<?= ENV_APP['BASE_URL'] ?>/postular/<?= $vacante['id'] ?>" class="btn-fill">Postulación Rápida ⚡</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
    </div>

    <!-- ELEGANT THEME TOGGLE (Floating Pill) - Consistent across all pages now -->
    <!-- Theme Toggle Removed -->
    <script>
        // Force Light Theme
        localStorage.setItem('theme', 'light');
        document.documentElement.setAttribute('data-theme', 'light');
    </script>
</body>
</html>
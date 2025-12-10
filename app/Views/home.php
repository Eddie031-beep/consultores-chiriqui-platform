<?php
// Lógica de obtención de datos (sin cambios)
$vacantes = [];
try {
    $db = db_connect('local');
    $busqueda = $_GET['busqueda'] ?? '';
    $modalidad = $_GET['modalidad'] ?? '';

    $sql = "SELECT v.*, e.nombre as empresa_nombre FROM vacantes v 
            JOIN empresas e ON v.empresa_id = e.id 
            WHERE v.estado = 'abierta'";
    
    $params = [];
    if (!empty($busqueda)) {
        $sql .= " AND (v.titulo LIKE ? OR v.descripcion LIKE ?)";
        $params[] = "%$busqueda%";
        $params[] = "%$busqueda%";
    }
    if (!empty($modalidad)) {
        $sql .= " AND v.modalidad = ?";
        $params[] = $modalidad;
    }

    $sql .= " ORDER BY v.fecha_publicacion DESC LIMIT 12";

    $stmt = $db->prepare($sql);
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
    <title>Inicio - Consultores Chiriquí</title>
    
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-animations.css">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/home-elegant.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        /* ESTILOS DE SELECTOR PERSONALIZADO ANIMADO */
        .custom-select-wrapper {
            position: relative;
            user-select: none;
            width: 100%;
        }
        .custom-select {
            position: relative;
            display: flex;
            flex-direction: column;
        }
        .custom-select__trigger {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 15px;
            font-size: 0.95rem;
            font-weight: 500;
            color: #3b3b3b;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .custom-select__trigger:hover {
            border-color: #2563eb;
        }
        .custom-arrow {
            transition: transform 0.3s ease;
        }
        
        /* Opciones del Dropdown */
        .custom-options {
            position: absolute;
            display: block;
            top: 100%;
            left: 0;
            right: 0;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
            background: #fff;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            z-index: 50;
            margin-top: 8px;
            overflow: hidden;
        }
        
        /* ESTADO ABIERTO */
        .custom-select.open .custom-options {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .custom-select.open .custom-arrow {
            transform: rotate(180deg);
        }
        
        .custom-option {
            position: relative;
            display: block;
            padding: 10px 15px;
            font-size: 0.9rem;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s;
        }
        .custom-option:hover {
            background-color: #f1f5f9;
            color: #2563eb;
            padding-left: 20px; /* Pequeño desplazamiento elegante */
        }
        .custom-option.selected {
            color: #fff;
            background-color: #2563eb;
        }

        /* AJUSTES DE TARJETAS (Más compactas y modernas) */
        .vacancy-card-elegant {
            padding: 20px; /* Reducido para que no sea tan grande */
            border-radius: 16px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .vacancy-card-elegant:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 30px -5px rgba(0,0,0,0.08);
            border-color: #e0e7ff;
        }
        .job-title {
            font-size: 1.1rem; /* Ajustado */
            margin-bottom: 4px;
        }
        .company-name {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 500;
            display: flex; align-items: center; gap: 6px;
        }
        .portal-desc {
            font-size: 0.9rem;
            line-height: 1.5;
            color: #475569;
            margin: 12px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Badges de Modalidad */
        .badge-mod {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .mod-remoto { background: #dcfce7; color: #166534; }
        .mod-hibrido { background: #f3e8ff; color: #6b21a8; }
        .mod-presencial { background: #e0f2fe; color: #0369a1; }

        .search-container-overlap {
            margin-top: -40px; /* Subir un poco más */
        }
    </style>
</head>
<body>

    <?php include __DIR__ . '/components/navbar.php'; ?>

    <section class="hero-extended animate-fade-in" style="padding-bottom: 120px;">
        <div class="hero-bg-anim">
            <div class="floating-shape shape-1"></div>
            <div class="floating-shape shape-2"></div>
        </div>

        <div class="hero-content">
            <h1 class="hero-title animate-slide-up delay-100" style="font-size: 2.8rem;">
                Tu próximo empleo está aquí
            </h1>
            <p class="hero-subtitle animate-slide-up delay-200" style="font-size: 1.1rem;">
                Conectamos talento con las mejores empresas de Chiriquí.
            </p>
        </div>
    </section>

    <div class="container search-container-overlap animate-slide-up delay-300">
        <div class="glass-search-bar" style="padding: 30px; background: white; border-radius: 12px; box-shadow: 0 20px 40px -5px rgba(0,0,0,0.1); border: 1px solid #f1f5f9;">
            <form method="GET" action="<?= ENV_APP['BASE_URL'] ?>/vacantes">
                <div class="search-grid" style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 20px; align-items: end;">
                    
                    <div class="input-group">
                        <label class="input-label" style="font-size: 0.85rem; margin-bottom: 8px; color: #64748b; font-weight: 600;">¿Qué estás buscando?</label>
                        <div style="position: relative;">
                            <i class="fas fa-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                            <input type="text" name="busqueda" class="custom-input" 
                                   style="padding: 14px 14px 14px 45px; border: 1px solid #e2e8f0; border-radius: 8px; width: 100%; font-size: 0.95rem; background: #f8fafc;"
                                   placeholder="Ej: Desarrollador, Ventas..." 
                                   value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="input-group">
                        <label class="input-label" style="font-size: 0.85rem; margin-bottom: 8px; color: #64748b; font-weight: 600;">Modalidad</label>
                        
                        <div class="custom-select-wrapper">
                            <div class="custom-select" style="border: 1px solid #e2e8f0; background: #f8fafc; border-radius: 8px;">
                                <div class="custom-select__trigger" style="padding: 14px 15px;">
                                    <span id="selected-text" style="color: #334155;">Todas las modalidades</span>
                                    <i class="fas fa-chevron-down custom-arrow" style="color: #94a3b8; font-size: 0.8rem;"></i>
                                </div>
                                <div class="custom-options">
                                    <span class="custom-option selected" data-value="">Todas las modalidades</span>
                                    <span class="custom-option" data-value="presencial">🏢 Presencial</span>
                                    <span class="custom-option" data-value="remoto">🏠 Remoto</span>
                                    <span class="custom-option" data-value="hibrido">🔄 Híbrido</span>
                                </div>
                            </div>
                            <input type="hidden" name="modalidad" id="modalidad-input" value="<?= htmlspecialchars($_GET['modalidad'] ?? '') ?>">
                        </div>
                    </div>

                    <button type="submit" class="custom-btn" style="height: 52px; padding: 0 40px; background: #4f46e5; color: white; border-radius: 8px; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s;">
                        Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="container" style="margin-top: 60px;">
        
        <?php if (empty($vacantes)): ?>
             <div style="text-align: center; padding: 60px 20px; color: var(--text-muted);" class="animate-fade-in delay-400">
                <div style="font-size: 4rem; opacity: 0.3; margin-bottom: 20px;">🕵️‍♀️</div>
                <h2>No encontramos resultados</h2>
                <p>Intenta con otros términos.</p>
             </div>
        <?php else: ?>
            <div class="vacantes-grid-v2">
                <?php foreach ($vacantes as $index => $vacante): 
                    $delay = min(($index + 1) * 100, 800); 
                    
                    // Lógica de Badge
                    $modClass = 'mod-presencial';
                    if($vacante['modalidad'] === 'remoto') $modClass = 'mod-remoto';
                    if($vacante['modalidad'] === 'hibrido') $modClass = 'mod-hibrido';
                ?>
                    <div class="vacancy-card-elegant animate-slide-up" style="animation-delay: <?= $delay ?>ms;">
                        
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                            <span class="badge-mod <?= $modClass ?>">
                                <?= ucfirst($vacante['modalidad']) ?>
                            </span>
                            <span style="font-size: 0.75rem; color: #94a3b8;">
                                Hace <?= intval((time() - strtotime($vacante['fecha_publicacion'])) / 86400) ?>d
                            </span>
                        </div>

                        <h2 class="job-title"><?= htmlspecialchars($vacante['titulo']) ?></h2>
                        <div class="company-name">
                            <i class="far fa-building"></i> <?= htmlspecialchars($vacante['empresa_nombre']) ?>
                        </div>

                        <div class="vacante-info portal-desc">
                            <?= substr(strip_tags($vacante['descripcion']), 0, 90) ?>...
                        </div>
                        
                        <div style="border-top: 1px solid #f1f5f9; padding-top: 15px; margin-top: auto; display: flex; gap: 10px;">
                            <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes/<?= htmlspecialchars($vacante['slug']) ?>" class="btn-outline" style="flex: 1; font-size: 0.85rem; padding: 8px;">
                                Ver Detalle
                            </a>
                            <a href="<?= ENV_APP['BASE_URL'] ?>/postular/<?= $vacante['id'] ?>" class="btn-fill" style="flex: 1; font-size: 0.85rem; padding: 8px;">
                                Aplicar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
    </div>

    <?php include __DIR__ . '/components/chatbot-widget.php'; ?>

    <script>
        document.querySelector('.custom-select-wrapper').addEventListener('click', function() {
            this.querySelector('.custom-select').classList.toggle('open');
        });

        for (const option of document.querySelectorAll(".custom-option")) {
            option.addEventListener('click', function() {
                if (!this.classList.contains('selected')) {
                    this.parentNode.querySelector('.custom-option.selected').classList.remove('selected');
                    this.classList.add('selected');
                    
                    // Actualizar texto y valor oculto
                    document.querySelector('#selected-text').textContent = this.textContent;
                    document.getElementById('modalidad-input').value = this.getAttribute('data-value');
                }
            })
        }

        // Cerrar al hacer click fuera
        window.addEventListener('click', function(e) {
            const select = document.querySelector('.custom-select');
            if (!select.contains(e.target)) {
                select.classList.remove('open');
            }
        });
    </script>

</body>
</html>
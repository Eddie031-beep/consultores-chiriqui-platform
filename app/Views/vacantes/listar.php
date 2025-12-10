<?php
$vacantes = $vacantes ?? [];
$empresas = $empresas ?? [];

// Helper de tiempo
function tiempoRelativo($fecha) {
    $timestamp = strtotime($fecha);
    $diferencia = time() - $timestamp;
    if ($diferencia < 60) return 'Hace un momento';
    if ($diferencia < 3600) return 'Hace ' . floor($diferencia / 60) . ' min';
    if ($diferencia < 86400) return 'Hace ' . floor($diferencia / 3600) . ' horas';
    if ($diferencia < 604800) return 'Hace ' . floor($diferencia / 86400) . ' días';
    return date('d/m/Y', $timestamp);
}
?>

<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacantes Disponibles - Consultores Chiriquí</title>
    
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-animations.css">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/home-elegant.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        /* --- CORRECCIONES DE ESTILO PARA EL FILTRO --- */
        
        /* Contenedor principal del filtro (Píldora) */
        .filter-pill {
            background: white;
            border-radius: 16px;
            padding: 10px;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255,255,255,0.8);
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr auto;
            gap: 0;
            align-items: center;
            position: relative;
            z-index: 50; /* Importante para estar sobre el contenido */
        }

        .filter-group {
            padding: 5px 15px;
            border-right: 1px solid #e2e8f0;
            position: relative;
        }
        .filter-group:last-of-type { border-right: none; }

        .filter-label {
            display: block; font-size: 0.75rem; color: #64748b; font-weight: 600;
            margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;
        }

        .filter-input {
            width: 100%; border: none; outline: none; font-size: 0.95rem;
            color: #1e293b; font-weight: 500; background: transparent; padding: 0;
        }

        /* --- DROPDOWN ANIMADO CORREGIDO --- */
        .custom-select-wrapper {
            position: relative;
            cursor: pointer;
            width: 100%;
        }
        
        .custom-select-trigger {
            display: flex; justify-content: space-between; align-items: center;
            font-size: 0.95rem; font-weight: 500; color: #1e293b; width: 100%;
        }

        .custom-arrow {
            font-size: 0.8rem; color: #94a3b8; transition: transform 0.3s ease;
        }

        /* El menú desplegable */
        .custom-options {
            position: absolute;
            top: calc(100% + 15px); /* Un poco separado */
            left: -10px; right: -10px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 15px 30px -5px rgba(0,0,0,0.15);
            border: 1px solid #f1f5f9;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 1000; /* Z-index muy alto para flotar sobre todo */
            padding: 5px;
            max-height: 300px;
            overflow-y: auto;
        }

        .custom-select-wrapper.open .custom-options {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .custom-select-wrapper.open .custom-arrow {
            transform: rotate(180deg);
            color: #2563eb;
        }

        .custom-option {
            padding: 10px 15px; border-radius: 8px; color: #475569; font-size: 0.9rem;
            transition: background 0.2s; display: flex; align-items: center; gap: 8px;
        }
        .custom-option:hover { background: #f8fafc; color: #2563eb; }
        .custom-option.selected { background: #eff6ff; color: #2563eb; font-weight: 600; }

        /* Botón Buscar */
        .btn-search {
            background: #2563eb; color: white; border: none; padding: 12px 24px;
            border-radius: 12px; font-weight: 600; cursor: pointer; transition: background 0.2s;
            height: 100%; margin-left: 10px; display: flex; align-items: center; justify-content: center;
        }
        .btn-search:hover { background: #1d4ed8; }

        /* Responsive */
        @media (max-width: 992px) {
            .filter-pill { grid-template-columns: 1fr 1fr; gap: 15px; border-radius: 20px; }
            .filter-group { border-right: none; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; }
            .btn-search { grid-column: 1 / -1; margin: 0; width: 100%; }
        }
        @media (max-width: 600px) { .filter-pill { grid-template-columns: 1fr; } }

        /* Hero */
        .modern-hero-compact {
            background: radial-gradient(circle at center, #1e1b4b 0%, #0f172a 100%);
            padding: 6rem 1rem 7rem;
            text-align: center;
            position: relative;
            color: white;
            overflow: hidden;
        }
        
        /* Fix overlapping */
        .search-container-overlap {
            max-width: 1100px;
            margin: -3.5rem auto 3rem;
            position: relative;
            z-index: 60; /* Mayor que el hero */
            padding: 0 1rem;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <section class="modern-hero-compact animate-fade-in">
        <div class="hero-glow"></div>
        <div style="position: relative; z-index: 2;">
            <h1 class="hero-title animate-slide-up">Vacantes Disponibles</h1>
            <p class="hero-subtitle animate-slide-up delay-100">Explora oportunidades en las mejores empresas de la región.</p>
        </div>
    </section>

    <div class="search-container-overlap animate-slide-up delay-200">
        <form method="GET" action="<?= ENV_APP['BASE_URL'] ?>/vacantes">
            <div class="filter-pill">
                
                <div class="filter-group">
                    <label class="filter-label"><i class="fas fa-search"></i> Búsqueda</label>
                    <input type="text" name="busqueda" class="filter-input" 
                           placeholder="Cargo o palabra clave..." 
                           value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>">
                </div>

                <div class="filter-group">
                    <label class="filter-label"><i class="fas fa-laptop-house"></i> Modalidad</label>
                    <div class="custom-select-wrapper" id="select-modalidad">
                        <div class="custom-select-trigger">
                            <span class="trigger-text">Todas</span>
                            <i class="fas fa-chevron-down custom-arrow"></i>
                        </div>
                        <div class="custom-options">
                            <div class="custom-option selected" data-value="">🌐 Todas</div>
                            <div class="custom-option" data-value="presencial">🏢 Presencial</div>
                            <div class="custom-option" data-value="remoto">🏠 Remoto</div>
                            <div class="custom-option" data-value="hibrido">🔄 Híbrido</div>
                        </div>
                        <input type="hidden" name="modalidad" value="<?= htmlspecialchars($_GET['modalidad'] ?? '') ?>">
                    </div>
                </div>

                <div class="filter-group">
                    <label class="filter-label"><i class="fas fa-map-marker-alt"></i> Ubicación</label>
                    <input type="text" name="ubicacion" class="filter-input" 
                           placeholder="Ciudad o Provincia"
                           value="<?= htmlspecialchars($_GET['ubicacion'] ?? '') ?>">
                </div>

                <div class="filter-group">
                    <label class="filter-label"><i class="fas fa-building"></i> Empresa</label>
                    <div class="custom-select-wrapper" id="select-empresa">
                        <div class="custom-select-trigger">
                            <span class="trigger-text">Todas</span>
                            <i class="fas fa-chevron-down custom-arrow"></i>
                        </div>
                        <div class="custom-options">
                            <div class="custom-option selected" data-value="">🏢 Todas las empresas</div>
                            <?php foreach ($empresas as $emp): ?>
                                <div class="custom-option" data-value="<?= $emp['id'] ?>">
                                    <?= htmlspecialchars($emp['nombre']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="empresa" value="<?= htmlspecialchars($_GET['empresa'] ?? '') ?>">
                    </div>
                </div>

                <button type="submit" class="btn-search">Buscar</button>
            </div>
        </form>
    </div>

    <div class="container" style="margin-top: 40px; padding-bottom: 60px;">
        
        <div style="margin-bottom: 25px; color: #64748b; font-weight: 500;">
            Se encontraron <strong><?= count($vacantes) ?></strong> vacantes
        </div>

        <?php if (empty($vacantes)): ?>
            <div style="text-align: center; padding: 4rem; background: white; border-radius: 16px; border: 1px dashed #cbd5e1;">
                <i class="fas fa-ghost" style="font-size: 3rem; color: #e2e8f0; margin-bottom: 1rem;"></i>
                <h3 style="color: #475569;">Sin resultados</h3>
                <p style="color: #94a3b8;">Prueba ajustando los filtros de búsqueda.</p>
                <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes" style="color: #2563eb; font-weight: 600; text-decoration: none; margin-top: 10px; display: inline-block;">Limpiar Filtros</a>
            </div>
        <?php else: ?>
            <div class="vacantes-grid-v2">
                <?php foreach ($vacantes as $index => $v): 
                    $delay = min(($index + 1) * 100, 800);
                    $modClass = match($v['modalidad']) {
                        'remoto' => 'bg-green-100 text-green-700', // Clases de utilidad simuladas
                        'hibrido' => 'bg-purple-100 text-purple-700',
                        default => 'bg-blue-100 text-blue-700'
                    };
                    $badgeStyle = match($v['modalidad']) {
                        'remoto' => 'background:#dcfce7; color:#166534;',
                        'hibrido' => 'background:#f3e8ff; color:#6b21a8;',
                        default => 'background:#e0f2fe; color:#0369a1;'
                    };
                ?>
                    <div class="vacancy-card-elegant animate-slide-up" style="animation-delay: <?= $delay ?>ms;">
                        <div class="portal-meta">
                            <span style="<?= $badgeStyle ?> padding:3px 10px; border-radius:12px; font-size:0.75rem; font-weight:700; text-transform:uppercase;">
                                <?= ucfirst($v['modalidad']) ?>
                            </span>
                            <span class="portal-date"><?= tiempoRelativo($v['fecha_publicacion']) ?></span>
                        </div>

                        <div class="card-header-v2">
                            <h2 class="job-title"><?= htmlspecialchars($v['titulo']) ?></h2>
                            <div class="company-name"><i class="far fa-building"></i> <?= htmlspecialchars($v['empresa_nombre']) ?></div>
                        </div>

                        <div class="vacante-info portal-desc">
                            <?= substr(strip_tags($v['descripcion']), 0, 100) ?>...
                        </div>

                        <div style="margin-top:auto; padding-top:15px; border-top:1px solid #f1f5f9;">
                            <div style="font-size:0.85rem; color:#64748b; margin-bottom:15px;">
                                <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($v['ubicacion']) ?>
                            </div>
                            <div class="card-actions-v2">
                                <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes/<?= htmlspecialchars($v['slug']) ?>" class="btn-outline">Ver Detalle</a>
                                <a href="<?= ENV_APP['BASE_URL'] ?>/postular/<?= $v['id'] ?>" class="btn-fill">Aplicar</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdowns = document.querySelectorAll('.custom-select-wrapper');

            dropdowns.forEach(wrapper => {
                const trigger = wrapper.querySelector('.custom-select-trigger');
                const input = wrapper.querySelector('input[type="hidden"]');
                const textSpan = wrapper.querySelector('.trigger-text');
                const options = wrapper.querySelectorAll('.custom-option');

                // Pre-seleccionar
                if(input.value) {
                    const sel = wrapper.querySelector(`.custom-option[data-value="${input.value}"]`);
                    if(sel) {
                        textSpan.textContent = sel.textContent;
                        options.forEach(o => o.classList.remove('selected'));
                        sel.classList.add('selected');
                    }
                }

                trigger.addEventListener('click', (e) => {
                    dropdowns.forEach(d => { if(d !== wrapper) d.classList.remove('open'); });
                    wrapper.classList.toggle('open');
                    e.stopPropagation();
                });

                options.forEach(opt => {
                    opt.addEventListener('click', (e) => {
                        input.value = opt.getAttribute('data-value');
                        textSpan.textContent = opt.textContent;
                        options.forEach(o => o.classList.remove('selected'));
                        opt.classList.add('selected');
                        wrapper.classList.remove('open');
                        e.stopPropagation();
                    });
                });
            });

            document.addEventListener('click', () => {
                dropdowns.forEach(d => d.classList.remove('open'));
            });
        });
    </script>
</body>
</html>
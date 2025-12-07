<?php
/**
 * Vacante Detalle - Redesigned
 * Professional SaaS Theme
 */
$vacante = $vacante ?? [];
$isAuthenticated = isset($_SESSION['user']);
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($vacante['titulo']) ?> | Consultores Chiriquí</title>
    <!-- GLOBAL CSS -->
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <!-- Bootstrap 5 (via CDN for layout components) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #2563eb;
            --secondary: #64748b;
            --success: #10b981;
            --bg-body: #f1f5f9;
            --text-heading: #1e293b;
            --text-body: #475569;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-body);
            padding-top: 80px; /* Space for fixed navbar */
        }

        /* Navbar Override */
        .navbar-logo img { height: 50px; } 

        /* Hero Header */
        .job-hero {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }

        .job-title {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--text-heading);
            margin-bottom: 0.5rem;
            letter-spacing: -0.025em;
        }

        .job-company {
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--primary);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .job-meta-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-top: 1.5rem;
            font-size: 0.95rem;
            color: var(--secondary);
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .badge-pill {
            padding: 0.35em 0.8em;
            border-radius: 50rem;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .badge-presencial { background: #dbeafe; color: #1e40af; }
        .badge-remoto { background: #d1fae5; color: #065f46; }
        .badge-hibrido { background: #f3e8ff; color: #6b21a8; }

        /* Main Content Grid */
        .content-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .card-body-custom {
            padding: 2.5rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-heading);
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .section-title i { color: var(--primary); }

        .job-description {
            line-height: 1.8;
            font-size: 1.05rem;
        }
        
        .company-sidebar-card {
            background: white;
            border-radius: 1rem;
            border: 1px solid #cbd5e1;
            padding: 2rem;
            position: sticky;
            top: 100px;
        }

        .btn-apply {
            display: block;
            width: 100%;
            padding: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            transition: all 0.2s;
        }

        .btn-primary-custom {
            background: var(--primary);
            color: white;
            border: none;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
        }
        
        .btn-primary-custom:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(37, 99, 235, 0.3);
        }

        .contact-list {
            list-style: none;
            padding: 0;
            margin: 0;
            margin-top: 1.5rem;
        }

        .contact-list li {
            margin-bottom: 1rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            color: var(--secondary);
            font-size: 0.95rem;
        }
        
        .alert-custom {
            border-radius: 0.75rem;
            border-left: 5px solid #f59e0b;
            background: #fffbeb;
            color: #92400e;
        }

    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <!-- Hero Section -->
    <header class="job-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes" class="text-decoration-none text-muted mb-3 d-inline-block fw-bold">
                        <i class="fas fa-arrow-left me-1"></i> Volver a vacantes
                    </a>
                    
                    <h1 class="job-title"><?= htmlspecialchars($vacante['titulo']) ?></h1>
                    
                    <div class="job-company">
                        <i class="fas fa-building"></i>
                        <?= htmlspecialchars($vacante['empresa_nombre']) ?>
                    </div>

                    <div class="job-meta-row">
                        <div class="meta-item">
                            <?php 
                                $badgeClass = match($vacante['modalidad']) {
                                    'remoto' => 'badge-remoto',
                                    'hibrido' => 'badge-hibrido',
                                    default => 'badge-presencial'
                                };
                            ?>
                            <span class="badge-pill <?= $badgeClass ?>">
                                <?= ucfirst($vacante['modalidad']) ?>
                            </span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <?= htmlspecialchars($vacante['ubicacion']) ?>
                        </div>
                        <div class="meta-item">
                            <i class="far fa-clock"></i>
                            Publicado: <?= date('d/m/Y', strtotime($vacante['fecha_publicacion'])) ?>
                        </div>
                        <?php if ($vacante['salario_min']): ?>
                        <div class="meta-item text-success fw-bold">
                            <i class="fas fa-money-bill-wave"></i>
                            B/. <?= number_format($vacante['salario_min'], 2) ?> - <?= number_format($vacante['salario_max'], 2) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    <?php if (isset($vacante['estado']) && $vacante['estado'] !== 'abierta'): ?>
                         <button class="btn btn-secondary btn-lg w-100 disabled" disabled>Convocatoria Cerrada</button>
                    <?php else: ?>
                        <a href="<?= ENV_APP['BASE_URL'] ?>/postular/<?= $vacante['id'] ?>" class="btn btn-primary-custom btn-apply btn-lg">
                            Postularme Ahora <i class="fas fa-paper-plane ms-2"></i>
                        </a>
                        <p class="text-muted small mb-0">Solo toma unos segundos</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container pb-5">
        <div class="row">
            <!-- Left Column: Description -->
            <div class="col-lg-8">
                <?php if (!$isAuthenticated): ?>
                    <div class="alert alert-custom mb-4 shadow-sm" role="alert">
                        <div class="d-flex">
                            <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                            <div>
                                <h5 class="alert-heading fw-bold">Se requiere cuenta</h5>
                                <p class="mb-0">Para postularte a esta posición necesitas una cuenta de candidato. El registro es gratuito y rápido.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="content-card">
                    <div class="card-body-custom">
                        <h2 class="section-title">
                            <i class="fas fa-align-left"></i> Descripción del Puesto
                        </h2>
                        <div class="job-description text-secondary">
                            <?= nl2br(htmlspecialchars($vacante['descripcion'])) ?>
                        </div>
                    </div>
                </div>

                <!-- Fake Requirements Section (Enhancement) -->
                <div class="content-card">
                    <div class="card-body-custom">
                        <h2 class="section-title">
                            <i class="fas fa-check-circle"></i> Requisitos (Sugeridos)
                        </h2>
                        <ul class="list-group list-group-flush list-group-numbered">
                            <li class="list-group-item bg-transparent">Experiencia previa comprobable en cargos similares.</li>
                            <li class="list-group-item bg-transparent">Habilidades de comunicación y trabajo en equipo.</li>
                            <li class="list-group-item bg-transparent">Disponibilidad para <?= $vacante['modalidad'] ?>.</li>
                            <li class="list-group-item bg-transparent">Residir en <?= htmlspecialchars($vacante['ubicacion']) ?> o zonas aledañas.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Column: Company Info -->
            <div class="col-lg-4">
                <div class="company-sidebar-card shadow-sm">
                    <h5 class="fw-bold mb-3 text-dark">Sobre la Empresa</h5>
                    <hr class="mb-4">
                    
                    <h4 class="fw-bold text-primary mb-2"><?= htmlspecialchars($vacante['empresa_nombre']) ?></h4>
                    <?php if ($vacante['sector']): ?>
                        <p class="text-muted mb-4"><small class="text-uppercase fw-bold"><?= htmlspecialchars($vacante['sector']) ?></small></p>
                    <?php endif; ?>

                    <ul class="contact-list">
                        <?php if ($vacante['sitio_web']): ?>
                        <li>
                            <i class="fas fa-globe mt-1 text-primary"></i>
                            <div>
                                <span class="d-block fw-bold text-dark">Sitio Web</span>
                                <a href="<?= htmlspecialchars($vacante['sitio_web']) ?>" target="_blank" class="text-break"><?= htmlspecialchars($vacante['sitio_web']) ?></a>
                            </div>
                        </li>
                        <?php endif; ?>

                        <?php if ($vacante['email_contacto']): ?>
                        <li>
                            <i class="fas fa-envelope mt-1 text-primary"></i>
                            <div>
                                <span class="d-block fw-bold text-dark">Email de Contacto</span>
                                <a href="mailto:<?= htmlspecialchars($vacante['email_contacto']) ?>" class="text-break"><?= htmlspecialchars($vacante['email_contacto']) ?></a>
                            </div>
                        </li>
                        <?php endif; ?>
                        
                        <?php if ($vacante['telefono']): ?>
                        <li>
                            <i class="fas fa-phone mt-1 text-primary"></i>
                            <div>
                                <span class="d-block fw-bold text-dark">Teléfono</span>
                                <?= htmlspecialchars($vacante['telefono']) ?>
                            </div>
                        </li>
                        <?php endif; ?>
                    </ul>

                    <div class="mt-4 pt-3 border-top text-center">
                        <span class="text-muted small">ID Vacante: #<?= $vacante['id'] ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
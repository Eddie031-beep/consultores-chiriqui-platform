<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Empresa | Consultores Chiriquí</title>
    <!-- Bootstrap & Modern Font -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body { background-color: #f8f9fc; font-family: 'Nunito', sans-serif; }
        .text-primary { color: #4e73df !important; }
        .border-left-primary { border-left: 0.25rem solid #4e73df !important; }
        .border-left-success { border-left: 0.25rem solid #1cc88a !important; }
        .border-left-info { border-left: 0.25rem solid #36b9cc !important; }
        .border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
        
        .card-title { font-size: 1.5rem; color: #5a5c69; }
        .shadow-sm { box-shadow: 0 .125rem .25rem 0 rgba(58,59,69,.2)!important; }
        
        /* Navbar Tweaks */
        .navbar { border-bottom: 1px solid #e3e6f0; }
        .nav-link { color: #858796; font-weight: 600; }
        .nav-link.active { color: #4e73df; }
        .nav-link:hover { color: #2e59d9; }
    </style>
</head>
<body>

<!-- A. Barra de Navegación (Header) -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="<?= ENV_APP['ASSETS_URL'] ?>/img/logo.png" alt="Consultores Chiriquí" height="50" class="me-3">
            <span class="fw-bold text-dark" style="font-size: 1.25rem; letter-spacing: -0.5px;">Consultores Chiriquí</span>
        </a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link text-dark fw-medium" href="<?= ENV_APP['BASE_URL'] ?>/">
                        <i class="fas fa-home me-1"></i> Inicio
                    </a>
                </li>
                <li class="nav-item"><a class="nav-link active" href="<?= ENV_APP['BASE_URL'] ?>/empresa/dashboard">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes">Mis Vacantes</a></li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="<?= ENV_APP['BASE_URL'] ?>/logout">Salir <i class="fas fa-sign-out-alt"></i></a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 mt-4">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 text-gray-800 fw-bold">Bienvenido, <?= htmlspecialchars($user['nombre']) ?></h2>
            <p class="text-muted mb-0">Gestión de Talento & Facturación | <span class="badge bg-light text-dark border">ID: #<?= $user['empresa_id'] ?></span></p>
        </div>
        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes/crear" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus"></i> Nueva Vacante
        </a>
    </div>

    <!-- B. Tarjetas de Estadísticas (KPIs) -->
    <div class="row g-3 mb-4">
        <!-- Vacantes Activas -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-left-primary">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="fas fa-briefcase text-primary fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="card-subtitle text-muted mb-1 text-uppercase font-weight-bold" style="font-size: 0.7rem;">Vacantes Activas</h6>
                        <h3 class="card-title mb-0 fw-bold"><?= $vacantesActivas ?></h3>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Candidatos -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-left-success">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="fas fa-users text-success fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="card-subtitle text-muted mb-1 text-uppercase font-weight-bold" style="font-size: 0.7rem;">Candidatos</h6>
                        <h3 class="card-title mb-0 fw-bold"><?= $totalCandidatos ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Consumo Mes -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-left-info">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="fas fa-dollar-sign text-info fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="card-subtitle text-muted mb-1 text-uppercase font-weight-bold" style="font-size: 0.7rem;">Consumo Mes</h6>
                        <h3 class="card-title mb-0 fw-bold">B/. <?= number_format($consumoActual, 2) ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reputación -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-left-warning">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2 text-uppercase font-weight-bold" style="font-size: 0.7rem;">Reputación Corporativa</h6>
                    <div class="d-flex justify-content-center align-items-center gap-1 mb-1">
                        <!-- Estrellas dinámicas basadas en reputación (simple) -->
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star-half-alt text-warning"></i>
                    </div>
                    <small class="fw-bold text-dark"><?= $reputacion ?> / 5.0</small>
                    <a href="#comentarios" class="d-block small text-primary mt-1">Ver opiniones</a>
                </div>
            </div>
        </div>
    </div>

    <!-- C. Sección de Comentarios / Valoraciones -->
    <div class="row mt-4" id="comentarios">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Últimas Valoraciones recibidas</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <?php if(empty($valoraciones)): ?>
                             <div class="text-center py-4 text-muted">
                                <i class="far fa-comment-dots fa-2x mb-2"></i>
                                <p>Aún no tienes valoraciones de candidatos.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach($valoraciones as $val): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 text-dark fw-bold"><?= $val['autor'] ?></h6>
                                    <small class="text-muted"><?= $val['fecha'] ?></small>
                                </div>
                                <div class="mb-1 text-warning small">
                                    <?php for($i=0; $i<$val['estrellas']; $i++): ?><i class="fas fa-star"></i><?php endfor; ?>
                                </div>
                                <p class="mb-1 small text-secondary">"<?= $val['comentario'] ?>"</p>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-dark">Gestión</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes/crear" class="btn btn-outline-primary text-start">
                            <i class="fas fa-bullhorn me-2"></i> Publicar Vacante
                        </a>
                        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/candidatos" class="btn btn-outline-secondary text-start">
                            <i class="fas fa-search me-2"></i> Buscar Candidatos
                        </a>
                        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/facturacion" class="btn btn-outline-dark text-start">
                            <i class="fas fa-file-invoice-dollar me-2"></i> Ver Facturación
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
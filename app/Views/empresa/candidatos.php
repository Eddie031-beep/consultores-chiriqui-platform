<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Candidatos | Consultores Chiriquí</title>
    <!-- Bootstrap & Modern Font -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body { background-color: #f8f9fc; font-family: 'Nunito', sans-serif; }
        .text-primary { color: #4e73df !important; }
        .shadow-sm { box-shadow: 0 .125rem .25rem 0 rgba(58,59,69,.2)!important; }
        
        /* Navbar Tweaks */
        .navbar { border-bottom: 1px solid #e3e6f0; }
        .nav-link { color: #858796; font-weight: 600; }
        .nav-link.active { color: #4e73df; }
        .nav-link:hover { color: #2e59d9; }
        
        .avatar-circle {
            width: 40px; height: 40px;
            background-color: #4e73df; color: white;
            border-radius: 50%; display: flex;
            align-items: center; justify-content: center;
            font-weight: bold; margin-right: 10px;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
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
                        <i class="fas fa-home me-1"></i> Inicio / Mercado
                    </a>
                </li>
                <li class="nav-item"><a class="nav-link" href="<?= ENV_APP['BASE_URL'] ?>/empresa/dashboard">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes">Mis Vacantes</a></li>
                <li class="nav-item"><a class="nav-link active" href="#">Candidatos</a></li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="<?= ENV_APP['BASE_URL'] ?>/logout">Salir <i class="fas fa-sign-out-alt"></i></a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Candidatos Postulados</h1>
    </div>

    <div class="row">
        <?php if (empty($candidatos)): ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm py-5 text-center">
                    <div class="card-body">
                        <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aún no hay candidatos</h5>
                        <p class="text-muted small">Tus vacantes están esperando talento.</p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($candidatos as $c): ?>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-circle">
                                <?= strtoupper(substr($c['nombre'], 0, 1) . substr($c['apellido'], 0, 1)) ?>
                            </div>
                            <div>
                                <h5 class="card-title mb-0 fw-bold" style="font-size: 1.1rem;"><?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido']) ?></h5>
                                <small class="text-muted">Solicitante ID: #<?= $c['solicitante_id'] ?></small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <small class="text-uppercase fw-bold text-xs text-primary">Postulado a:</small>
                            <div class="fw-bold text-dark"><?= htmlspecialchars($c['vacante_titulo']) ?></div>
                            <small class="text-muted"><i class="far fa-clock me-1"></i> <?= date('d M Y', strtotime($c['fecha_postulacion'])) ?></small>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="mailto:<?= htmlspecialchars($c['email'] ?? '') ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-envelope"></i> Contactar
                            </a>
                            <a href="#" class="btn btn-primary btn-sm">
                                <i class="fas fa-user-circle"></i> Ver Perfil Completo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

</body>
</html>

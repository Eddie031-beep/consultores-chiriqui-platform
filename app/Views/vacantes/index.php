<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Vacantes | Consultores Chiriquí</title>
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
                <li class="nav-item"><a class="nav-link active" href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes">Mis Vacantes</a></li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="<?= ENV_APP['BASE_URL'] ?>/logout">Salir <i class="fas fa-sign-out-alt"></i></a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Mis Vacantes</h1>
        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes/crear" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Nueva Vacante
        </a>
    </div>

    <!-- Vacancies Card -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Listado de Vacantes</h6>
        </div>
        <div class="card-body">
            <?php if (empty($vacantes)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-folder-open fa-3x mb-3"></i>
                    <p>No tienes vacantes publicadas. ¡Crea la primera!</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Ubicación</th>
                                <th>Modalidad</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vacantes as $v): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($v['titulo']) ?></strong></td>
                                <td><?= htmlspecialchars($v['ubicacion']) ?></td>
                                <td><?= ucfirst($v['modalidad']) ?></td>
                                <td>
                                    <?php if($v['estado'] === 'abierta'): ?>
                                        <span class="badge bg-success">Abierta</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Cerrada</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y', strtotime($v['fecha_publicacion'])) ?></td>
                                <td class="text-end">
                                    <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes/<?= $v['id'] ?>" class="btn btn-sm btn-info text-white me-1">
                                        <i class="fas fa-pencil-alt"></i> Editar
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

</body>
</html>

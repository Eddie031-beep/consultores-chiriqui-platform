<?php
use App\Helpers\Auth;

// Configuración de fecha y hora
date_default_timezone_set('America/Panama');
setlocale(LC_TIME, 'es_PA.UTF-8', 'es_ES.UTF-8', 'spanish');

$user = Auth::user();
$iniciales = strtoupper(substr($user['nombre'], 0, 1) . substr($user['apellido'], 0, 1));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Vacantes | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-animations.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* Estilos del Hero (Reutilizados para consistencia) */
        .hero-header {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            margin-bottom: 2.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            border: 1px solid #f1f5f9;
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .hero-header::after {
            content: ''; position: absolute; top: 0; right: 0; width: 300px; height: 100%;
            background: linear-gradient(90deg, transparent, #f8fafc); pointer-events: none;
        }
        .welcome-text h1 { font-size: 2rem; color: #1e293b; font-weight: 800; margin: 0 0 8px 0; letter-spacing: -0.5px; }
        
        .live-clock {
            color: #64748b; font-size: 1rem; margin: 0; display: flex; align-items: center; gap: 10px;
            font-family: 'Inter', system-ui, sans-serif; font-variant-numeric: tabular-nums; font-weight: 500;
            background: #f1f5f9; padding: 6px 14px; border-radius: 6px; width: fit-content; border: 1px solid #e2e8f0;
        }
        .live-clock i { color: #2563eb; }

        /* Perfil Pill */
        .profile-pill {
            display: flex; align-items: center; gap: 12px; background: #ffffff;
            border: 1px solid #e2e8f0; padding: 8px 12px 8px 8px; border-radius: 50px;
            transition: all 0.2s ease; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            position: relative; z-index: 20;
        }
        .profile-pill:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); transform: translateY(-1px); border-color: #cbd5e1; }
        .avatar-circle {
            width: 40px; height: 40px; background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.9rem;
        }
        .profile-info { display: flex; flex-direction: column; margin-right: 10px; }
        .profile-name { font-weight: 700; font-size: 0.9rem; color: #334155; line-height: 1.2; }
        .profile-role { font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Dropdown */
        .dropdown-menu {
            position: absolute; top: 110%; right: 0; background: white; border: 1px solid #e2e8f0;
            border-radius: 12px; width: 180px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.2s ease; overflow: hidden;
        }
        .profile-pill:hover .dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }
        .dropdown-item { display: block; padding: 10px 15px; color: #475569; text-decoration: none; font-size: 0.9rem; transition: background 0.2s; }
        .dropdown-item:hover { background: #f1f5f9; color: #2563eb; }
        .dropdown-item.logout { color: #ef4444; border-top: 1px solid #f1f5f9; }
        .dropdown-item.logout:hover { background: #fef2f2; }

        /* Tabla */
        .table-container {
            background: white; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0; overflow: hidden;
        }
    </style>
</head>
<body>

    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="dashboard-wrapper">
        
        <div class="hero-header animate-slide-up">
            <div class="welcome-text">
                <h1>Gestión de Vacantes</h1>
                <div class="live-clock">
                    <i class="far fa-clock"></i>
                    <span id="reloj">Cargando...</span>
                </div>
            </div>
            
            <div class="profile-pill">
                <div class="avatar-circle"><?= $iniciales ?></div>
                <div class="profile-info">
                    <span class="profile-name"><?= htmlspecialchars($user['nombre']) ?></span>
                    <span class="profile-role">Empresa</span>
                </div>
                <i class="fas fa-chevron-down" style="color: #cbd5e1; font-size: 0.8rem;"></i>

                <div class="dropdown-menu">
                    <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/perfil" class="dropdown-item"><i class="fas fa-building"></i> Perfil Empresa</a>
                    <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/facturacion" class="dropdown-item"><i class="fas fa-file-invoice"></i> Facturación</a>
                    <a href="<?= ENV_APP['BASE_URL'] ?>/logout" class="dropdown-item logout"><i class="fas fa-sign-out-alt"></i> Salir</a>
                </div>
            </div>
        </div>

        <?php if(isset($_SESSION['mensaje'])): ?>
            <div class="animate-slide-up" style="padding: 1rem; border-radius: 10px; margin-bottom: 2rem; display: flex; align-items: center; gap: 10px; font-weight: 500; background: <?= $_SESSION['mensaje']['tipo'] === 'success' ? '#dcfce7' : '#fee2e2' ?>; color: <?= $_SESSION['mensaje']['tipo'] === 'success' ? '#166534' : '#991b1b' ?>; border: 1px solid <?= $_SESSION['mensaje']['tipo'] === 'success' ? '#bbf7d0' : '#fecaca' ?>;">
                <i class="fas <?= $_SESSION['mensaje']['tipo'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
                <?= $_SESSION['mensaje']['texto'] ?>
            </div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>

        <div class="animate-slide-up delay-100">
            <div style="display: flex; justify-content: flex-end; margin-bottom: 1.5rem;">
                <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes/crear" class="btn-primary" style="padding: 0.8rem 1.5rem; border-radius: 10px; font-size: 1rem; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);">
                    <i class="fas fa-plus-circle"></i> Publicar Nueva Vacante
                </a>
            </div>

            <div class="table-container" style="max-height: 600px; overflow-y: auto; border: 1px solid #e2e8f0;">
                <style>
                    /* Sticky Header for this table */
                    .premium-table thead th { position: sticky; top: 0; z-index: 10; background: #f8fafc; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
                </style>
                <?php if (empty($vacantes)): ?>
                    <div style="text-align: center; padding: 4rem; color: #94a3b8;">
                        <i class="fas fa-folder-open" style="font-size: 3rem; margin-bottom: 1rem; color: #e2e8f0;"></i>
                        <h3 style="color: #64748b; font-size: 1.2rem; margin-bottom: 0.5rem;">No tienes vacantes activas</h3>
                        <p>Publica tu primera oferta para empezar a recibir talento.</p>
                    </div>
                <?php else: ?>
                    <table class="premium-table">
                        <thead>
                            <tr>
                                <th>Título del Puesto</th>
                                <th>Ubicación</th>
                                <th>Modalidad</th>
                                <th>Candidatos</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vacantes as $v): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: #1e293b; font-size: 1rem;"><?= htmlspecialchars($v['titulo']) ?></div>
                                    <div style="font-size: 0.8rem; color: #94a3b8;">ID: #<?= $v['id'] ?></div>
                                </td>
                                <td><i class="fas fa-map-marker-alt" style="color: #94a3b8;"></i> <?= htmlspecialchars($v['ubicacion']) ?></td>
                                <td>
                                    <span style="background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; color: #475569; font-weight: 500;">
                                        <?= ucfirst($v['modalidad']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/postulantes?vacante_id=<?= $v['id'] ?>" style="text-decoration: none; font-weight: 600; color: #2563eb;">
                                        Ver postulantes &rarr;
                                    </a>
                                </td>
                                <td>
                                    <?php if($v['estado'] === 'abierta'): ?>
                                        <span class="status-badge status-active">Abierta</span>
                                    <?php else: ?>
                                        <span class="status-badge status-inactive">Cerrada</span>
                                    <?php endif; ?>
                                </td>
                                <td style="color: #64748b; font-size: 0.9rem;"><?= date('d/m/Y', strtotime($v['fecha_publicacion'])) ?></td>
                                <td>
                                    <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes/<?= $v['id'] ?>" class="action-icon" title="Editar Vacante">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <?php if($v['estado'] === 'abierta'): ?>
                                        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes/cerrar/<?= $v['id'] ?>" 
                                           class="action-icon" 
                                           title="Cerrar Vacante"
                                           onclick="return confirm('¿Estás seguro que deseas cerrar esta vacante? Ya no recibirás más postulantes.');"
                                           style="color: #ef4444; margin-left: 5px;">
                                            <i class="fas fa-ban"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes/abrir/<?= $v['id'] ?>" 
                                           class="action-icon" 
                                           title="Reabrir Vacante"
                                           onclick="return confirm('¿Deseas reabrir esta vacante para recibir más postulantes?');"
                                           style="color: #10b981; margin-left: 5px;">
                                            <i class="fas fa-redo"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes/interacciones/<?= $v['id'] ?>" 
                                       class="action-icon" 
                                       title="Ver Reporte de Vistas"
                                       style="color: #2563eb; margin-left: 5px;">
                                        <i class="fas fa-chart-line"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <script>
        function actualizarReloj() {
            const ahora = new Date();
            const opcionesFecha = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const fechaStr = ahora.toLocaleDateString('es-ES', opcionesFecha);
            let horas = ahora.getHours();
            const minutos = String(ahora.getMinutes()).padStart(2, '0');
            const segundos = String(ahora.getSeconds()).padStart(2, '0');
            const ampm = horas >= 12 ? 'p.m.' : 'a.m.';
            horas = horas % 12; horas = horas ? horas : 12;
            
            const fechaCapitalizada = fechaStr.charAt(0).toUpperCase() + fechaStr.slice(1);
            document.getElementById('reloj').textContent = `${fechaCapitalizada} | ${horas}:${minutos}:${segundos} ${ampm}`;
        }
        actualizarReloj(); setInterval(actualizarReloj, 1000);
    </script>

</body>
</html>

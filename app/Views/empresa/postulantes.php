<?php
use App\Helpers\Auth;
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Candidatos por Vacante</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-animations.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .split-layout { display: grid; grid-template-columns: 300px 1fr; gap: 2rem; align-items: start; }
        .sidebar-list { background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; }
        .sidebar-item { display: block; padding: 1rem; border-bottom: 1px solid #f1f5f9; cursor: pointer; text-decoration: none; color: #475569; transition: all 0.2s; }
        .sidebar-item:hover, .sidebar-item.active { background: #eff6ff; color: #2563eb; font-weight: 600; border-left: 4px solid #2563eb; }
        .sidebar-item:last-child { border-bottom: none; }
        
        .candidate-card { background: white; border-radius: 12px; padding: 1.5rem; border: 1px solid #e2e8f0; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center; transition: transform 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .candidate-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px rgba(0,0,0,0.05); border-color: #cbd5e1; }
        
        .badge-pill { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; background: #f1f5f9; color: #64748b; margin-right: 5px; }
        
        /* ALERT STYLES */
        body { padding-top: 90px; }
        .alert-box {
            padding: 1rem 1.5rem; 
            border-radius: 12px; 
            margin-bottom: 2rem;
            display: flex; 
            align-items: center; 
            gap: 12px; 
            font-weight: 600;
            animation: slideDown 0.4s ease-out;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        @keyframes slideDown { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }

        @media(max-width: 768px) { .split-layout { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<?php include __DIR__ . '/../components/navbar.php'; ?>

<div class="dashboard-wrapper">
    <?php if(isset($_SESSION['mensaje'])): ?>
        <div class="alert-box <?= $_SESSION['mensaje']['tipo'] === 'success' ? 'alert-success' : 'alert-error' ?>">
            <i class="fas <?= $_SESSION['mensaje']['tipo'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
            <?= $_SESSION['mensaje']['texto'] ?>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <div class="page-header animate-slide-up">
        <div>
            <h2><i class="fas fa-users" style="color: #2563eb; margin-right: 10px;"></i> Candidatos</h2>
            <p style="color: #64748b; margin: 5px 0 0;">Selecciona una vacante para ver quiénes se han postulado.</p>
        </div>
        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/dashboard" class="back-btn"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <div class="split-layout animate-slide-up delay-100">
        <div class="sidebar-list">
            <div style="padding: 1rem; background: #f8fafc; font-weight: 700; color: #1e293b; border-bottom: 1px solid #e2e8f0;">
                Tus Vacantes Activas
            </div>
            <?php if(empty($vacantes)): ?>
                <div style="padding: 2rem; text-align: center; color: #94a3b8; font-size: 0.9rem;">No hay postulantes aún.</div>
            <?php else: ?>
                <?php foreach($vacantes as $v): ?>
                    <a href="?vacante_id=<?= $v['id'] ?>" class="sidebar-item <?= $selectedVacante == $v['id'] ? 'active' : '' ?>">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span><?= htmlspecialchars($v['titulo']) ?></span>
                            <span style="background: #e2e8f0; padding: 2px 8px; border-radius: 10px; font-size: 0.75rem;">
                                <?= $v['total_postulantes'] ?>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="candidates-area">
            <?php if(!$selectedVacante): ?>
                <div style="text-align: center; padding: 4rem; color: #cbd5e1;">
                    <i class="fas fa-mouse-pointer fa-3x"></i>
                    <p style="margin-top: 1rem; color: #94a3b8;">Selecciona una vacante a la izquierda.</p>
                </div>
            <?php elseif(empty($candidatos)): ?>
                <div style="text-align: center; padding: 3rem; background: white; border-radius: 12px;">
                    <p>No hay candidatos para esta vacante.</p>
                </div>
            <?php else: ?>
                <h3 style="margin-bottom: 1.5rem; color: #334155;">Postulantes (<?= count($candidatos) ?>)</h3>
                <?php foreach($candidatos as $c): ?>
                    <div class="candidate-card animate-slide-up">
                        <div style="display: flex; gap: 1.5rem; align-items: center;">
                            <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #2563eb, #60a5fa); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold;">
                                <?= strtoupper(substr($c['nombre'], 0, 1)) ?>
                            </div>
                            <div>
                                <h4 style="margin: 0; color: #1e293b; font-size: 1.1rem;"><?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido']) ?></h4>
                                <div style="color: #64748b; font-size: 0.9rem; margin: 5px 0;">
                                    <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($c['ciudad'] ?? 'Panamá') ?> • 
                                    <i class="fas fa-envelope"></i> <?= htmlspecialchars($c['email']) ?>
                                </div>
                                <div style="margin-top: 8px;">
                                    <?php if(!empty($c['habilidades'])): 
                                        $skills = explode(',', $c['habilidades']);
                                        foreach(array_slice($skills, 0, 3) as $skill): ?>
                                        <span class="badge-pill"><?= htmlspecialchars(trim($skill)) ?></span>
                                    <?php endforeach; endif; ?>
                                </div>
                            </div>
                        </div>
                        <div style="text-align: right; min-width: 180px;">
                            <div style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 10px;">
                                Postulado: <?= date('d/m/Y', strtotime($c['fecha_postulacion'])) ?>
                            </div>
                            
                            <?php if($c['estado'] === 'pendiente' || $c['estado'] === 'revisado'): ?>
                                <div style="display: flex; gap: 5px; justify-content: flex-end;">
                                    <form action="<?= ENV_APP['BASE_URL'] ?>/empresa/postulacion/estado" method="POST">
                                        <input type="hidden" name="postulacion_id" value="<?= $c['id'] ?>"> <input type="hidden" name="vacante_id" value="<?= $selectedVacante ?>">
                                        <input type="hidden" name="nuevo_estado" value="aceptado">
                                        <button type="submit" class="btn-primary" style="background: #10b981; padding: 6px 12px; font-size: 0.8rem;" title="Aceptar Candidato">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>

                                    <form action="<?= ENV_APP['BASE_URL'] ?>/empresa/postulacion/estado" method="POST" onsubmit="return confirm('¿Estás seguro de rechazar a este candidato?');">
                                        <input type="hidden" name="postulacion_id" value="<?= $c['id'] ?>">
                                        <input type="hidden" name="vacante_id" value="<?= $selectedVacante ?>">
                                        <input type="hidden" name="nuevo_estado" value="rechazado">
                                        <button type="submit" class="btn-primary" style="background: #ef4444; padding: 6px 12px; font-size: 0.8rem;" title="Rechazar Candidato">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span class="status-badge <?= $c['estado'] ?>">
                                    <?= ucfirst($c['estado']) ?>
                                </span>
                            <?php endif; ?>

                            <div style="margin-top: 10px; text-align: right;">
                                <?php if(!empty($c['cv_ruta'])): ?>
                                    <a href="<?= ENV_APP['BASE_URL'] . $c['cv_ruta'] ?>" target="_blank" class="btn-primary" style="padding: 6px 12px; font-size: 0.8rem; background: #2563eb; display: inline-block;">
                                        <i class="fas fa-file-pdf"></i> Ver CV
                                    </a>
                                <?php else: ?>
                                    <span style="color: #94a3b8; font-size: 0.85rem; font-style: italic;">Sin CV adjunto</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>

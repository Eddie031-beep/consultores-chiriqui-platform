<?php
use App\Helpers\Auth; // <--- ESTA LÍNEA FALTABA Y CAUSABA EL ERROR

$vacante = $vacante ?? [];
$isAuthenticated = Auth::check(); // Usamos Auth directamente para ser consistentes
$resenas = $resenas ?? [];
$promedio = $promedio ?? 0;
$miResena = $miResena ?? null;
$haPostulado = $haPostulado ?? false;

// Helpers para formato
$fechaCierre = $vacante['fecha_cierre'] ? date('d/m/Y', strtotime($vacante['fecha_cierre'])) : date('d/m/Y', strtotime('+30 days'));
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($vacante['titulo']) ?> - Detalle</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #2563eb; --text-dark: #1e293b; --text-gray: #475569; }
        
        body { 
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
            padding-top: 90px; 
        }

        /* HERO HEADER */
        .job-hero {
            background: radial-gradient(circle at top right, #1e3a8a 0%, #0f172a 100%);
            padding: 2rem 1rem 3rem 1rem;
            color: white;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        
        .back-link {
            color: rgba(255,255,255,0.8); text-decoration: none; font-weight: 500;
            display: inline-flex; align-items: center; gap: 8px; margin-bottom: 1.5rem;
            font-size: 0.9rem; transition: color 0.2s;
        }
        .back-link:hover { color: white; text-decoration: underline; }

        .job-title { font-size: 2.2rem; font-weight: 800; line-height: 1.2; margin-bottom: 1rem; }
        .job-meta-hero { display: flex; flex-wrap: wrap; gap: 20px; font-size: 1rem; opacity: 0.95; align-items: center; }
        .job-meta-hero span { display: flex; align-items: center; gap: 6px; }
        .job-meta-hero i { color: #60a5fa; }

        /* LAYOUT PRINCIPAL */
        .job-container {
            max-width: 1150px; 
            margin: 0 auto 4rem auto;
            padding: 0 1.5rem;
            display: grid; 
            grid-template-columns: 2.5fr 1fr;
            gap: 2.5rem;
            align-items: start;
        }

        /* TARJETAS DE CONTENIDO */
        .content-card {
            background: white; border-radius: 12px; padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; 
            margin-bottom: 2rem;
        }

        /* Highlights Grid */
        .highlights-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
        .highlight-box { 
            text-align: center; padding: 15px; background: #f8fafc; 
            border-radius: 8px; border: 1px solid #e2e8f0; 
        }
        .highlight-label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 5px; }
        .highlight-val { font-size: 1rem; color: #1e293b; font-weight: 700; }
        .highlight-icon { font-size: 1.5rem; color: var(--primary); margin-bottom: 8px; }

        /* Estilos de Texto */
        .job-content h2 { 
            font-size: 1.4rem; color: #1e293b; margin: 0 0 1rem 0; 
            border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; font-weight: 700;
        }
        .job-content p { line-height: 1.8; color: #334155; margin-bottom: 1.5rem; }
        .job-list { padding-left: 20px; margin-bottom: 1.5rem; }
        .job-list li { margin-bottom: 0.5rem; color: #334155; }

        /* SIDEBAR (Empresa) */
        .company-card { 
            background: white; border-radius: 12px; padding: 2rem; 
            border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            position: sticky; top: 100px; 
        }
        
        .company-header { text-align: center; margin-bottom: 1.5rem; }
        .company-type-badge {
            display: inline-block; padding: 4px 10px; border-radius: 20px; 
            font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
            background: #f1f5f9; color: #475569; margin-top: 5px;
        }

        .btn-apply {
            display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%;
            background: var(--primary); color: white; padding: 14px; border-radius: 8px;
            font-weight: 700; text-decoration: none; transition: background 0.2s;
            border: none; cursor: pointer; text-align: center; margin-bottom: 10px;
        }
        .btn-apply:hover { background: #1d4ed8; transform: translateY(-1px); }

        .btn-more-jobs {
            display: block; width: 100%; background: white; color: #2563eb; padding: 0.8rem;
            border-radius: 10px; font-weight: 600; text-decoration: none; font-size: 0.95rem;
            border: 1px solid #2563eb; text-align: center; transition: all 0.2s;
        }
        .btn-more-jobs:hover { background: #f0f9ff; }

        /* MENSAJES DE ALERTA */
        .alert-box {
            padding: 1rem; border-radius: 8px; margin-bottom: 2rem;
            display: flex; align-items: center; gap: 10px; font-weight: 500;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        @media (max-width: 900px) { .job-container { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="job-hero">
        <div class="container" style="max-width: 1150px; padding: 0 1.5rem; margin: 0 auto;">
            <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes" class="back-link">
                <i class="fas fa-arrow-left"></i> Volver al listado
            </a>
            
            <h1 class="job-title"><?= htmlspecialchars($vacante['titulo']) ?></h1>
            
            <div class="job-meta-hero">
                <span><i class="fas fa-building"></i> <?= htmlspecialchars($vacante['empresa_nombre']) ?></span>
                <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($vacante['ubicacion']) ?></span>
                <span><i class="far fa-clock"></i> Publicado: <?= date('d/m/Y', strtotime($vacante['fecha_publicacion'])) ?></span>
            </div>
        </div>
    </div>

    <div class="job-container">
        
        <div class="main-content">
            
            <?php if(isset($_SESSION['mensaje'])): ?>
                <div class="alert-box <?= $_SESSION['mensaje']['tipo'] === 'success' ? 'alert-success' : 'alert-error' ?>">
                    <i class="fas <?= $_SESSION['mensaje']['tipo'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
                    <?= $_SESSION['mensaje']['texto'] ?>
                </div>
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif; ?>

            <div class="content-card" style="padding: 1.5rem;">
                <div class="highlights-grid">
                    <div class="highlight-box">
                        <i class="fas fa-money-bill-wave highlight-icon"></i>
                        <span class="highlight-label">Salario Estimado</span>
                        <div class="highlight-val">
                            <?php if ($vacante['salario_min']): ?>
                                B/. <?= number_format($vacante['salario_min'], 0) ?> - <?= number_format($vacante['salario_max'], 0) ?>
                            <?php else: ?>
                                Competitivo
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="highlight-box">
                        <i class="fas fa-laptop-house highlight-icon"></i>
                        <span class="highlight-label">Modalidad</span>
                        <div class="highlight-val"><?= ucfirst($vacante['modalidad']) ?></div>
                    </div>
                    <div class="highlight-box">
                        <i class="far fa-file-alt highlight-icon"></i>
                        <span class="highlight-label">Contrato</span>
                        <div class="highlight-val"><?= htmlspecialchars($vacante['tipo_contrato']) ?></div>
                    </div>
                </div>
            </div>

            <div class="content-card job-content">
                <h2>Descripción del Puesto</h2>
                
                <?php if (strlen($vacante['descripcion']) > 100): ?>
                    <div style="white-space: pre-line; line-height: 1.8; color: #334155;">
                        <?= nl2br(htmlspecialchars($vacante['descripcion'])) ?>
                    </div>
                <?php else: ?>
                    <p>En <strong><?= htmlspecialchars($vacante['empresa_nombre']) ?></strong> buscamos talento apasionado.</p>
                    <h3 style="font-size:1.1rem; color:#1e293b; font-weight:700; margin-top:1.5rem; margin-bottom: 1rem;">Responsabilidades</h3>
                    <ul class="job-list">
                        <li>Cumplir con las tareas asignadas con eficiencia.</li>
                        <li>Trabajar en equipo y comunicar avances.</li>
                    </ul>
                <?php endif; ?>

                <div style="margin-top: 2rem; border-top: 1px solid #f1f5f9; padding-top: 1.5rem; text-align: center;">
                    <?php if ($isAuthenticated): ?>
                        <?php if ($haPostulado): ?>
                            <span style="color: #10b981; font-weight: 600;"><i class="fas fa-check-circle"></i> Ya te has postulado</span>
                        <?php else: ?>
                            <a href="<?= ENV_APP['BASE_URL'] ?>/postular/<?= $vacante['id'] ?>" class="btn-apply" style="max-width: 300px; margin: 0 auto;">
                                Postularme Ahora <i class="fas fa-paper-plane"></i>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="content-card" id="resenas-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                    <h2 style="margin: 0; border: none;">Opiniones de Candidatos</h2>
                    <div style="text-align: right;">
                        <span style="font-size: 2rem; font-weight: 800; color: #fbbf24;"><?= $promedio ?></span>
                        <span style="color: #94a3b8; font-size: 0.9rem;">/ 5.0 (<?= count($resenas) ?> reseñas)</span>
                    </div>
                </div>

                <?php if (Auth::check() && Auth::user()['rol'] === 'candidato'): ?>
                    <div style="background: #f8fafc; padding: 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 2rem;">
                        <h4 style="margin-top: 0; color: #1e293b;"><?= $miResena ? 'Editar tu reseña' : 'Deja tu opinión' ?></h4>
                        
                        <form action="<?= ENV_APP['BASE_URL'] ?>/resenas/guardar" method="POST">
                            <input type="hidden" name="vacante_id" value="<?= $vacante['id'] ?>">
                            
                            <div class="star-rating" style="margin-bottom: 10px; display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 5px;">
                                <?php $cal = $miResena['calificacion'] ?? 5; ?>
                                <?php for($i=5; $i>=1; $i--): ?>
                                    <input type="radio" id="star<?= $i ?>" name="calificacion" value="<?= $i ?>" <?= $cal == $i ? 'checked' : '' ?> style="display: none;">
                                    <label for="star<?= $i ?>" style="cursor: pointer; font-size: 1.5rem; color: #cbd5e1; transition: color 0.2s;">★</label>
                                <?php endfor; ?>
                            </div>
                            <style>
                                .star-rating input:checked ~ label,
                                .star-rating label:hover,
                                .star-rating label:hover ~ label { color: #fbbf24 !important; }
                            </style>

                            <textarea name="comentario" rows="3" placeholder="Comparte tu experiencia..." style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; margin-bottom: 10px;" required><?= htmlspecialchars($miResena['comentario'] ?? '') ?></textarea>
                            
                            <div style="display: flex; justify-content: space-between;">
                                <?php if($miResena): ?>
                                    <button type="submit" formaction="<?= ENV_APP['BASE_URL'] ?>/resenas/eliminar/<?= $miResena['id'] ?>" onclick="return confirm('¿Borrar reseña?')" style="background: none; border: none; color: #ef4444; cursor: pointer;">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                <?php else: ?>
                                    <span></span>
                                <?php endif; ?>
                                <button type="submit" class="btn-apply" style="width: auto; padding: 8px 20px; font-size: 0.9rem; margin: 0;">
                                    <?= $miResena ? 'Actualizar' : 'Publicar' ?>
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

                <div class="reviews-list">
                    <?php if (empty($resenas)): ?>
                        <p style="color: #94a3b8; font-style: italic; text-align: center;">Aún no hay opiniones.</p>
                    <?php else: ?>
                        <?php foreach ($resenas as $r): ?>
                            <div style="border-bottom: 1px solid #f1f5f9; padding: 1rem 0;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                    <strong style="color: #334155;"><?= htmlspecialchars($r['nombre'] . ' ' . $r['apellido']) ?></strong>
                                    <div style="color: #fbbf24;">
                                        <?php for($i=0; $i<$r['calificacion']; $i++) echo '★'; ?>
                                        <span style="color: #cbd5e1; font-size: 0.8rem;"><?= date('d/m/Y', strtotime($r['fecha_creacion'])) ?></span>
                                    </div>
                                </div>
                                <p style="margin: 0; color: #475569; font-size: 0.95rem;"><?= nl2br(htmlspecialchars($r['comentario'])) ?></p>
                                
                                <div style="margin-top: 8px; display: flex; gap: 10px; font-size: 0.8rem;">
                                    <?php if(Auth::check()): ?>
                                        <form action="<?= ENV_APP['BASE_URL'] ?>/resenas/reportar/<?= $r['id'] ?>" method="POST" style="display:inline;">
                                            <button type="submit" style="background:none; border:none; color: #94a3b8; cursor: pointer;">
                                                <i class="fas fa-flag"></i> Reportar
                                            </button>
                                        </form>

                                        <?php 
                                            // CORRECCIÓN: Usamos el operador de fusión null (??) para evitar el error
                                            // si el usuario es candidato y no tiene 'empresa_id'
                                            $userEmpresaId = Auth::user()['empresa_id'] ?? null; 
                                            
                                            if(isset($vacante['empresa_id']) && $userEmpresaId == $vacante['empresa_id']): 
                                        ?>
                                            <form action="<?= ENV_APP['BASE_URL'] ?>/resenas/eliminar/<?= $r['id'] ?>" method="POST" onsubmit="return confirm('¿Eliminar este comentario como moderador?');" style="display:inline;">
                                                <button type="submit" style="background:none; border:none; color: #ef4444; cursor: pointer;">
                                                    <i class="fas fa-trash-alt"></i> Borrar (Moderador)
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="sidebar">
            <div class="company-card">
                <div class="company-header">
                    <div style="width: 70px; height: 70px; background: #eff6ff; color: #2563eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 10px;">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3 style="margin: 0; color: #1e293b; font-size: 1.2rem; font-weight: 700;"><?= htmlspecialchars($vacante['empresa_nombre']) ?></h3>
                    
                    <?php if (!empty($vacante['tipo'])): ?>
                        <span class="company-type-badge">Empresa <?= ucfirst($vacante['tipo']) ?></span>
                    <?php endif; ?>
                    
                    <p style="font-size: 0.85rem; color: #64748b; margin-top: 10px;">
                        <?= htmlspecialchars($vacante['sector'] ?? 'Sector General') ?>
                    </p>
                </div>

                <div style="font-size: 0.9rem; color: #475569; margin-bottom: 1.5rem;">
                    <p style="margin-bottom: 8px;">
                        <i class="fas fa-map-marker-alt" style="color:#94a3b8; width:16px;"></i> 
                        <?= htmlspecialchars($vacante['direccion'] ?? 'Panamá') ?>
                    </p>
                    <p style="margin-bottom: 8px;">
                        <i class="fas fa-envelope" style="color:#94a3b8; width:16px;"></i> 
                        <?= htmlspecialchars($vacante['email_contacto'] ?? 'No visible') ?>
                    </p>
                    <p>
                        <i class="fas fa-calendar-check" style="color:#94a3b8; width:16px;"></i> 
                        Registrada en <?= date('Y', strtotime($vacante['empresa_registro'] ?? 'now')) ?>
                    </p>
                </div>

                <?php if ($isAuthenticated): ?>
                    <?php if ($haPostulado): ?>
                        <div style="background: #ecfdf5; border: 1px solid #10b981; color: #047857; padding: 10px; border-radius: 8px; text-align: center; margin-bottom: 15px; font-size: 0.9rem;">
                            <i class="fas fa-check-circle"></i> Ya te has postulado
                        </div>
                        <form action="<?= ENV_APP['BASE_URL'] ?>/candidato/cancelar-postulacion" method="POST" onsubmit="return confirm('¿Retirar postulación?');">
                            <input type="hidden" name="vacante_id" value="<?= $vacante['id'] ?>">
                            <input type="hidden" name="redirect" value="/vacantes/<?= $vacante['slug'] ?>">
                            <button type="submit" class="btn-apply" style="background: white; color: #ef4444; border: 2px solid #fecaca; box-shadow: none;">
                                Cancelar Postulación
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="<?= ENV_APP['BASE_URL'] ?>/postular/<?= $vacante['id'] ?>" class="btn-apply">
                            Postularme Ahora
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?= ENV_APP['BASE_URL'] ?>/auth/registro?tipo=candidato&vacante_id=<?= $vacante['id'] ?>" class="btn-apply">
                        Regístrate para Aplicar
                    </a>
                    <p style="text-align: center; font-size: 0.8rem; color: #94a3b8; margin-top: 8px;">Es gratis y rápido</p>
                <?php endif; ?>

                <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                    <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes?empresa=<?= $vacante['empresa_id'] ?>" class="btn-more-jobs">
                        Ver más vacantes de esta empresa
                    </a>
                </div>

            </div>
        </div>

    </div>
</body>
</html>
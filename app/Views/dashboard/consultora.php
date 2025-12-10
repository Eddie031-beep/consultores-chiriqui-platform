<?php
use App\Helpers\Auth;

// Configuración de fecha
date_default_timezone_set('America/Panama');
setlocale(LC_TIME, 'es_PA.UTF-8', 'es_ES.UTF-8', 'spanish');

$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Consultora - Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-animations.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* Estilos del Hero Limpio */
        .hero-header {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            margin-bottom: 2.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            border: 1px solid #f1f5f9;
            position: relative;
            overflow: hidden;
            /* Centrar contenido verticalmente si es necesario */
            display: flex;
            align-items: center;
        }

        .hero-header::after {
            content: ''; position: absolute; top: 0; right: 0; width: 300px; height: 100%;
            background: linear-gradient(90deg, transparent, #f8fafc); pointer-events: none;
        }

        .welcome-text h1 { 
            font-size: 2rem; 
            color: #1e293b; 
            font-weight: 800; 
            margin: 0 0 8px 0; 
            letter-spacing: -0.5px;
        }
        
        /* Estilo del Reloj Profesional */
        .live-clock {
            color: #64748b;
            font-size: 1rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            /* Fuente estándar pero con números tabulares para que no salten */
            font-family: 'Inter', system-ui, sans-serif; 
            font-variant-numeric: tabular-nums;
            font-weight: 500;
            
            background: #f1f5f9;
            padding: 6px 14px;
            border-radius: 6px;
            width: fit-content;
            border: 1px solid #e2e8f0;
        }
        .live-clock i { color: #2563eb; }
    </style>
</head>
<body>

    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="dashboard-wrapper">
        
        <div class="hero-header animate-slide-up">
            <div class="welcome-text">
                <h1>Hola, <?= htmlspecialchars($user['nombre']) ?></h1>
                
                <div class="live-clock">
                    <i class="far fa-clock"></i>
                    <span id="reloj">Cargando...</span>
                </div>
            </div>
            </div>

        <div class="dashboard-grid">
            <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/empresas" class="glass-card" style="animation-delay: 0.1s;">
                <div class="card-icon-wrapper" style="background: #eff6ff; color: #2563eb;"><i class="fas fa-building"></i></div>
                <div class="card-content"><h3>Gestionar Empresas</h3><p>Administra registros y contratos.</p></div>
                <div class="card-arrow">➜</div>
            </a>
            <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion/estadisticas" class="glass-card" style="animation-delay: 0.2s;">
                <div class="card-icon-wrapper" style="background: #f0fdf4; color: #16a34a;"><i class="fas fa-chart-pie"></i></div>
                <div class="card-content"><h3>Métricas de Uso</h3><p>Analiza el tráfico y rendimiento.</p></div>
                <div class="card-arrow">➜</div>
            </a>
            <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion" class="glass-card" style="animation-delay: 0.3s;">
                <div class="card-icon-wrapper" style="background: #fff7ed; color: #ea580c;"><i class="fas fa-file-invoice-dollar"></i></div>
                <div class="card-content"><h3>Facturación</h3><p>Genera reportes fiscales.</p></div>
                <div class="card-arrow">➜</div>
            </a>
            <a href="<?= ENV_APP['BASE_URL'] ?>/chatbot" class="glass-card" style="animation-delay: 0.4s;">
                <div class="card-icon-wrapper" style="background: #faf5ff; color: #9333ea;"><i class="fas fa-robot"></i></div>
                <div class="card-content"><h3>Asistente Virtual</h3><p>Supervisa el chatbot en tiempo real.</p></div>
                <div class="card-arrow">➜</div>
            </a>
            <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/info" class="glass-card" style="animation-delay: 0.5s;">
                <div class="card-icon-wrapper" style="background: #f1f5f9; color: #475569;"><i class="fas fa-cogs"></i></div>
                <div class="card-content"><h3>Configuración</h3><p>Ajustes del sistema.</p></div>
                <div class="card-arrow">➜</div>
            </a>
            <a href="<?= ENV_APP['BASE_URL'] ?>/" class="glass-card" style="animation-delay: 0.6s;">
                <div class="card-icon-wrapper" style="background: #ecfeff; color: #0891b2;"><i class="fas fa-globe"></i></div>
                <div class="card-content"><h3>Sitio Público</h3><p>Visita la plataforma como usuario.</p></div>
                <div class="card-arrow">➜</div>
            </a>
        </div>

        <div class="stats-section animate-slide-up delay-300" style="margin-top: 2.5rem;">
            <div class="section-header">
                <div class="section-title"><i class="fas fa-trophy" style="color: #fbbf24; margin-right: 8px;"></i> Top Empresas</div>
                <div style="color: var(--text-muted); font-size: 0.85rem; background: #f1f5f9; padding: 4px 10px; border-radius: 20px;">Mes Actual</div>
            </div>
            <div class="table-container" style="box-shadow: none; padding: 0; margin: 0; border: none;">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th width="40%">Empresa</th>
                            <th width="20%" class="text-right">Vistas</th>
                            <th width="20%" class="text-right">Postulaciones</th>
                            <th width="20%" class="text-right">A Facturar (Est.)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($topEmpresas)): ?>
                            <tr><td colspan="4" style="text-align: center; padding: 3rem;">No hay actividad reciente.</td></tr>
                        <?php else: ?>
                            <?php foreach($topEmpresas as $stat): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center;">
                                        <div class="company-logo-placeholder"><?= strtoupper(substr($stat['nombre'], 0, 1)) ?></div>
                                        <span style="font-weight: 600; color: #334155;"><?= htmlspecialchars($stat['nombre']) ?></span>
                                    </div>
                                </td>
                                <td class="text-right"><?= $stat['vistas'] ?></td>
                                <td class="text-right"><?= $stat['aplicaciones'] ?></td>
                                <td class="text-right"><span style="background: #ecfccb; color: #4d7c0f; padding: 4px 10px; border-radius: 6px; font-weight: 600;">B/. <?= $stat['facturacion_estimada'] ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
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
            horas = horas % 12;
            horas = horas ? horas : 12; 
            
            const horaStr = `${horas}:${minutos}:${segundos} ${ampm}`;
            
            // Capitalizar texto
            const fechaCapitalizada = fechaStr.charAt(0).toUpperCase() + fechaStr.slice(1);

            document.getElementById('reloj').textContent = `${fechaCapitalizada} | ${horaStr}`;
        }

        actualizarReloj();
        setInterval(actualizarReloj, 1000);
    </script>

</body>
</html>
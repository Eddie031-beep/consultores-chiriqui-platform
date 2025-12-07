<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatos | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-animations.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .candidates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .candidate-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 20px;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        
        .candidate-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary-color);
        }

        .candidate-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .avatar-circle {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .candidate-info h3 {
            margin: 0;
            font-size: 1.1rem;
            color: var(--text-main);
        }

        .candidate-info p {
            margin: 2px 0 0;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            color: var(--text-light);
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .action-row {
            display: flex;
            gap: 10px;
        }

        .btn-view {
            flex: 1;
            padding: 8px;
            text-align: center;
            background: rgba(0, 86, 179, 0.1);
            color: var(--primary-color);
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-view:hover {
            background: var(--primary-color);
            color: white;
        }

        .btn-contact {
            padding: 8px 12px;
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
            border-radius: 6px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-contact:hover {
            background: var(--success);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: var(--text-muted);
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar animate-fade-in">
        <div class="container navbar-content">
            <div class="brand-logo">
                Consultores<span>Chiriquí</span>
            </div>
            <div class="nav-links">
                <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/dashboard" class="nav-item">Dashboard</a>
                <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes" class="nav-item">Mis Vacantes</a>
                <a href="#" class="nav-item active">Candidatos</a>
                <a href="<?= ENV_APP['BASE_URL'] ?>/logout" class="nav-item" style="color: var(--danger);">Salir</a>
            </div>
        </div>
    </nav>

    <div class="container dashboard-wrapper animate-fade-in">
        <div class="welcome-section">
            <div class="welcome-text">
                <h1>Candidatos Postulados</h1>
                <p>Gestione los talentos interesados en sus vacantes.</p>
            </div>
        </div>

        <?php if (empty($candidatos)): ?>
            <div class="card-box empty-state animate-slide-up">
                <div style="font-size: 3rem; margin-bottom: 15px;">👥</div>
                <h3>No hay candidatos aún</h3>
                <p>Cuando alguien se postule a sus vacantes, aparecerán aquí.</p>
            </div>
        <?php else: ?>
            <div class="candidates-grid">
                <?php $delay = 100; ?>
                <?php foreach ($candidatos as $c): ?>
                    <div class="candidate-card animate-slide-up" style="animation-delay: <?= $delay ?>ms">
                        <div class="candidate-header">
                            <div class="avatar-circle">
                                <?= strtoupper(substr($c['nombre'], 0, 1) . substr($c['apellido'], 0, 1)) ?>
                            </div>
                            <div class="candidate-info">
                                <h3><?= htmlspecialchars($c['nombre'] . ' ' . $c['apellido']) ?></h3>
                                <p>Postulado para: <strong><?= htmlspecialchars($c['vacante_titulo']) ?></strong></p>
                            </div>
                        </div>

                        <div class="meta-row">
                            <span>📅 <?= date('d M Y', strtotime($c['fecha_postulacion'])) ?></span>
                            <span>ID Solicitante: #<?= $c['solicitante_id'] ?></span>
                        </div>

                        <div class="action-row">
                            <a href="#" class="btn-view">Ver Perfil</a> 
                            <a href="mailto:<?= htmlspecialchars($c['email'] ?? '') ?>" class="btn-contact" title="Enviar Correo">
                                ✉️
                            </a>
                        </div>
                    </div>
                    <?php $delay += 100; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- ELEGANT THEME TOGGLE (Floating Pill) -->
    <button class="theme-toggle" id="themeToggle" title="Modo Oscuro/Claro" 
            style="position: fixed; bottom: 30px; right: 30px; width: 55px; height: 55px; border-radius: 50%; background: var(--text-main); color: var(--bg-body); border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.2); cursor: pointer; display: grid; place-items: center; z-index: 1000; font-size: 1.5rem; transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
        🌙
    </button>

    <script>
        // Theme Toggle Logic
        const toggleBtn = document.getElementById('themeToggle');
        const html = document.documentElement;
        
        // Check saved theme
        const savedTheme = localStorage.getItem('theme') || 'light';
        html.setAttribute('data-theme', savedTheme);
        updateIcon(savedTheme);

        toggleBtn.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            // Add rotation animation
            toggleBtn.style.transform = 'rotate(360deg)';
            setTimeout(() => toggleBtn.style.transform = 'none', 300);

            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
        });

        function updateIcon(theme) {
            toggleBtn.textContent = theme === 'light' ? '🌙' : '☀️';
        }
    </script>
</body>
</html>

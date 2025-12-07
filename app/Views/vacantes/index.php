<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Vacantes | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-animations.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }

        .data-table th {
            text-align: left;
            padding: 15px;
            border-bottom: 2px solid var(--border-color);
            color: var(--text-muted);
            font-weight: 600;
        }

        .data-table td {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-main);
            vertical-align: middle;
        }

        .data-table tr:hover {
            background-color: rgba(0,0,0,0.02);
        }
        
        [data-theme="dark"] .data-table tr:hover {
            background-color: rgba(255,255,255,0.02);
        }

        .badge-status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-open {
            background: rgba(40, 167, 69, 0.15);
            color: var(--success);
            border: 1px solid rgba(40, 167, 69, 0.2);
        }

        .badge-closed {
            background: rgba(220, 53, 69, 0.15);
            color: var(--danger);
            border: 1px solid rgba(220, 53, 69, 0.2);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
        }

        .btn-edit {
            background: rgba(0, 86, 179, 0.1);
            color: var(--primary-color);
        }

        .btn-edit:hover {
            background: var(--primary-color);
            color: white;
        }

        .btn-close {
            background: transparent;
            color: var(--danger);
            border: 1px solid transparent;
        }

        .btn-close:hover {
            background: rgba(220, 53, 69, 0.1);
            border-color: rgba(220, 53, 69, 0.3);
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
                <a href="#" class="nav-item active">Mis Vacantes</a>
                <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/candidatos" class="nav-item">Candidatos</a>
                <a href="<?= ENV_APP['BASE_URL'] ?>/logout" class="nav-item" style="color: var(--danger);">Salir</a>
            </div>
        </div>
    </nav>

    <div class="container dashboard-wrapper animate-fade-in">
        
        <div class="welcome-section">
            <div class="welcome-text">
                <h1>Mis Vacantes</h1>
                <p>Administre sus ofertas de empleo.</p>
            </div>
            <div>
                <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes/crear" class="action-btn" style="flex-direction: row; padding: 10px 20px;">
                    <span style="font-size: 1.2rem;">📢</span>
                    <span>Nueva Vacante</span>
                </a>
            </div>
        </div>

        <div class="card-box animate-slide-up delay-100" style="padding: 0; overflow: hidden;">
            <?php if (empty($vacantes)): ?>
                <div style="padding: 40px; text-align: center; color: var(--text-muted);">
                    <p>No tienes vacantes publicadas actualmente.</p>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Vacante</th>
                                <th>Ubicación</th>
                                <th>Modalidad</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th style="text-align: right;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vacantes as $v): ?>
                            <tr class="animate-slide-up">
                                <td>
                                    <strong><?= htmlspecialchars($v['titulo']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($v['ubicacion']) ?></td>
                                <td><?= htmlspecialchars($v['modalidad']) ?></td>
                                <td>
                                    <span class="badge-status <?= $v['estado'] === 'abierta' ? 'badge-open' : 'badge-closed' ?>">
                                        <?= htmlspecialchars($v['estado']) ?>
                                    </span>
                                </td>
                                <td><?= date('d M Y', strtotime($v['fecha_publicacion'])) ?></td>
                                <td style="text-align: right;">
                                    <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes/<?= $v['id'] ?>" class="btn-sm btn-edit">
                                        ✏️ Editar
                                    </a>
                                    <?php if ($v['estado'] === 'abierta'): ?>
                                        <!-- Formulario oculto para cerrar -->
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
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

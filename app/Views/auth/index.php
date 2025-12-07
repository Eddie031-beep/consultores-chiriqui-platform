<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Consultores Chiriquí</title>
    <!-- GLOBAL CSS -->
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <!-- ELEGANT AUTH CSS -->
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/auth-elegant.css">
    <style>
        /* Compact Panel Overrides */
        body { margin: 0; padding: 0; }
        .hero-svg { width: 24px; height: 24px; }
        
        .compact-panel {
            background: white;
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 480px;
            text-align: center;
            box-shadow: 0 20px 50px -10px rgba(0, 0, 0, 0.15);
            position: relative;
            z-index: 10;
            border: 1px solid rgba(255,255,255,0.5);
        }

        .compact-header h1 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            color: var(--auth-text-main);
        }

        .compact-header p {
            color: var(--auth-text-muted);
            margin-bottom: 30px;
            font-size: 0.95rem;
        }

        .role-selector {
            display: grid;
            gap: 15px;
            margin-bottom: 30px;
        }

        .role-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 20px;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            text-decoration: none;
            color: var(--auth-text-main);
            transition: all 0.2s ease;
            font-weight: 600;
            background: transparent;
        }

        .role-btn:hover {
            border-color: var(--auth-accent);
            background: rgba(37, 99, 235, 0.05); /* very light blue tint */
            transform: translateY(-2px);
        }

        .role-btn .icon-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .role-btn .arrow {
            color: var(--auth-text-muted);
            transition: transform 0.2s;
        }

        .role-btn:hover .arrow {
            color: var(--auth-accent);
            transform: translateX(4px);
        }

        .divider {
            height: 1px;
            background: var(--border-color);
            margin: 20px 0;
            position: relative;
        }
        
        .divider span {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 0 10px;
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        .admin-link {
            font-size: 0.85rem;
            color: var(--auth-text-muted);
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.2s;
        }
        
        .admin-link:hover {
            opacity: 1;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-portal-wrapper" style="justify-content: center; padding-top: 0;">
        <!-- Floating Shapes (Subdued) -->
        <div class="auth-shape shape-a" style="opacity: 0.3; width: 300px; height: 300px;"></div>
        <div class="auth-shape shape-b" style="opacity: 0.3; width: 200px; height: 200px;"></div>

        <!-- Back to Home Button -->
        <a href="<?= ENV_APP['BASE_URL'] ?>" class="btn-back-home">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Inicio
        </a>

        <div class="compact-panel animate-slide-up">
            <div class="compact-header">
                <div style="margin-bottom: 20px;">
                     <!-- Simple Logo Placeholder or Icon -->
                     <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="var(--auth-accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                     </svg>
                </div>
                <h1>Consultores Chiriquí</h1>
                <p>Selecciona cómo deseas ingresar</p>
            </div>

            <div class="role-selector">
                <!-- CANDIDATE -->
                <a href="<?= ENV_APP['BASE_URL'] ?>/auth/login?tipo=persona" class="role-btn">
                    <div class="icon-wrapper">
                        <svg class="hero-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Soy Candidato</span>
                    </div>
                    <span class="arrow">→</span>
                </a>

                <!-- COMPANY -->
                <a href="<?= ENV_APP['BASE_URL'] ?>/auth/login?tipo=empresa" class="role-btn">
                    <div class="icon-wrapper">
                        <svg class="hero-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                            <line x1="9" y1="22" x2="9" y2="22.01"></line>
                            <line x1="15" y1="22" x2="15" y2="22.01"></line>
                        </svg>
                        <span>Soy Empresa</span>
                    </div>
                    <span class="arrow">→</span>
                </a>
            </div>

            <div class="divider"><span>O registra una cuenta nueva</span></div>

            <div style="display: flex; gap: 10px; justify-content: center; font-size: 0.9rem; margin-bottom: 25px;">
                <a href="<?= ENV_APP['BASE_URL'] ?>/auth/registro?tipo=persona" style="color: var(--auth-accent); text-decoration: none; font-weight: 600;">Candidato</a>
                <span style="color: var(--border-color);">|</span>
                <a href="<?= ENV_APP['BASE_URL'] ?>/auth/registro?tipo=empresa" style="color: var(--auth-accent); text-decoration: none; font-weight: 600;">Empresa</a>
            </div>

            <div style="border-top: 1px solid var(--border-color); padding-top: 20px;">
                <a href="<?= ENV_APP['BASE_URL'] ?>/auth/login?tipo=consultora" class="admin-link">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px; vertical-align: middle;"><path d="M16.5 10.5V6a3 3 0 0 0-3-3h-3a3 3 0 0 0-3 3v4.5"></path><path d="M6 12h12v9H6z"></path></svg>
                    Acceso Administrativo
                </a>
            </div>
        </div>
    </div>

    <!-- Theme Persist Script -->
    <script>
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
</body>
</html>
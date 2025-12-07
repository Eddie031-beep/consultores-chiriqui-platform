<?php
// app/Views/auth/login-unificado.php
$tipo = $tipo ?? 'persona';
$error = $error ?? '';
$email = $email ?? '';

$titles = [
    'persona' => 'Bienvenido de nuevo',
    'candidato' => 'Bienvenido de nuevo',
    'empresa' => 'Portal Corporativo',
    'consultora' => 'Administración'
];

$subtitles = [
    'persona' => 'Ingresa para descubrir tu próximo gran empleo.',
    'candidato' => 'Ingresa para descubrir tu próximo gran empleo.',
    'empresa' => 'Gestiona tus vacantes y encuentra el talento ideal.',
    'consultora' => 'Accede al panel de control global.'
];

// Icons rendered as SVGs dynamically below
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titles[$tipo] ?></title>
    <!-- GLOBAL CSS -->
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <!-- ELEGANT AUTH CSS -->
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/auth-elegant.css">
</head>
<body>
    <div class="auth-page-wrapper">
        
        <!-- LEFT SIDE: VISUAL EXPERIENCE -->
        <div class="auth-visual-side">
            <!-- Animated Background Shapes -->
            <div class="auth-shape shape-a"></div>
            <div class="auth-shape shape-b"></div>
            
            <div class="auth-visual-content">
                <div style="margin-bottom: 30px; color: white;">
                    <?php if($tipo === 'persona' || $tipo === 'candidato'): ?>
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    <?php elseif($tipo === 'empresa'): ?>
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                            <path d="M9 22v-4h6v4"></path>
                            <path d="M8 6h.01"></path><path d="M16 6h.01"></path><path d="M12 6h.01"></path><path d="M12 10h.01"></path><path d="M12 14h.01"></path><path d="M16 10h.01"></path><path d="M16 14h.01"></path><path d="M8 10h.01"></path><path d="M8 14h.01"></path>
                        </svg>
                    <?php else: ?>
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                            <line x1="8" y1="21" x2="16" y2="21"></line>
                            <line x1="12" y1="17" x2="12" y2="21"></line>
                        </svg>
                    <?php endif; ?>
                </div>
                <h1 class="visual-title"><?= $titles[$tipo] ?></h1>
                <p class="visual-text"><?= $subtitles[$tipo] ?></p>
            </div>
        </div>

        <!-- RIGHT SIDE: INTERACTIVE FORM -->
        <div class="auth-form-side" style="position: relative;">
            <a href="<?= ENV_APP['BASE_URL'] ?>" style="position: absolute; top: 20px; right: 20px; text-decoration: none; color: #64748b; font-weight: 600; display: flex; align-items: center; gap: 5px; z-index: 10;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                Inicio
            </a>
            <div class="auth-card-modern">
                <div class="auth-header">
                    <h2>Iniciar Sesión</h2>
                    <p>Introduce tus credenciales para continuar</p>
                </div>

                <?php if ($error): ?>
                    <div style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 15px; border-radius: 12px; margin-bottom: 25px; border-left: 4px solid #ef4444; display: flex; align-items: center; gap: 10px; font-size: 0.95rem;">
                         <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= ENV_APP['BASE_URL'] ?>/auth/login" id="loginForm">
                    <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo) ?>">

                    <div class="floating-group">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="input-elegant" 
                            placeholder=" " 
                            value="<?= htmlspecialchars($email) ?>"
                            required 
                            autofocus
                        >
                        <label for="email" class="label-elegant">Correo Electrónico</label>
                    </div>

                    <div class="floating-group">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="input-elegant" 
                            placeholder=" " 
                            required
                        >
                        <label for="password" class="label-elegant">Contraseña</label>
                    </div>

                    <div style="text-align: right; margin-bottom: 20px;">
                        <a href="#" class="link-elegant" style="font-size: 0.9rem;">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" class="btn-submit-elegant">
                        <span>Acceder Ahora</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </button>
                </form>

                <div class="auth-links">
                    <span>¿No tienes una cuenta?</span>
                    <a href="<?= ENV_APP['BASE_URL'] ?>/auth/registro?tipo=<?= htmlspecialchars($tipo) ?>" class="link-elegant" style="font-weight: 700;">
                        Crear Cuenta Nueva
                    </a>
                </div>

                <div style="margin-top: 30px; text-align: center;">
                    <a href="<?= ENV_APP['BASE_URL'] ?>/auth" class="link-elegant">
                        ← Cambiar tipo de usuario
                    </a>
                </div>
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
<?php
// app/Views/auth/login-unificado.php
$tipo = $tipo ?? 'persona';
$error = $error ?? '';
$email = $email ?? '';

$titles = [
    'persona' => '👤 Iniciar Sesión - Persona',
    'empresa' => '🏢 Iniciar Sesión - Empresa',
    'consultora' => '👨‍💼 Iniciar Sesión - Consultora'
];

$descriptions = [
    'persona' => 'Accede a tu cuenta para postularte a vacantes',
    'empresa' => 'Accede al panel de gestión de tu empresa',
    'consultora' => 'Panel de administración de Consultores Chiriquí'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titles[$tipo] ?? 'Iniciar Sesión' ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            animation: float 15s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .login-container {
            max-width: 450px;
            width: 100%;
            position: relative;
            z-index: 1;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            padding: 3rem 2.5rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .login-icon {
            font-size: 4em;
            margin-bottom: 1rem;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .login-title {
            font-size: 2rem;
            font-weight: 800;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            color: #666;
            font-size: 0.95rem;
        }

        .alert {
            background: rgba(239, 68, 68, 0.1);
            border-left: 4px solid #ef4444;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            color: #dc2626;
            font-size: 0.9rem;
            animation: shake 0.5s ease-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .form-input-wrapper {
            position: relative;
        }

        .form-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            color: #999;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
            color: #333;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-input::placeholder {
            color: #999;
        }

        .btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 2rem 0;
            color: #999;
            font-size: 0.85rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .login-footer {
            text-align: center;
            margin-top: 2rem;
            color: #666;
            font-size: 0.9rem;
        }

        .login-footer a {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .login-footer a:hover {
            color: #764ba2;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 2rem 1.5rem;
            }

            .login-title {
                font-size: 1.75rem;
            }
        }

        .loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .loading::after {
            content: '';
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">
                    <?php
                    $icons = ['persona' => '👤', 'empresa' => '🏢', 'consultora' => '👨‍💼'];
                    echo $icons[$tipo] ?? '🔑';
                    ?>
                </div>
                <h1 class="login-title"><?= $titles[$tipo] ?? 'Iniciar Sesión' ?></h1>
                <p class="login-subtitle"><?= $descriptions[$tipo] ?? 'Accede a tu cuenta' ?></p>
            </div>

            <?php if ($error): ?>
                <div class="alert">
                    ⚠️ <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= ENV_APP['BASE_URL'] ?>/auth/login" id="loginForm">
                <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo) ?>">
                
                <div class="form-group">
                    <label class="form-label" for="email">Correo Electrónico</label>
                    <div class="form-input-wrapper">
                        <span class="form-icon">📧</span>
                        <input 
                            type="email" 
                            class="form-input" 
                            id="email" 
                            name="email" 
                            value="<?= htmlspecialchars($email) ?>"
                            placeholder="tu@email.com"
                            required
                            autocomplete="email"
                            autofocus
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Contraseña</label>
                    <div class="form-input-wrapper">
                        <span class="form-icon">🔒</span>
                        <input 
                            type="password" 
                            class="form-input" 
                            id="password" 
                            name="password" 
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    🔓 Iniciar Sesión
                </button>
            </form>

            <div class="divider">o</div>

            <div class="login-footer">
                <p>
                    ¿No tienes una cuenta? 
                    <a href="<?= ENV_APP['BASE_URL'] ?>/auth/registro?tipo=<?= htmlspecialchars($tipo) ?>">
                        Regístrate aquí
                    </a>
                </p>
                <p style="margin-top: 1rem;">
                    <a href="<?= ENV_APP['BASE_URL'] ?>/auth">← Volver a opciones de acceso</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('loginForm');
        const submitButton = form.querySelector('.btn-primary');

        form.addEventListener('submit', function() {
            submitButton.classList.add('loading');
            submitButton.textContent = 'Iniciando sesión...';
        });
    </script>
</body>
</html>
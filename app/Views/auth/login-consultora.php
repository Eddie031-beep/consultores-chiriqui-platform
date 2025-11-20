<?php
$error = $error ?? '';
$email = $email ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Consultora</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            max-width: 450px;
            width: 100%;
            background: #0f172a;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            border: 1px solid #1e293b;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5em;
        }

        .login-header h1 {
            color: #e5e7eb;
            font-size: 2em;
            margin-bottom: 10px;
        }

        .login-header p {
            color: #94a3b8;
            font-size: 0.95em;
        }

        .alert {
            background: rgba(239, 68, 68, 0.1);
            border-left: 4px solid #ef4444;
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            color: #fca5a5;
            font-size: 0.9em;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #e5e7eb;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #334155;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s;
            background: #1e293b;
            color: #e5e7eb;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            background: #0f172a;
        }

        .form-group input::placeholder {
            color: #64748b;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .links {
            text-align: center;
            margin-top: 25px;
        }

        .links a {
            color: #818cf8;
            text-decoration: none;
            font-weight: 500;
        }

        .links a:hover {
            color: #a5b4fc;
            text-decoration: underline;
        }

        .security-notice {
            background: rgba(59, 130, 246, 0.1);
            border-left: 4px solid #3b82f6;
            padding: 12px 15px;
            margin-top: 20px;
            border-radius: 5px;
            color: #60a5fa;
            font-size: 0.85em;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="login-icon">👨‍💼</div>
            <h1>Consultora</h1>
            <p>Panel de administración</p>
        </div>

        <?php if ($error): ?>
            <div class="alert">
                ⚠️ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= ENV_APP['BASE_URL'] ?>/auth/login-consultora">
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?= htmlspecialchars($email) ?>"
                    placeholder="admin@consultora.com"
                    required
                    autocomplete="email"
                >
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                >
            </div>

            <button type="submit" class="btn-submit">
                🔓 Iniciar Sesión
            </button>
        </form>

        <div class="security-notice">
            🔒 Acceso restringido solo para personal autorizado de la consultora
        </div>

        <div class="links">
            <p>
                <a href="<?= ENV_APP['BASE_URL'] ?>/">← Volver al inicio</a>
            </p>
        </div>
    </div>
</body>
</html>
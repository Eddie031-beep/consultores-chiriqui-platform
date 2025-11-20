<?php
session_start();
require_once '../config/database.php';

// Redirigir si ya está autenticado
if(isset($_SESSION['user_id'])) {
    header('Location: ../panel-candidato/dashboard.php');
    exit;
}

$error = '';
$email = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if(empty($email) || empty($password)) {
        $error = 'Por favor completa todos los campos';
    } else {
        try {
            $pdo = db_connect();
            $stmt = $pdo->prepare('SELECT * FROM solicitantes WHERE email = ? AND estado = ?');
            $stmt->execute([$email, 'activo']);
            $solicitante = $stmt->fetch();

            if($solicitante && password_verify($password, $solicitante['password_hash'])) {
                // Login exitoso
                $_SESSION['user_id'] = $solicitante['id'];
                $_SESSION['user_type'] = 'candidato';
                $_SESSION['user_name'] = $solicitante['nombre'] . ' ' . $solicitante['apellido'];
                $_SESSION['user_email'] = $solicitante['email'];

                // Actualizar último login
                $stmt_update = $pdo->prepare('UPDATE solicitantes SET ultimo_login = NOW() WHERE id = ?');
                $stmt_update->execute([$solicitante['id']]);

                // Redirigir a vacante si viene desde postulación
                if(isset($_GET['vacante_id']) && !empty($_GET['vacante_id'])) {
                    header('Location: ../panel-candidato/postular.php?vacante_id=' . intval($_GET['vacante_id']));
                } else {
                    header('Location: ../panel-candidato/dashboard.php');
                }
                exit;
            } else {
                $error = 'Email o contraseña incorrectos';
            }
        } catch(PDOException $e) {
            $error = 'Error en la base de datos: ' . $e->getMessage();
        }
    }
}

$vacante_id = $_GET['vacante_id'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Candidato | Consultores Chiriquí</title>
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
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 450px;
        }

        .form-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-header h1 {
            font-size: 1.8em;
            color: #333;
            margin-bottom: 10px;
        }

        .form-header p {
            color: #666;
            font-size: 0.95em;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 0.95em;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 0.95em;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.95em;
        }

        .alert-error {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
        }

        .alert-info {
            background: #eef;
            border: 1px solid #ccf;
            color: #33c;
        }

        .btn-login {
            width: 100%;
            background: #667eea;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-login:hover {
            background: #5568d3;
        }

        .form-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #eee;
        }

        .form-footer p {
            color: #666;
            font-size: 0.95em;
            margin-bottom: 10px;
        }

        .form-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .form-footer a:hover {
            color: #5568d3;
        }

        .back-link {
            display: block;
            margin-bottom: 20px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: opacity 0.3s;
        }

        .back-link:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">← Volver</a>

        <div class="form-card">
            <div class="form-header">
                <h1>👤 Iniciar Sesión</h1>
                <p>Como Persona Postulante</p>
            </div>

            <?php if(!empty($error)): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if(!empty($vacante_id)): ?>
                <div class="alert alert-info">
                    ℹ️ Inicia sesión para postularte a esta vacante
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?php echo htmlspecialchars($email); ?>" 
                        required
                        placeholder="tu@email.com"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        placeholder="••••••••"
                    >
                </div>

                <button type="submit" class="btn-login">Iniciar Sesión</button>
            </form>

            <div class="form-footer">
                <p>¿No tienes cuenta?</p>
                <a href="registro-candidato.php<?php echo !empty($vacante_id) ? '?vacante_id=' . intval($vacante_id) : ''; ?>">
                    Crear cuenta de candidato
                </a>
            </div>
        </div>
    </div>
</body>
</html>
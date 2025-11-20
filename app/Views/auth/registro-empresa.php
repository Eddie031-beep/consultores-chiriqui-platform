<?php
session_start();
require_once '../config/database.php';

// Redirigir si ya está autenticado
if(isset($_SESSION['user_id'])) {
    header('Location: ../panel-empresa/dashboard.php');
    exit;
}

$error = '';
$form_data = [
    'nombre_empresa' => '',
    'ruc' => '',
    'dv' => '',
    'direccion' => '',
    'provincia' => '',
    'telefono' => '',
    'email_contacto' => '',
    'sitio_web' => '',
    'sector' => '',
    'nombre_usuario' => '',
    'apellido_usuario' => '',
    'email_usuario' => ''
];

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_data = [
        'nombre_empresa' => trim($_POST['nombre_empresa'] ?? ''),
        'ruc' => trim($_POST['ruc'] ?? ''),
        'dv' => trim($_POST['dv'] ?? ''),
        'direccion' => trim($_POST['direccion'] ?? ''),
        'provincia' => trim($_POST['provincia'] ?? ''),
        'telefono' => trim($_POST['telefono'] ?? ''),
        'email_contacto' => trim($_POST['email_contacto'] ?? ''),
        'sitio_web' => trim($_POST['sitio_web'] ?? ''),
        'sector' => trim($_POST['sector'] ?? ''),
        'nombre_usuario' => trim($_POST['nombre_usuario'] ?? ''),
        'apellido_usuario' => trim($_POST['apellido_usuario'] ?? ''),
        'email_usuario' => trim($_POST['email_usuario'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'password_confirm' => $_POST['password_confirm'] ?? '',
        'tipo' => $_POST['tipo'] ?? 'privada'
    ];

    // Validaciones
    if(empty($form_data['nombre_empresa'])) $error = 'El nombre de la empresa es requerido';
    elseif(empty($form_data['ruc'])) $error = 'El RUC es requerido';
    elseif(empty($form_data['direccion'])) $error = 'La dirección es requerida';
    elseif(empty($form_data['provincia'])) $error = 'La provincia es requerida';
    elseif(empty($form_data['nombre_usuario'])) $error = 'El nombre del usuario es requerido';
    elseif(empty($form_data['apellido_usuario'])) $error = 'El apellido del usuario es requerido';
    elseif(empty($form_data['email_usuario'])) $error = 'El email es requerido';
    elseif(!filter_var($form_data['email_usuario'], FILTER_VALIDATE_EMAIL)) $error = 'Email inválido';
    elseif(empty($form_data['password'])) $error = 'La contraseña es requerida';
    elseif(strlen($form_data['password']) < 6) $error = 'La contraseña debe tener mínimo 6 caracteres';
    elseif($form_data['password'] !== $form_data['password_confirm']) $error = 'Las contraseñas no coinciden';
    else {
        try {
            $pdo = db_connect();

            // Verificar si RUC ya existe
            $stmt_check_ruc = $pdo->prepare('SELECT id FROM empresas WHERE ruc = ?');
            $stmt_check_ruc->execute([$form_data['ruc']]);
            if($stmt_check_ruc->fetch()) {
                $error = 'Este RUC ya está registrado';
            } else {
                // Verificar si email ya existe
                $stmt_check_email = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
                $stmt_check_email->execute([$form_data['email_usuario']]);
                if($stmt_check_email->fetch()) {
                    $error = 'Este email ya está registrado';
                } else {
                    // Iniciar transacción
                    $pdo->beginTransaction();

                    try {
                        // Insertar empresa
                        $stmt_empresa = $pdo->prepare('
                            INSERT INTO empresas (tipo, nombre, ruc, dv, direccion, provincia, telefono, email_contacto, sitio_web, sector, estado)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                        ');
                        
                        $stmt_empresa->execute([
                            $form_data['tipo'],
                            $form_data['nombre_empresa'],
                            $form_data['ruc'],
                            $form_data['dv'],
                            $form_data['direccion'],
                            $form_data['provincia'],
                            $form_data['telefono'] ?: null,
                            $form_data['email_contacto'] ?: null,
                            $form_data['sitio_web'] ?: null,
                            $form_data['sector'] ?: null,
                            'activa'
                        ]);

                        $empresa_id = $pdo->lastInsertId();

                        // Insertar usuario admin de la empresa
                        $password_hash = password_hash($form_data['password'], PASSWORD_BCRYPT);
                        $stmt_usuario = $pdo->prepare('
                            INSERT INTO usuarios (empresa_id, nombre, apellido, email, password_hash, rol_id, estado)
                            VALUES (?, ?, ?, ?, ?, 2, ?)
                        ');

                        $stmt_usuario->execute([
                            $empresa_id,
                            $form_data['nombre_usuario'],
                            $form_data['apellido_usuario'],
                            $form_data['email_usuario'],
                            $password_hash,
                            'activo'
                        ]);

                        $usuario_id = $pdo->lastInsertId();

                        // Confirmar transacción
                        $pdo->commit();

                        // Login automático
                        $_SESSION['user_id'] = $usuario_id;
                        $_SESSION['user_type'] = 'empresa';
                        $_SESSION['empresa_id'] = $empresa_id;
                        $_SESSION['user_name'] = $form_data['nombre_usuario'] . ' ' . $form_data['apellido_usuario'];
                        $_SESSION['user_email'] = $form_data['email_usuario'];
                        $_SESSION['empresa_nombre'] = $form_data['nombre_empresa'];

                        header('Location: ../panel-empresa/dashboard.php');
                        exit;

                    } catch(PDOException $e) {
                        $pdo->rollBack();
                        $error = 'Error al registrar la empresa: ' . $e->getMessage();
                    }
                }
            }
        } catch(PDOException $e) {
            $error = 'Error en la base de datos: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Empresa | Consultores Chiriquí</title>
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
            max-width: 700px;
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

        .form-section {
            margin-bottom: 25px;
            padding-bottom: 25px;
            border-bottom: 1px solid #eee;
        }

        .form-section:last-of-type {
            border-bottom: none;
        }

        .form-section-titulo {
            font-size: 1.1em;
            font-weight: 600;
            color: #667eea;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 0.95em;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 0.95em;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
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

        .btn-registro {
            width: 100%;
            background: #4caf50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-registro:hover {
            background: #45a049;
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

        .required {
            color: #c33;
        }

        @media (max-width: 700px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .form-card {
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">← Volver</a>

        <div class="form-card">
            <div class="form-header">
                <h1>🏢 Registrar Empresa</h1>
                <p>Publica tus vacantes y gestiona candidatos</p>
            </div>

            <?php if(!empty($error)): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <!-- Datos de la Empresa -->
                <div class="form-section">
                    <h3 class="form-section-titulo">📋 Información de la Empresa</h3>

                    <div class="form-group">
                        <label for="nombre_empresa">Nombre de la Empresa <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="nombre_empresa" 
                            name="nombre_empresa" 
                            value="<?php echo htmlspecialchars($form_data['nombre_empresa']); ?>" 
                            required
                        >
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="ruc">RUC <span class="required">*</span></label>
                            <input 
                                type="text" 
                                id="ruc" 
                                name="ruc" 
                                value="<?php echo htmlspecialchars($form_data['ruc']); ?>" 
                                placeholder="Ej: 155555555-1"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="dv">DV</label>
                            <input 
                                type="text" 
                                id="dv" 
                                name="dv" 
                                value="<?php echo htmlspecialchars($form_data['dv']); ?>"
                                maxlength="5"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="direccion">Dirección <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="direccion" 
                            name="direccion" 
                            value="<?php echo htmlspecialchars($form_data['direccion']); ?>" 
                            required
                        >
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="provincia">Provincia <span class="required">*</span></label>
                            <select id="provincia" name="provincia" required>
                                <option value="">Selecciona una provincia</option>
                                <option value="Panamá" <?php echo $form_data['provincia'] === 'Panamá' ? 'selected' : ''; ?>>Panamá</option>
                                <option value="Chiriquí" <?php echo $form_data['provincia'] === 'Chiriquí' ? 'selected' : ''; ?>>Chiriquí</option>
                                <option value="Bocas del Toro" <?php echo $form_data['provincia'] === 'Bocas del Toro' ? 'selected' : ''; ?>>Bocas del Toro</option>
                                <option value="Colón" <?php echo $form_data['provincia'] === 'Colón' ? 'selected' : ''; ?>>Colón</option>
                                <option value="Coclé" <?php echo $form_data['provincia'] === 'Coclé' ? 'selected' : ''; ?>>Coclé</option>
                                <option value="Los Santos" <?php echo $form_data['provincia'] === 'Los Santos' ? 'selected' : ''; ?>>Los Santos</option>
                                <option value="Herrera" <?php echo $form_data['provincia'] === 'Herrera' ? 'selected' : ''; ?>>Herrera</option>
                                <option value="Panamá Oeste" <?php echo $form_data['provincia'] === 'Panamá Oeste' ? 'selected' : ''; ?>>Panamá Oeste</option>
                                <option value="Darién" <?php echo $form_data['provincia'] === 'Darién' ? 'selected' : ''; ?>>Darién</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="sector">Sector</label>
                            <input 
                                type="text" 
                                id="sector" 
                                name="sector" 
                                value="<?php echo htmlspecialchars($form_data['sector']); ?>"
                                placeholder="Ej: Tecnología, Salud..."
                            >
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input 
                                type="tel" 
                                id="telefono" 
                                name="telefono" 
                                value="<?php echo htmlspecialchars($form_data['telefono']); ?>"
                            >
                        </div>

                        <div class="form-group">
                            <label for="email_contacto">Email de Contacto</label>
                            <input 
                                type="email" 
                                id="email_contacto" 
                                name="email_contacto" 
                                value="<?php echo htmlspecialchars($form_data['email_contacto']); ?>"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="sitio_web">Sitio Web</label>
                        <input 
                            type="url" 
                            id="sitio_web" 
                            name="sitio_web" 
                            value="<?php echo htmlspecialchars($form_data['sitio_web']); ?>"
                            placeholder="https://ejemplo.com"
                        >
                    </div>
                </div>

                <!-- Datos del Usuario Admin -->
                <div class="form-section">
                    <h3 class="form-section-titulo">👤 Datos del Administrador</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nombre_usuario">Nombre <span class="required">*</span></label>
                            <input 
                                type="text" 
                                id="nombre_usuario" 
                                name="nombre_usuario" 
                                value="<?php echo htmlspecialchars($form_data['nombre_usuario']); ?>" 
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="apellido_usuario">Apellido <span class="required">*</span></label>
                            <input 
                                type="text" 
                                id="apellido_usuario" 
                                name="apellido_usuario" 
                                value="<?php echo htmlspecialchars($form_data['apellido_usuario']); ?>" 
                                required
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email_usuario">Email <span class="required">*</span></label>
                        <input 
                            type="email" 
                            id="email_usuario" 
                            name="email_usuario" 
                            value="<?php echo htmlspecialchars($form_data['email_usuario']); ?>" 
                            required
                        >
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Contraseña <span class="required">*</span
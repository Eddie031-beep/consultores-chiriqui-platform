<?php
// app/Views/auth/registro-unificado.php
$tipo = $tipo ?? 'persona';
$error = $error ?? '';
$form_data = $form_data ?? [];

$titles = [
    'persona' => '👤 Registro - Persona',
    'empresa' => '🏢 Registro - Empresa'
];

$descriptions = [
    'persona' => 'Crea tu cuenta para postularte a vacantes',
    'empresa' => 'Registra tu empresa y comienza a publicar vacantes'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titles[$tipo] ?? 'Registro' ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            animation: float 15s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        .registro-container {
            max-width: 600px;
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

        .registro-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            padding: 3rem 2.5rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .registro-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .registro-icon {
            font-size: 4em;
            margin-bottom: 1rem;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .registro-title {
            font-size: 2rem;
            font-weight: 800;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .registro-subtitle {
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
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .form-label .required {
            color: #ef4444;
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

        .form-input,
        .form-select {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
            color: #333;
            font-family: inherit;
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: #f5576c;
            box-shadow: 0 0 0 3px rgba(245, 87, 108, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
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
            margin-top: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            box-shadow: 0 10px 30px rgba(245, 87, 108, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(245, 87, 108, 0.4);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.5rem 0;
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

        .registro-footer {
            text-align: center;
            margin-top: 2rem;
            color: #666;
            font-size: 0.9rem;
        }

        .registro-footer a {
            color: #f5576c;
            font-weight: 600;
            text-decoration: none;
        }

        .registro-footer a:hover {
            color: #f093fb;
        }

        @media (max-width: 640px) {
            .registro-card {
                padding: 2rem 1.5rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .registro-title {
                font-size: 1.75rem;
            }
        }

        .loading {
            pointer-events: none;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="registro-container">
        <div class="registro-card">
            <div class="registro-header">
                <div class="registro-icon">
                    <?= $tipo === 'persona' ? '👤' : '🏢' ?>
                </div>
                <h1 class="registro-title"><?= $titles[$tipo] ?? 'Registro' ?></h1>
                <p class="registro-subtitle"><?= $descriptions[$tipo] ?? 'Crea tu cuenta' ?></p>
            </div>

            <?php if ($error): ?>
                <div class="alert">
                    ⚠️ <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= ENV_APP['BASE_URL'] ?>/auth/registro" id="registroForm">
                <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo) ?>">
                
                <?php if ($tipo === 'persona'): ?>
                    <!-- FORMULARIO PERSONA -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nombre <span class="required">*</span></label>
                            <div class="form-input-wrapper">
                                <span class="form-icon">👤</span>
                                <input type="text" class="form-input" name="nombre" 
                                       value="<?= htmlspecialchars($form_data['nombre'] ?? '') ?>"
                                       placeholder="Juan" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Apellido <span class="required">*</span></label>
                            <div class="form-input-wrapper">
                                <span class="form-icon">👤</span>
                                <input type="text" class="form-input" name="apellido" 
                                       value="<?= htmlspecialchars($form_data['apellido'] ?? '') ?>"
                                       placeholder="Pérez" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <div class="form-input-wrapper">
                            <span class="form-icon">📧</span>
                            <input type="email" class="form-input" name="email" 
                                   value="<?= htmlspecialchars($form_data['email'] ?? '') ?>"
                                   placeholder="tu@email.com" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Teléfono</label>
                            <div class="form-input-wrapper">
                                <span class="form-icon">📱</span>
                                <input type="tel" class="form-input" name="telefono" 
                                       value="<?= htmlspecialchars($form_data['telefono'] ?? '') ?>"
                                       placeholder="+507 6000-0000">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Cédula</label>
                            <div class="form-input-wrapper">
                                <span class="form-icon">🪪</span>
                                <input type="text" class="form-input" name="cedula" 
                                       value="<?= htmlspecialchars($form_data['cedula'] ?? '') ?>"
                                       placeholder="8-123-456">
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- FORMULARIO EMPRESA -->
                    <div class="form-group">
                        <label class="form-label">Nombre de la Empresa <span class="required">*</span></label>
                        <div class="form-input-wrapper">
                            <span class="form-icon">🏢</span>
                            <input type="text" class="form-input" name="nombre_empresa" 
                                   value="<?= htmlspecialchars($form_data['nombre_empresa'] ?? '') ?>"
                                   placeholder="Mi Empresa S.A." required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">RUC <span class="required">*</span></label>
                            <div class="form-input-wrapper">
                                <span class="form-icon">📋</span>
                                <input type="text" class="form-input" name="ruc" 
                                       value="<?= htmlspecialchars($form_data['ruc'] ?? '') ?>"
                                       placeholder="123456-1-123456" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">DV <span class="required">*</span></label>
                            <div class="form-input-wrapper">
                                <span class="form-icon">🔢</span>
                                <input type="text" class="form-input" name="dv" 
                                       value="<?= htmlspecialchars($form_data['dv'] ?? '') ?>"
                                       placeholder="12" required maxlength="5">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Dirección <span class="required">*</span></label>
                        <div class="form-input-wrapper">
                            <span class="form-icon">📍</span>
                            <input type="text" class="form-input" name="direccion" 
                                   value="<?= htmlspecialchars($form_data['direccion'] ?? '') ?>"
                                   placeholder="Calle 50, Ciudad de Panamá" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Provincia <span class="required">*</span></label>
                            <div class="form-input-wrapper">
                                <span class="form-icon">🗺️</span>
                                <select class="form-select" name="provincia" required>
                                    <option value="">Selecciona...</option>
                                    <option value="Panamá" <?= ($form_data['provincia'] ?? '') === 'Panamá' ? 'selected' : '' ?>>Panamá</option>
                                    <option value="Chiriquí" <?= ($form_data['provincia'] ?? '') === 'Chiriquí' ? 'selected' : '' ?>>Chiriquí</option>
                                    <option value="Colón" <?= ($form_data['provincia'] ?? '') === 'Colón' ? 'selected' : '' ?>>Colón</option>
                                    <option value="Coclé" <?= ($form_data['provincia'] ?? '') === 'Coclé' ? 'selected' : '' ?>>Coclé</option>
                                    <option value="Herrera" <?= ($form_data['provincia'] ?? '') === 'Herrera' ? 'selected' : '' ?>>Herrera</option>
                                    <option value="Los Santos" <?= ($form_data['provincia'] ?? '') === 'Los Santos' ? 'selected' : '' ?>>Los Santos</option>
                                    <option value="Veraguas" <?= ($form_data['provincia'] ?? '') === 'Veraguas' ? 'selected' : '' ?>>Veraguas</option>
                                    <option value="Bocas del Toro" <?= ($form_data['provincia'] ?? '') === 'Bocas del Toro' ? 'selected' : '' ?>>Bocas del Toro</option>
                                    <option value="Darién" <?= ($form_data['provincia'] ?? '') === 'Darién' ? 'selected' : '' ?>>Darién</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tipo</label>
                            <div class="form-input-wrapper">
                                <span class="form-icon">🏛️</span>
                                <select class="form-select" name="tipo_empresa">
                                    <option value="privada" <?= ($form_data['tipo'] ?? 'privada') === 'privada' ? 'selected' : '' ?>>Privada</option>
                                    <option value="publica" <?= ($form_data['tipo'] ?? '') === 'publica' ? 'selected' : '' ?>>Pública</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Teléfono</label>
                            <div class="form-input-wrapper">
                                <span class="form-icon">📱</span>
                                <input type="tel" class="form-input" name="telefono" 
                                       value="<?= htmlspecialchars($form_data['telefono'] ?? '') ?>"
                                       placeholder="+507 000-0000">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Sector</label>
                            <div class="form-input-wrapper">
                                <span class="form-icon">🏭</span>
                                <input type="text" class="form-input" name="sector" 
                                       value="<?= htmlspecialchars($form_data['sector'] ?? '') ?>"
                                       placeholder="Tecnología">
                            </div>
                        </div>
                    </div>

                    <div class="divider">Datos del Administrador</div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nombre <span class="required">*</span></label>
                            <div class="form-input-wrapper">
                                <span class="form-icon">👤</span>
                                <input type="text" class="form-input" name="nombre_usuario" 
                                       value="<?= htmlspecialchars($form_data['nombre_usuario'] ?? '') ?>"
                                       placeholder="Juan" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Apellido <span class="required">*</span></label>
                            <div class="form-input-wrapper">
                                <span class="form-icon">👤</span>
                                <input type="text" class="form-input" name="apellido_usuario" 
                                       value="<?= htmlspecialchars($form_data['apellido_usuario'] ?? '') ?>"
                                       placeholder="Pérez" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email del Administrador <span class="required">*</span></label>
                        <div class="form-input-wrapper">
                            <span class="form-icon">📧</span>
                            <input type="email" class="form-input" name="email_usuario" 
                                   value="<?= htmlspecialchars($form_data['email_usuario'] ?? '') ?>"
                                   placeholder="admin@empresa.com" required>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- CONTRASEÑAS (común para ambos) -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Contraseña <span class="required">*</span></label>
                        <div class="form-input-wrapper">
                            <span class="form-icon">🔒</span>
                            <input type="password" class="form-input" name="password" 
                                   placeholder="••••••••" required minlength="6">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirmar <span class="required">*</span></label>
                        <div class="form-input-wrapper">
                            <span class="form-icon">🔒</span>
                            <input type="password" class="form-input" name="password_confirm" 
                                   placeholder="••••••••" required minlength="6">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    ✨ Crear Cuenta
                </button>
            </form>

            <div class="registro-footer">
                <p>
                    ¿Ya tienes una cuenta? 
                    <a href="<?= ENV_APP['BASE_URL'] ?>/auth/login?tipo=<?= htmlspecialchars($tipo) ?>">
                        Inicia sesión aquí
                    </a>
                </p>
                <p style="margin-top: 1rem;">
                    <a href="<?= ENV_APP['BASE_URL'] ?>/auth">← Volver a opciones de acceso</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('registroForm');
        const submitButton = form.querySelector('.btn-primary');

        form.addEventListener('submit', function(e) {
            const password = form.querySelector('[name="password"]').value;
            const passwordConfirm = form.querySelector('[name="password_confirm"]').value;

            if (password !== passwordConfirm) {
                e.preventDefault();
                alert('❌ Las contraseñas no coinciden');
                return;
            }

            submitButton.classList.add('loading');
            submitButton.textContent = 'Creando cuenta...';
        });
    </script>
</body>
</html>
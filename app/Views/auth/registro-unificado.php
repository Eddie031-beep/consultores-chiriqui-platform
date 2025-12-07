<?php
// app/Views/auth/registro-unificado.php
$tipo = $tipo ?? 'persona';
$error = $error ?? '';
$form_data = $form_data ?? [];

$titles = [
    'persona' => 'Únete como talento',
    'candidato' => 'Únete como talento',
    'empresa' => 'Empieza a contratar'
];

$subtitles = [
    'persona' => 'Crea tu perfil profesional y conecta con las mejores empresas.',
    'candidato' => 'Crea tu perfil profesional y conecta con las mejores empresas.',
    'empresa' => 'Registra tu organización y publica tus primeras vacantes hoy mismo.'
];
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titles[$tipo] ?? 'Registro' ?></title>
    <!-- GLOBAL CSS -->
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <!-- ELEGANT AUTH CSS -->
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/auth-elegant.css">
</head>
<body>
    <div class="auth-page-wrapper">
        
        <!-- LEFT SIDE: VISUAL -->
        <div class="auth-visual-side">
            <div class="auth-shape shape-a"></div>
            <div class="auth-shape shape-b"></div>
            
            <div class="auth-visual-content">
                <div style="margin-bottom: 30px; color: white;">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="20" y1="8" x2="20" y2="14"></line>
                        <line x1="23" y1="11" x2="17" y2="11"></line>
                    </svg>
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
            <div class="auth-card-modern" style="max-width: 600px;"> <!-- Wider for registration -->
                <div class="auth-header">
                    <h2>Crear Cuenta Nueva</h2>
                    <p>Completa el formulario para registrarte como <strong><?= ucfirst($tipo) ?></strong></p>
                </div>

                <?php if ($error): ?>
                    <div style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 15px; border-radius: 12px; margin-bottom: 25px; border-left: 4px solid #ef4444; display: flex; align-items: center; gap: 10px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= ENV_APP['BASE_URL'] ?>/auth/registro" id="registroForm">
                    <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo) ?>">
                    
                    <?php if ($tipo === 'persona' || $tipo === 'candidato'): ?>
                        <!-- FORMULARIO PERSONA -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="floating-group">
                                <input type="text" name="nombre" class="input-elegant" placeholder=" " value="<?= htmlspecialchars($form_data['nombre'] ?? '') ?>" required>
                                <label class="label-elegant">Nombre</label>
                            </div>
                            <div class="floating-group">
                                <input type="text" name="apellido" class="input-elegant" placeholder=" " value="<?= htmlspecialchars($form_data['apellido'] ?? '') ?>" required>
                                <label class="label-elegant">Apellido</label>
                            </div>
                        </div>

                        <div class="floating-group">
                            <input type="email" name="email" class="input-elegant" placeholder=" " value="<?= htmlspecialchars($form_data['email'] ?? '') ?>" required>
                            <label class="label-elegant">Correo Electrónico</label>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="floating-group">
                                <input type="tel" name="telefono" class="input-elegant" placeholder=" " value="<?= htmlspecialchars($form_data['telefono'] ?? '') ?>">
                                <label class="label-elegant">Teléfono</label>
                            </div>
                            <div class="floating-group">
                                <input type="text" name="cedula" class="input-elegant" placeholder=" " value="<?= htmlspecialchars($form_data['cedula'] ?? '') ?>">
                                <label class="label-elegant">Cédula</label>
                            </div>
                        </div>

                        <!-- NEW FIELDS ADDED -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="floating-group">
                                <input type="date" name="fecha_nacimiento" class="input-elegant" placeholder=" " value="<?= htmlspecialchars($form_data['fecha_nacimiento'] ?? '') ?>">
                                <label class="label-elegant">Fecha de Nacimiento</label>
                            </div>
                            <div class="floating-group">
                                <select name="nacionalidad" class="input-elegant" style="background: white;">
                                    <option value="" disabled selected>Seleccione...</option>
                                    <option value="Panamá" <?= ($form_data['nacionalidad']??'') == 'Panamá' ? 'selected' : '' ?>>Panamá</option>
                                    <option value="Extranjero" <?= ($form_data['nacionalidad']??'') == 'Extranjero' ? 'selected' : '' ?>>Extranjero</option>
                                </select>
                                <label class="label-elegant">Nacionalidad</label>
                            </div>
                        </div>

                    <?php elseif ($tipo === 'empresa'): ?>
                        <!-- FORMULARIO EMPRESA -->
                        <div class="floating-group">
                            <input type="text" name="nombre_empresa" class="input-elegant" placeholder=" " value="<?= htmlspecialchars($form_data['nombre_empresa'] ?? '') ?>" required>
                            <label class="label-elegant">Nombre de la Empresa</label>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="floating-group">
                                <input type="text" name="ruc" class="input-elegant" placeholder=" " value="<?= htmlspecialchars($form_data['ruc'] ?? '') ?>" required>
                                <label class="label-elegant">RUC</label>
                            </div>
                            <div class="floating-group">
                                <input type="text" name="dv" class="input-elegant" placeholder=" " value="<?= htmlspecialchars($form_data['dv'] ?? '') ?>" required maxlength="5">
                                <label class="label-elegant">DV</label>
                            </div>
                        </div>

                        <div class="floating-group">
                            <input type="text" name="direccion" class="input-elegant" placeholder=" " value="<?= htmlspecialchars($form_data['direccion'] ?? '') ?>" required>
                            <label class="label-elegant">Dirección</label>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                             <div class="floating-group">
                                <input type="text" name="provincia" class="input-elegant" placeholder=" " value="<?= htmlspecialchars($form_data['provincia'] ?? '') ?>" required>
                                <label class="label-elegant">Provincia</label>
                            </div>
                            <div class="floating-group">
                                <input type="text" name="tipo_empresa" class="input-elegant" placeholder=" " value="<?= htmlspecialchars($form_data['tipo'] ?? '') ?>">
                                <label class="label-elegant">Tipo (Privada/Pública)</label>
                            </div>
                        </div>

                        <div style="margin: 30px 0 20px; font-weight: 700; color: var(--auth-text-muted); font-size: 0.9rem; letter-spacing: 0.5px; text-transform: uppercase;">Datos del Administrador</div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="floating-group">
                                <input type="text" name="nombre_usuario" class="input-elegant" placeholder=" " value="<?= htmlspecialchars($form_data['nombre_usuario'] ?? '') ?>" required>
                                <label class="label-elegant">Nombre</label>
                            </div>
                            <div class="floating-group">
                                <input type="text" name="apellido_usuario" class="input-elegant" placeholder=" " value="<?= htmlspecialchars($form_data['apellido_usuario'] ?? '') ?>" required>
                                <label class="label-elegant">Apellido</label>
                            </div>
                        </div>
                        <div class="floating-group">
                            <input type="email" name="email_usuario" class="input-elegant" placeholder=" " value="<?= htmlspecialchars($form_data['email_usuario'] ?? '') ?>" required>
                            <label class="label-elegant">Correo Electrónico</label>
                        </div>


                    <?php endif; ?>

                    <!-- PASSWORD (COMMON) -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 10px;">
                        <div class="floating-group">
                            <input type="password" name="password" class="input-elegant" placeholder=" " required minlength="6">
                            <label class="label-elegant">Contraseña</label>
                        </div>
                        <div class="floating-group">
                            <input type="password" name="password_confirm" class="input-elegant" placeholder=" " required minlength="6">
                            <label class="label-elegant">Confirmar</label>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit-elegant">
                        <span>Registrarme</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </button>
                </form>

                <div class="auth-links">
                    <span>¿Ya tienes una cuenta?</span>
                    <a href="<?= ENV_APP['BASE_URL'] ?>/auth/login?tipo=<?= htmlspecialchars($tipo) ?>" class="link-elegant" style="font-weight: 700;">
                        Inicia Sesión
                    </a>
                </div>

                <div style="margin-top: 25px; text-align: center;">
                    <a href="<?= ENV_APP['BASE_URL'] ?>/auth" class="link-elegant">
                        ← Volver a opciones de acceso
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Theme Persist Script -->
    <script>
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);

        // Simple validation visualization
        const form = document.getElementById('registroForm');
        form.addEventListener('submit', (e) => {
            const p1 = form.querySelector('[name="password"]').value;
            const p2 = form.querySelector('[name="password_confirm"]').value;
            if(p1 !== p2) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
            }
        });
    </script>
</body>
</html>
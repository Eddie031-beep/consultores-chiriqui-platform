<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Consultores Chiriquí</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-color: #10b981;
            --error-color: #ef4444;
            --text-dark: #1f2937;
            --text-gray: #6b7280;
            --bg-light: #f9fafb;
            --border-color: #e5e7eb;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        [data-theme="dark"] {
            --text-dark: #f9fafb;
            --text-gray: #d1d5db;
            --bg-light: #111827;
            --border-color: #374151;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 50%, #667eea 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
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
                radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 60% 60%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            animation: float 15s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        .register-container {
            max-width: 550px;
            width: 100%;
            position: relative;
            z-index: 1;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            padding: 3rem 2.5rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: slideUp 0.6s ease-out;
        }

        [data-theme="dark"] .register-card {
            background: rgba(31, 41, 55, 0.95);
            border-color: rgba(75, 85, 99, 0.3);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .register-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .register-logo {
            width: 70px;
            height: 70px;
            background: var(--secondary-gradient);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 30px rgba(240, 147, 251, 0.3);
        }

        .register-logo svg {
            width: 40px;
            height: 40px;
            fill: white;
        }

        .register-title {
            font-size: 2rem;
            font-weight: 800;
            background: var(--secondary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .register-subtitle {
            color: var(--text-gray);
            font-size: 0.95rem;
        }

        /* Tabs */
        .register-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            background: var(--bg-light);
            padding: 0.5rem;
            border-radius: 12px;
        }

        .tab-btn {
            flex: 1;
            padding: 0.75rem;
            border: none;
            background: transparent;
            color: var(--text-gray);
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
        }

        .tab-btn:hover {
            background: rgba(240, 147, 251, 0.1);
            color: #f5576c;
        }

        .tab-btn.active {
            background: white;
            color: #f5576c;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        [data-theme="dark"] .tab-btn.active {
            background: #374151;
        }

        /* Form */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
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
            color: var(--text-gray);
            font-size: 1.2rem;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-size: 0.95rem;
            transition: var(--transition);
            background: white;
            color: var(--text-dark);
            font-family: 'Inter', sans-serif;
        }

        [data-theme="dark"] .form-input,
        [data-theme="dark"] .form-select {
            background: #1f2937;
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

        /* Password Strength */
        .password-strength {
            margin-top: 0.5rem;
            height: 4px;
            background: var(--border-color);
            border-radius: 2px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease, background-color 0.3s ease;
        }

        .password-strength-bar.weak { width: 33%; background: #ef4444; }
        .password-strength-bar.medium { width: 66%; background: #f59e0b; }
        .password-strength-bar.strong { width: 100%; background: #10b981; }

        /* Checkbox */
        .form-checkbox-label {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            cursor: pointer;
            color: var(--text-dark);
            font-size: 0.9rem;
        }

        .form-checkbox {
            width: 18px;
            height: 18px;
            margin-top: 2px;
            cursor: pointer;
            accent-color: #f5576c;
        }

        /* Button */
        .btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }

        .btn-primary {
            background: var(--secondary-gradient);
            color: white;
            box-shadow: 0 10px 30px rgba(245, 87, 108, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(245, 87, 108, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        /* Footer */
        .register-footer {
            text-align: center;
            margin-top: 2rem;
            color: var(--text-gray);
            font-size: 0.9rem;
        }

        .register-footer a {
            color: #f5576c;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
        }

        .register-footer a:hover {
            color: #667eea;
        }

        /* Alerts */
        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideDown 0.4s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        /* Responsive */
        @media (max-width: 640px) {
            .register-card {
                padding: 2rem 1.5rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .register-title {
                font-size: 1.75rem;
            }
        }

        /* Loading */
        .btn.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn.loading::after {
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
    <div class="register-container">
        <div class="register-card">
            <!-- Header -->
            <div class="register-header">
                <div class="register-logo">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="white" stroke-width="2"/>
                        <circle cx="8.5" cy="7" r="4" stroke="white" stroke-width="2"/>
                        <line x1="20" y1="8" x2="20" y2="14" stroke="white" stroke-width="2"/>
                        <line x1="23" y1="11" x2="17" y2="11" stroke="white" stroke-width="2"/>
                    </svg>
                </div>
                <h1 class="register-title">¡Únete a nosotros!</h1>
                <p class="register-subtitle">Crea tu cuenta en minutos</p>
            </div>

            <!-- Tabs -->
            <div class="register-tabs">
                <button class="tab-btn active" data-tab="candidato">
                    👤 Candidato
                </button>
                <button class="tab-btn" data-tab="empresa">
                    🏢 Empresa
                </button>
            </div>

            <!-- Mensajes de Error -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    ⚠️ <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Formulario -->
            <form id="registerForm" method="POST" action="<?= ENV_APP['BASE_URL'] ?>/auth/register">
                <input type="hidden" name="user_type" id="userType" value="candidato">
                
                <!-- Nombre completo o empresa -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="firstName" id="firstNameLabel">Nombre</label>
                        <div class="form-input-wrapper">
                            <span class="form-icon">👤</span>
                            <input 
                                type="text" 
                                class="form-input" 
                                id="firstName" 
                                name="first_name" 
                                placeholder="Juan"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-group" id="lastNameGroup">
                        <label class="form-label" for="lastName">Apellido</label>
                        <div class="form-input-wrapper">
                            <span class="form-icon">👤</span>
                            <input 
                                type="text" 
                                class="form-input" 
                                id="lastName" 
                                name="last_name" 
                                placeholder="Pérez"
                                required
                            >
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label class="form-label" for="email">Correo Electrónico</label>
                    <div class="form-input-wrapper">
                        <span class="form-icon">📧</span>
                        <input 
                            type="email" 
                            class="form-input" 
                            id="email" 
                            name="email" 
                            placeholder="tu@email.com"
                            required
                        >
                    </div>
                </div>

                <!-- Teléfono -->
                <div class="form-group">
                    <label class="form-label" for="phone">Teléfono</label>
                    <div class="form-input-wrapper">
                        <span class="form-icon">📱</span>
                        <input 
                            type="tel" 
                            class="form-input" 
                            id="phone" 
                            name="phone" 
                            placeholder="+507 6000-0000"
                            required
                        >
                    </div>
                </div>

                <!-- Campos específicos de empresa (ocultos por defecto) -->
                <div id="empresaFields" style="display: none;">
                    <div class="form-group">
                        <label class="form-label" for="sector">Sector</label>
                        <div class="form-input-wrapper">
                            <span class="form-icon">🏭</span>
                            <select class="form-select" id="sector" name="sector">
                                <option value="">Selecciona un sector</option>
                                <option value="tecnologia">Tecnología</option>
                                <option value="finanzas">Finanzas</option>
                                <option value="salud">Salud</option>
                                <option value="educacion">Educación</option>
                                <option value="retail">Retail</option>
                                <option value="construccion">Construcción</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Contraseña -->
                <div class="form-row">
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
                            >
                        </div>
                        <div class="password-strength">
                            <div class="password-strength-bar" id="strengthBar"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="confirmPassword">Confirmar</label>
                        <div class="form-input-wrapper">
                            <span class="form-icon">🔒</span>
                            <input 
                                type="password" 
                                class="form-input" 
                                id="confirmPassword" 
                                name="confirm_password" 
                                placeholder="••••••••"
                                required
                            >
                        </div>
                    </div>
                </div>

                <!-- Términos y Condiciones -->
                <div class="form-group">
                    <label class="form-checkbox-label">
                        <input type="checkbox" class="form-checkbox" name="terms" required>
                        <span>
                            Acepto los 
                            <a href="<?= ENV_APP['BASE_URL'] ?>/terminos" target="_blank" style="color: #f5576c; text-decoration: none;">
                                Términos y Condiciones
                            </a>
                            y la
                            <a href="<?= ENV_APP['BASE_URL'] ?>/privacidad" target="_blank" style="color: #f5576c; text-decoration: none;">
                                Política de Privacidad
                            </a>
                        </span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">
                    Crear Cuenta
                </button>
            </form>

            <!-- Footer -->
            <div class="register-footer">
                ¿Ya tienes una cuenta? 
                <a href="<?= ENV_APP['BASE_URL'] ?>/login">Inicia sesión aquí</a>
            </div>
        </div>
    </div>

    <script>
        // Tab System
        const tabButtons = document.querySelectorAll('.tab-btn');
        const userTypeInput = document.getElementById('userType');
        const empresaFields = document.getElementById('empresaFields');
        const lastNameGroup = document.getElementById('lastNameGroup');
        const firstNameLabel = document.getElementById('firstNameLabel');
        const firstName = document.getElementById('firstName');
        const lastName = document.getElementById('lastName');
        const sector = document.getElementById('sector');

        tabButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                tabButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const userType = this.dataset.tab;
                userTypeInput.value = userType;
                
                // Update form action dynamically
                const baseUrl = '<?= ENV_APP['BASE_URL'] ?>';
                registerForm.action = `${baseUrl}/auth/registro-${userType}`;
                
                if (userType === 'empresa') {
                    empresaFields.style.display = 'block';
                    lastNameGroup.style.display = 'none';
                    firstNameLabel.textContent = 'Nombre de la Empresa';
                    firstName.placeholder = 'Mi Empresa S.A.';
                    lastName.required = false;
                    sector.required = true;
                } else {
                    empresaFields.style.display = 'none';
                    lastNameGroup.style.display = 'block';
                    firstNameLabel.textContent = 'Nombre';
                    firstName.placeholder = 'Juan';
                    lastName.required = true;
                    sector.required = false;
                }
            });
        });
        
        // Set initial action
        const initialUserType = userTypeInput.value;
        const baseUrl = '<?= ENV_APP['BASE_URL'] ?>';
        registerForm.action = `${baseUrl}/auth/registro-${initialUserType}`;

        // Password Strength
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;

            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z\d]/.test(password)) strength++;

            strengthBar.className = 'password-strength-bar';
            if (strength <= 1) {
                strengthBar.classList.add('weak');
            } else if (strength <= 2) {
                strengthBar.classList.add('medium');
            } else {
                strengthBar.classList.add('strong');
            }
        });

        // Form Validation
        const registerForm = document.getElementById('registerForm');
        const confirmPassword = document.getElementById('confirmPassword');
        const submitButton = registerForm.querySelector('.btn-primary');

        registerForm.addEventListener('submit', function(e) {
            if (passwordInput.value !== confirmPassword.value) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                return;
            }

            submitButton.classList.add('loading');
            submitButton.textContent = 'Creando cuenta...';
        });

        // Apply saved theme
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.documentElement.setAttribute('data-theme', savedTheme);
        }
    </script>
</body>
</html>

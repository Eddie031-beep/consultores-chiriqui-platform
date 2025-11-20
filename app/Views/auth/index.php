<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Consultores Chiriquí</title>
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
            position: relative;
            overflow: hidden;
        }

        /* Animación de fondo */
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

        .container {
            max-width: 1000px;
            width: 100%;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 3rem;
            animation: fadeInDown 0.8s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header h1 {
            font-size: 2.8em;
            margin-bottom: 0.5rem;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .header p {
            font-size: 1.2em;
            opacity: 0.95;
        }

        /* Selector de tipo de usuario */
        .user-type-selector {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .user-type-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 3px solid transparent;
            position: relative;
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
            animation-fill-mode: both;
        }

        .user-type-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .user-type-card:nth-child(2) {
            animation-delay: 0.2s;
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

        .user-type-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .user-type-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            border-color: #667eea;
        }

        .user-type-card:hover::before {
            opacity: 0.1;
        }

        .user-type-icon {
            font-size: 4em;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .user-type-card:hover .user-type-icon {
            animation: bounceHover 0.6s ease-in-out;
        }

        @keyframes bounceHover {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2) rotate(10deg); }
        }

        .user-type-title {
            font-size: 1.8em;
            font-weight: 700;
            color: #333;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }

        .user-type-description {
            color: #666;
            font-size: 1em;
            line-height: 1.6;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        /* Dropdown de cascada */
        .dropdown-cascade {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 1;
        }

        .user-type-card:hover .dropdown-cascade,
        .dropdown-cascade.active {
            max-height: 200px;
        }

        .cascade-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            padding-top: 1rem;
            border-top: 2px solid #f0f0f0;
        }

        .cascade-button {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
            text-align: center;
            opacity: 0;
            transform: translateY(-20px);
            animation: cascadeFall 0.5s ease-out forwards;
        }

        .cascade-button:nth-child(1) {
            animation-delay: 0.1s;
        }

        .cascade-button:nth-child(2) {
            animation-delay: 0.2s;
        }

        @keyframes cascadeFall {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-login {
            background: #667eea;
            color: white;
        }

        .btn-login:hover {
            background: #5568d3;
            transform: translateX(5px);
        }

        .btn-register {
            background: #4caf50;
            color: white;
        }

        .btn-register:hover {
            background: #45a049;
            transform: translateX(5px);
        }

        /* Sección de acceso especial */
        .special-access {
            text-align: center;
            margin-top: 2rem;
            animation: fadeIn 1s ease-out 0.8s both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .special-access a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .special-access a:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2em;
            }

            .user-type-selector {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .user-type-card {
                padding: 2rem;
            }

            .user-type-icon {
                font-size: 3em;
            }

            .user-type-title {
                font-size: 1.5em;
            }
        }

        /* Estilo adicional para mantener abierto en mobile */
        @media (hover: none) {
            .dropdown-cascade {
                max-height: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🌟 Bienvenido a Consultores Chiriquí</h1>
            <p>Elige cómo deseas acceder a la plataforma</p>
        </div>

        <div class="user-type-selector">
            <!-- Opción: Persona -->
            <div class="user-type-card" onmouseenter="showDropdown(this)" onmouseleave="hideDropdown(this)">
                <div class="user-type-icon">👤</div>
                <h2 class="user-type-title">Persona</h2>
                <p class="user-type-description">
                    Busco empleo y deseo postularme a las vacantes disponibles
                </p>
                
                <div class="dropdown-cascade">
                    <div class="cascade-buttons">
                        <a href="<?= ENV_APP['BASE_URL'] ?>/auth/login?tipo=persona" class="cascade-button btn-login">
                            🔑 Iniciar Sesión
                        </a>
                        <a href="<?= ENV_APP['BASE_URL'] ?>/auth/registro?tipo=persona" class="cascade-button btn-register">
                            ✨ Registrarse
                        </a>
                    </div>
                </div>
            </div>

            <!-- Opción: Empresa -->
            <div class="user-type-card" onmouseenter="showDropdown(this)" onmouseleave="hideDropdown(this)">
                <div class="user-type-icon">🏢</div>
                <h2 class="user-type-title">Empresa</h2>
                <p class="user-type-description">
                    Representante de una empresa que busca publicar vacantes
                </p>
                
                <div class="dropdown-cascade">
                    <div class="cascade-buttons">
                        <a href="<?= ENV_APP['BASE_URL'] ?>/auth/login?tipo=empresa" class="cascade-button btn-login">
                            🔑 Iniciar Sesión
                        </a>
                        <a href="<?= ENV_APP['BASE_URL'] ?>/auth/registro?tipo=empresa" class="cascade-button btn-register">
                            ✨ Registrarse
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acceso especial para consultora -->
        <div class="special-access">
            <a href="<?= ENV_APP['BASE_URL'] ?>/auth/login?tipo=consultora">
                👨‍💼 Acceso Consultora
            </a>
        </div>
    </div>

    <script>
        function showDropdown(card) {
            const dropdown = card.querySelector('.dropdown-cascade');
            dropdown.classList.add('active');
        }

        function hideDropdown(card) {
            // En desktop mantener el dropdown visible al hacer hover
            // En mobile se mantendrá abierto por el CSS
        }

        // Para mobile: toggle al hacer click
        if (window.matchMedia('(max-width: 768px)').matches) {
            document.querySelectorAll('.user-type-card').forEach(card => {
                card.addEventListener('click', function(e) {
                    // No cerrar si se hace click en un botón
                    if (e.target.classList.contains('cascade-button')) {
                        return;
                    }
                    
                    const dropdown = this.querySelector('.dropdown-cascade');
                    dropdown.classList.toggle('active');
                });
            });
        }
    </script>
</body>
</html>
<?php
/**
 * Navbar Component
 * Barra de navegación moderna y responsive
 */

// Obtener la URL base de la configuración
$baseUrl = defined('BASE_URL') ? BASE_URL : ENV_APP['BASE_URL'];
$assetsUrl = defined('ASSETS_URL') ? ASSETS_URL : ENV_APP['ASSETS_URL'];

// Verificar si el usuario está autenticado
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? ($_SESSION['user_name'] ?? 'Usuario') : '';
$userRole = $isLoggedIn ? ($_SESSION['user_role'] ?? '') : '';
?>

<style>
    /* Reset y configuración base */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Variables CSS - Modo Claro (por defecto) */
    :root {
        --navbar-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --navbar-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        --text-white: #ffffff;
        --text-light: #f0f0f0;
        --hover-bg: rgba(255, 255, 255, 0.15);
        --active-bg: rgba(255, 255, 255, 0.25);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        
        /* Variables para el body (modo claro) */
        --bg-primary: #f8f9fa;
        --bg-secondary: #ffffff;
        --text-primary: #212529;
        --text-secondary: #6c757d;
        --border-color: #dee2e6;
    }

    /* Variables CSS - Modo Oscuro */
    [data-theme="dark"] {
        --navbar-bg: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        --navbar-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        --text-white: #ffffff;
        --text-light: #e0e0e0;
        --hover-bg: rgba(255, 255, 255, 0.1);
        --active-bg: rgba(255, 255, 255, 0.2);
        
        /* Variables para el body (modo oscuro) */
        --bg-primary: #0f0f1e;
        --bg-secondary: #1a1a2e;
        --text-primary: #ffffff;
        --text-secondary: #b0b0b0;
        --border-color: #2d2d44;
    }

    /* Aplicar variables al body */
    body {
        background-color: var(--bg-primary);
        color: var(--text-primary);
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* Navbar Principal */
    .navbar {
        background: var(--navbar-bg);
        box-shadow: var(--navbar-shadow);
        position: sticky;
        top: 0;
        z-index: 1000;
        backdrop-filter: blur(10px);
    }

    .navbar-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-height: 70px;
    }

    /* Logo */
    .navbar-logo {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
        color: var(--text-white);
        font-size: 1.5rem;
        font-weight: 700;
        transition: var(--transition);
    }

    .navbar-logo:hover {
        transform: translateY(-2px);
    }

    .navbar-logo-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        padding: 0.5rem;
    }

    /* Menú de Navegación */
    .navbar-menu {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        list-style: none;
    }

    .navbar-item {
        position: relative;
    }

    .navbar-link {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        color: var(--text-white);
        text-decoration: none;
        font-weight: 500;
        border-radius: 8px;
        transition: var(--transition);
        white-space: nowrap;
    }

    .navbar-link:hover {
        background: var(--hover-bg);
        transform: translateY(-2px);
    }

    .navbar-link.active {
        background: var(--active-bg);
    }

    /* Icono en los links */
    .navbar-link i {
        font-size: 1.1rem;
    }

    /* Botón Hamburguesa */
    .navbar-toggle {
        display: none;
        flex-direction: column;
        gap: 0.35rem;
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
    }

    .navbar-toggle span {
        display: block;
        width: 28px;
        height: 3px;
        background: var(--text-white);
        border-radius: 3px;
        transition: var(--transition);
    }

    .navbar-toggle.active span:nth-child(1) {
        transform: rotate(45deg) translate(8px, 8px);
    }

    .navbar-toggle.active span:nth-child(2) {
        opacity: 0;
    }

    .navbar-toggle.active span:nth-child(3) {
        transform: rotate(-45deg) translate(8px, -8px);
    }

    /* Botón de Tema (Dark/Light Mode) */
    .theme-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 45px;
        height: 45px;
        background: rgba(255, 255, 255, 0.15);
        border: none;
        border-radius: 50%;
        cursor: pointer;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .theme-toggle:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: scale(1.1) rotate(15deg);
    }

    .theme-toggle svg {
        width: 24px;
        height: 24px;
        fill: var(--text-white);
        stroke: var(--text-white);
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
        transition: var(--transition);
    }

    .theme-toggle .moon-icon,
    .theme-toggle .sun-icon {
        position: absolute;
        transition: var(--transition);
    }

    .theme-toggle .sun-icon {
        opacity: 1;
        transform: rotate(0deg) scale(1);
    }

    .theme-toggle .moon-icon {
        opacity: 0;
        transform: rotate(180deg) scale(0);
    }

    [data-theme="dark"] .theme-toggle .sun-icon {
        opacity: 0;
        transform: rotate(-180deg) scale(0);
    }

    [data-theme="dark"] .theme-toggle .moon-icon {
        opacity: 1;
        transform: rotate(0deg) scale(1);
    }

    /* Dropdown de Usuario */
    .navbar-user {
        position: relative;
    }

    .navbar-user-button {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 1rem;
        background: rgba(255, 255, 255, 0.15);
        border: none;
        border-radius: 50px;
        color: var(--text-white);
        cursor: pointer;
        transition: var(--transition);
        font-weight: 500;
    }

    .navbar-user-button:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-2px);
    }

    .navbar-user-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
    }

    .navbar-dropdown {
        position: absolute;
        top: calc(100% + 0.5rem);
        right: 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        min-width: 220px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: var(--transition);
        overflow: hidden;
    }

    .navbar-user:hover .navbar-dropdown,
    .navbar-dropdown:hover {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .navbar-dropdown-item {
        padding: 0.75rem 1.25rem;
        color: #333;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: var(--transition);
        border-bottom: 1px solid #f0f0f0;
    }

    .navbar-dropdown-item:last-child {
        border-bottom: none;
    }

    .navbar-dropdown-item:hover {
        background: #f8f9fa;
        color: #667eea;
    }

    .navbar-dropdown-item i {
        font-size: 1.1rem;
        width: 20px;
    }

    /* Botón CTA */
    .navbar-cta {
        padding: 0.75rem 1.5rem !important;
        background: white;
        color: #667eea !important;
        font-weight: 600;
        border-radius: 50px;
        box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
    }

    .navbar-cta:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 6px 20px rgba(255, 255, 255, 0.3);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .navbar-container {
            padding: 0 1rem;
        }

        .navbar-toggle {
            display: flex;
        }

        .navbar-menu {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--navbar-bg);
            flex-direction: column;
            padding: 1rem;
            gap: 0.25rem;
            max-height: 0;
            overflow: hidden;
            transition: var(--transition);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar-menu.active {
            max-height: 500px;
        }

        .navbar-link {
            width: 100%;
            justify-content: flex-start;
        }

        .navbar-user-button {
            width: 100%;
            justify-content: flex-start;
        }

        .navbar-dropdown {
            position: static;
            opacity: 1;
            visibility: visible;
            transform: none;
            box-shadow: none;
            margin-top: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
        }

        .navbar-dropdown-item {
            color: var(--text-white);
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }

        .navbar-dropdown-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-white);
        }
    }

    /* Animaciones */
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .navbar {
        animation: slideDown 0.5s ease-out;
    }
</style>

<!-- Navbar HTML -->
<nav class="navbar">
    <div class="navbar-container">
        <!-- Logo -->
        <a href="<?= $baseUrl ?>" class="navbar-logo">
            <div class="navbar-logo-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L2 7L12 12L22 7L12 2Z" fill="white"/>
                    <path d="M2 17L12 22L22 17" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 12L12 17L22 12" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <span>Consultores Chiriquí</span>
        </a>

        <!-- Botón Toggle para móvil -->
        <button class="navbar-toggle" id="navbarToggle" aria-label="Toggle navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <!-- Menú de Navegación -->
        <ul class="navbar-menu" id="navbarMenu">
            <li class="navbar-item">
                <a href="<?= $baseUrl ?>" class="navbar-link">
                    <i>🏠</i>
                    <span>Inicio</span>
                </a>
            </li>
            
            <li class="navbar-item">
                <a href="<?= $baseUrl ?>/vacantes" class="navbar-link">
                    <i>💼</i>
                    <span>Vacantes</span>
                </a>
            </li>

            <?php if ($isLoggedIn): ?>
                <?php if ($userRole === 'empresa'): ?>
                    <li class="navbar-item">
                        <a href="<?= $baseUrl ?>/dashboard/empresa" class="navbar-link">
                            <i>📊</i>
                            <span>Panel Empresa</span>
                        </a>
                    </li>
                <?php elseif ($userRole === 'candidato'): ?>
                    <li class="navbar-item">
                        <a href="<?= $baseUrl ?>/dashboard/candidato" class="navbar-link">
                            <i>📋</i>
                            <span>Mis Aplicaciones</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Usuario Autenticado -->
                <li class="navbar-item navbar-user">
                    <button class="navbar-user-button">
                        <div class="navbar-user-avatar">
                            <?= strtoupper(substr($userName, 0, 1)) ?>
                        </div>
                        <span><?= htmlspecialchars($userName) ?></span>
                    </button>
                    
                    <div class="navbar-dropdown">
                        <a href="<?= $baseUrl ?>/perfil" class="navbar-dropdown-item">
                            <i>👤</i>
                            <span>Mi Perfil</span>
                        </a>
                        <a href="<?= $baseUrl ?>/configuracion" class="navbar-dropdown-item">
                            <i>⚙️</i>
                            <span>Configuración</span>
                        </a>
                        <a href="<?= $baseUrl ?>/logout" class="navbar-dropdown-item">
                            <i>🚪</i>
                            <span>Cerrar Sesión</span>
                        </a>
                    </div>
                </li>
            <?php else: ?>
                <!-- Usuario No Autenticado -->
                <li class="navbar-item">
                    <a href="<?= $baseUrl ?>/public/login.php" class="navbar-link">
                        <i>🔑</i>
                        <span>Iniciar Sesión</span>
                    </a>
                </li>
                <li class="navbar-item">
                    <a href="<?= $baseUrl ?>/public/registro.php" class="navbar-link navbar-cta">
                        <i>✨</i>
                        <span>Registrarse</span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Botón de Tema (siempre visible) -->
            <li class="navbar-item">
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                    <svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="5"/>
                        <line x1="12" y1="1" x2="12" y2="3"/>
                        <line x1="12" y1="21" x2="12" y2="23"/>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                        <line x1="1" y1="12" x2="3" y2="12"/>
                        <line x1="21" y1="12" x2="23" y2="12"/>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                    </svg>
                    <svg class="moon-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                </button>
            </li>
        </ul>
    </div>
</nav>

<!-- JavaScript para el menú móvil -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('navbarToggle');
        const menu = document.getElementById('navbarMenu');
        
        if (toggle && menu) {
            toggle.addEventListener('click', function() {
                this.classList.toggle('active');
                menu.classList.toggle('active');
            });

            // Cerrar menú al hacer clic en un enlace (móvil)
            const links = menu.querySelectorAll('.navbar-link');
            links.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        toggle.classList.remove('active');
                        menu.classList.remove('active');
                    }
                });
            });

            // Cerrar menú al hacer clic fuera
            document.addEventListener('click', function(event) {
                const isClickInsideNav = event.target.closest('.navbar-container');
                if (!isClickInsideNav && menu.classList.contains('active')) {
                    toggle.classList.remove('active');
                    menu.classList.remove('active');
                }
            });
        }

        // Marcar enlace activo según la URL actual
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.navbar-link');
        
        navLinks.forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
            }
        });

        // Funcionalidad del Theme Toggle (Dark/Light Mode)
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        
        // Cargar tema guardado o detectar preferencia del sistema
        const savedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const currentTheme = savedTheme || (prefersDark ? 'dark' : 'light');
        
        // Aplicar tema inicial
        html.setAttribute('data-theme', currentTheme);
        
        // Event listener para cambiar tema
        if (themeToggle) {
            themeToggle.addEventListener('click', function() {
                const currentTheme = html.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                
                // Animación del botón
                this.style.transform = 'scale(0.9) rotate(180deg)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 300);
            });
        }
    });
</script>

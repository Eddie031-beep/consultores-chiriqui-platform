<?php
/**
 * Navbar Component - RUTAS CORREGIDAS
 * app/Views/components/navbar.php
 */

// Obtener la URL base de la configuración
$baseUrl = defined('BASE_URL') ? BASE_URL : ENV_APP['BASE_URL'];
$assetsUrl = defined('ASSETS_URL') ? ASSETS_URL : ENV_APP['ASSETS_URL'];

// Verificar si el usuario está autenticado
$isLoggedIn = isset($_SESSION['user']);
$userName = $isLoggedIn ? ($_SESSION['user']['nombre'] ?? 'Usuario') : '';
$userRole = $isLoggedIn ? ($_SESSION['user']['rol'] ?? '') : '';
?>

<!-- Estilos del Navbar -->
<style>
    :root {
        --navbar-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --navbar-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        --text-white: #ffffff;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    [data-theme="dark"] {
        --navbar-bg: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    }

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

    .navbar-menu {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        list-style: none;
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
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-2px);
        color: var(--text-white) !important;
    }

    .navbar-cta {
        padding: 0.75rem 1.5rem !important;
        background: white;
        color: #667eea !important;
        font-weight: 600;
        border-radius: 50px;
        box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
    }

    .navbar-user-dropdown {
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

    /* Dropdown del usuario */
    .navbar-dropdown {
        position: absolute;
        top: calc(100% + 0.5rem);
        right: 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        min-width: 220px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: var(--transition);
        overflow: hidden;
        z-index: 1000;
        border: 1px solid rgba(0,0,0,0.05);
    }

    [data-theme="dark"] .navbar-dropdown {
        background: #1e293b;
        border: 1px solid #334155;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    }

    /* Mostrar dropdown al hacer hover */
    .navbar-user-dropdown:hover .navbar-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .navbar-dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
        color: #333;
        text-decoration: none;
        transition: var(--transition);
        border-bottom: 1px solid #f0f0f0;
        font-weight: 500;
    }

    [data-theme="dark"] .navbar-dropdown-item {
        color: #e2e8f0;
        border-bottom-color: #334155;
    }

    .navbar-dropdown-item:last-child {
        border-bottom: none;
    }

    .navbar-dropdown-item:hover {
        background: #f8f9fa;
        color: #667eea;
        padding-left: 1.8rem;
    }

    [data-theme="dark"] .navbar-dropdown-item:hover {
        background: rgba(255, 255, 255, 0.05);
        color: #818cf8;
    }

    /* Botón de tema */
    .theme-toggle {
        width: 45px;
        height: 45px;
        background: rgba(255, 255, 255, 0.15);
        border: none;
        border-radius: 50%;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .theme-toggle:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: scale(1.05);
    }

    @media (max-width: 768px) {
        .navbar-menu {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--navbar-bg);
            flex-direction: column;
            padding: 1rem;
            max-height: 0;
            overflow: hidden;
            transition: var(--transition);
        }

        .navbar-menu.active {
            max-height: 500px;
        }
    }
</style>

<!-- Navbar HTML -->
<nav class="navbar">
    <div class="navbar-container">
        <!-- Logo -->
        <a href="<?= $baseUrl ?>" class="navbar-logo">
            <span>💼 Consultores Chiriquí</span>
        </a>

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
                <!-- USUARIO AUTENTICADO -->
                <?php if ($userRole === 'empresa' || $userRole === 'empresa_admin'): ?>
                    <li class="navbar-item">
                        <a href="<?= $baseUrl ?>/empresa/dashboard" class="navbar-link">
                            <i>📊</i>
                            <span>Panel Empresa</span>
                        </a>
                    </li>
                <?php elseif ($userRole === 'candidato'): ?>
                    <li class="navbar-item">
                        <a href="<?= $baseUrl ?>/candidato/dashboard" class="navbar-link">
                            <i>📋</i>
                            <span>Mis Postulaciones</span>
                        </a>
                    </li>
                <?php elseif ($userRole === 'admin_consultora'): ?>
                    <li class="navbar-item">
                        <a href="<?= $baseUrl ?>/consultora/dashboard" class="navbar-link">
                            <i>👨‍💼</i>
                            <span>Panel Consultora</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Dropdown de Usuario -->
                <li class="navbar-item navbar-user-dropdown">
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
                        <a href="<?= $baseUrl ?>/logout" class="navbar-dropdown-item">
                            <i>🚪</i>
                            <span>Cerrar Sesión</span>
                        </a>
                    </div>
                </li>
            <?php else: ?>
                <!-- USUARIO NO AUTENTICADO - RUTAS CORREGIDAS -->
                <li class="navbar-item">
                    <a href="<?= $baseUrl ?>/auth" class="navbar-link">
                        <i>🔑</i>
                        <span>Acceder</span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Botón de Tema -->
            <li class="navbar-item">
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                    <span style="font-size: 1.5rem;">🌙</span>
                </button>
            </li>
        </ul>
    </div>
</nav>

<!-- JavaScript -->
<script>
    // Theme Toggle
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;
    
    const savedTheme = localStorage.getItem('theme') || 
                      (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    
    html.setAttribute('data-theme', savedTheme);
    themeToggle.innerHTML = savedTheme === 'dark' ? '<span style="font-size: 1.5rem;">☀️</span>' : '<span style="font-size: 1.5rem;">🌙</span>';
    
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            this.innerHTML = newTheme === 'dark' ? '<span style="font-size: 1.5rem;">☀️</span>' : '<span style="font-size: 1.5rem;">🌙</span>';
            
            this.style.transform = 'scale(0.9) rotate(180deg)';
            setTimeout(() => {
                this.style.transform = '';
            }, 300);
        });
    }

    // Marcar link activo
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.navbar-link');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.style.background = 'rgba(255, 255, 255, 0.25)';
        }
    });
</script>
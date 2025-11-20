<?php
/**
 * Navbar Component
 * Barra de navegación moderna y responsive con Dark Mode
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
        
        /* Variables para el body (modo claro) - MEJORADO */
        --bg-primary: #f8f9fa;
        --bg-secondary: #ffffff;
        --bg-card: #ffffff;
        --text-primary: #1a1a1a;        /* Más oscuro y legible */
        --text-secondary: #4a5568;      /* Gris más oscuro */
        --text-heading: #0f172a;        /* Casi negro para títulos */
        --border-color: #e2e8f0;
        --shadow-color: rgba(0, 0, 0, 0.08);
    }

    /* Variables CSS - Modo Oscuro - MEJORADO */
    [data-theme="dark"] {
        --navbar-bg: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        --navbar-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        --text-white: #ffffff;
        --text-light: #e0e0e0;
        --hover-bg: rgba(255, 255, 255, 0.1);
        --active-bg: rgba(255, 255, 255, 0.2);
        
        /* Variables para el body (modo oscuro) - COLORES MEJORADOS */
        --bg-primary: #0f172a;          /* Azul oscuro más suave */
        --bg-secondary: #1e293b;        /* Azul gris medio */
        --bg-card: #1e293b;             /* Azul gris para cards */
        --text-primary: #f1f5f9;        /* Blanco casi puro */
        --text-secondary: #94a3b8;      /* Gris azulado claro */
        --text-heading: #ffffff;        /* Blanco puro para títulos */
        --border-color: #334155;        /* Borde azul gris */
        --shadow-color: rgba(0, 0, 0, 0.5);
    }

    /* Aplicar variables al body */
    body {
        background-color: var(--bg-primary);
        color: var(--text-primary);
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* ===== ESTILOS GLOBALES PARA DARK MODE ===== */
    
    /* Cards y contenedores */
    .container,
    .filtros,
    .vacante-card,
    .nav-top,
    .vacante-container,
    .postulaciones,
    .card {
        background-color: var(--bg-card) !important;
        color: var(--text-primary) !important;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* Títulos principales */
    h1, h2, h3, h4, h5, h6,
    .vacante-titulo,
    .header-vacantes h1,
    .section-titulo,
    .card-titulo,
    .postulacion-info h3 {
        color: var(--text-heading) !important;
    }

    /* Modo oscuro específico para títulos en tarjetas - MEJORADO */
    [data-theme="dark"] .vacante-titulo,
    [data-theme="dark"] .vacante-card .vacante-titulo,
    [data-theme="dark"] .vacante-card h2,
    [data-theme="dark"] .card-titulo,
    [data-theme="dark"] h1,
    [data-theme="dark"] h2,
    [data-theme="dark"] h3 {
        color: #ffffff !important;  /* Blanco puro para máximo contraste */
    }
    
    /* Modo claro - asegurar buen contraste */
    .vacante-titulo,
    .vacante-card .vacante-titulo,
    .card-titulo {
        color: #0f172a !important;  /* Casi negro en modo claro */
        font-weight: 700;
    }

    /* Texto secundario - MEJORADO */
    p, span, label,
    .vacante-descripcion,
    .vacante-ubicacion,
    .contador-vacantes,
    .vacante-info,
    .section-contenido,
    .card-desc,
    .postulacion-fecha {
        color: var(--text-secondary) !important;
    }
    
    /* Modo claro - texto más oscuro */
    p, span, label {
        color: #4a5568 !important;
    }
    
    /* Modo oscuro - texto más claro */
    [data-theme="dark"] p,
    [data-theme="dark"] span:not(.navbar-link span),
    [data-theme="dark"] label {
        color: #cbd5e1 !important;
    }

    /* Inputs y selects */
    .filtro-grupo input,
    .filtro-grupo select,
    input, select, textarea {
        background-color: var(--bg-secondary) !important;
        color: var(--text-primary) !important;
        border-color: var(--border-color) !important;
    }

    input::placeholder,
    textarea::placeholder {
        color: var(--text-secondary) !important;
        opacity: 0.7;
    }

    /* Empresas nombres - MEJORADO */
    .vacante-empresa,
    .postulacion-empresa {
        color: #667eea !important;
        font-weight: 600;
    }

    /* Más brillante y visible en modo oscuro */
    [data-theme="dark"] .vacante-empresa,
    [data-theme="dark"] .postulacion-empresa {
        color: #818cf8 !important;  /* Índigo más brillante */
    }

    /* Badges - MEJORADO */
    .badge {
        font-weight: 600;
        font-size: 0.85rem;
    }
    
    /* Modo claro - badges con buen contraste */
    .badge-modalidad {
        background-color: #dbeafe !important;
        color: #1e40af !important;
    }
    
    /* Modo oscuro - badges más brillantes */
    [data-theme="dark"] .badge {
        background-color: rgba(99, 102, 241, 0.15) !important;
        color: #a5b4fc !important;
    }

    [data-theme="dark"] .badge-modalidad,
    [data-theme="dark"] .badge-estado {
        background-color: rgba(99, 102, 241, 0.2) !important;
        color: #c7d2fe !important;
    }

    /* Borders y sombras - MEJORADO */
    .vacante-card,
    .filtros,
    .nav-top,
    .card {
        border: 1px solid var(--border-color);
        box-shadow: 0 2px 8px var(--shadow-color);
        transition: all 0.3s ease;
    }
    
    [data-theme="dark"] .vacante-card,
    [data-theme="dark"] .filtros,
    [data-theme="dark"] .nav-top,
    [data-theme="dark"] .card {
        border: 1px solid #334155 !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4) !important;
    }
    
    /* Hover effect mejorado */
    .vacante-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
    
    [data-theme="dark"] .vacante-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.6) !important;
        border-color: #475569 !important;
    }

    /* Links */
    a {
        color: #667eea;
        transition: color 0.3s ease;
    }

    [data-theme="dark"] a:not(.btn-detalle):not(.btn-postular):not(.btn-volver):not(.navbar-link) {
        color: #96aaf9;
    }

    /* Hero sections */
    [data-theme="dark"] .hero,
    [data-theme="dark"] .header {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%) !important;
    }

    /* Info items */
    .info-item-label {
        color: var(--text-secondary) !important;
    }

    .info-item-valor {
        color: var(--text-heading) !important;
    }

    [data-theme="dark"] .info-item-valor {
        color: #f3f4f6 !important;
    }

    /* Empresa info */
    [data-theme="dark"] .empresa-info {
        background-color: rgba(255, 255, 255, 0.05) !important;
    }

    /* Alertas */
    [data-theme="dark"] .alerta,
    [data-theme="dark"] .alerta-registro {
        background-color: rgba(255, 193, 7, 0.2) !important;
        color: #fbbf24 !important;
    }

    /* Header de vacantes */
    .vacante-header {
        border-bottom-color: var(--border-color) !important;
    }

    /* ===== NAVBAR STYLES ===== */

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
        color: var(--text-white) !important;
    }

    .navbar-link.active {
        background: var(--active-bg);
    }

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

    /* Botón de Tema */
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

    [data-theme="dark"] .navbar-dropdown {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
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

    [data-theme="dark"] .navbar-dropdown-item {
        color: var(--text-primary);
        border-bottom-color: var(--border-color);
    }

    .navbar-dropdown-item:last-child {
        border-bottom: none;
    }

    .navbar-dropdown-item:hover {
        background: #f8f9fa;
        color: #667eea;
    }

    [data-theme="dark"] .navbar-dropdown-item:hover {
        background: rgba(255, 255, 255, 0.05);
        color: #96aaf9;
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
        color: #667eea !important;
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
                    <a href="<?= $baseUrl ?>/auth/login-candidato" class="navbar-link">
                        <i>🔑</i>
                        <span>Iniciar Sesión</span>
                    </a>
                </li>
                <li class="navbar-item">
                    <a href="<?= $baseUrl ?>/auth/registro-candidato" class="navbar-link navbar-cta">
                        <i>✨</i>
                        <span>Registrarse</span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Botón de Tema -->
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

<!-- JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('navbarToggle');
        const menu = document.getElementById('navbarMenu');
        
        if (toggle && menu) {
            toggle.addEventListener('click', function() {
                this.classList.toggle('active');
                menu.classList.toggle('active');
            });

            const links = menu.querySelectorAll('.navbar-link');
            links.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        toggle.classList.remove('active');
                        menu.classList.remove('active');
                    }
                });
            });

            document.addEventListener('click', function(event) {
                const isClickInsideNav = event.target.closest('.navbar-container');
                if (!isClickInsideNav && menu.classList.contains('active')) {
                    toggle.classList.remove('active');
                    menu.classList.remove('active');
                }
            });
        }

        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.navbar-link');
        
        navLinks.forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
            }
        });

        // Theme Toggle
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        
        const savedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const currentTheme = savedTheme || (prefersDark ? 'dark' : 'light');
        
        html.setAttribute('data-theme', currentTheme);
        
        if (themeToggle) {
            themeToggle.addEventListener('click', function() {
                const currentTheme = html.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                
                this.style.transform = 'scale(0.9) rotate(180deg)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 300);
            });
        }
    });
</script>
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
        /* User Requested: White Header */
        --navbar-bg-scrolled: #ffffff;
        --navbar-text: #1e293b; /* Slate 800 */
        --navbar-width: 1200px;
    }

    .navbar {
        background: #ffffff; /* Always White */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
        transition: all 0.3s ease;
        padding: 10px 0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); /* Slight shadow always */
    }

    .navbar.scrolled {
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        padding: 10px 0;
    }

    .navbar-container {
        max-width: var(--navbar-width);
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .navbar-logo {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        color: var(--navbar-text);
        margin-right: 5rem;
        position: relative;
    }

    .navbar-menu {
        display: flex;
        align-items: center;
        gap: 60px;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .navbar-link {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #64748b; /* Slate 500 */
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.2s;
        padding: 8px 12px;
        border-radius: 8px;
    }

    .navbar-link:hover, .navbar-link.active {
        background: #eff6ff; /* Light Blue Hover */
        color: #2563eb; /* Blue Text */
    }

    /* User Profile & Dropdown */
    .navbar-user-section {
        position: relative;
    }

    .user-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f1f5f9; /* Light Gray */
        border: 1px solid #e2e8f0;
        padding: 6px 15px;
        border-radius: 30px;
        color: #334155;
        cursor: pointer;
        transition: all 0.2s;
    }

    .user-btn:hover {
        background: #e2e8f0;
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        background: #2563eb; /* Brand Blue */
        color: white; /* White Text */
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
    }

    /* Dropdown */
    .nav-dropdown {
        position: absolute;
        top: 120%;
        right: 0;
        background: white;
        border-radius: 12px;
        width: 200px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.2s;
        overflow: hidden;
        padding: 5px;
        border: 1px solid #e2e8f0;
    }

    .navbar-user-section:hover .nav-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-item {
        display: block;
        padding: 10px 15px;
        color: #334155;
        text-decoration: none;
        font-size: 0.9rem;
        border-radius: 8px;
        transition: background 0.2s;
    }

    .dropdown-item:hover {
        background: #f1f5f9;
        color: #2563eb;
    }

    /* Theme Toggle */
    .nav-theme-toggle {
        background: transparent;
        border: none;
        color: #64748b;
        cursor: pointer;
        font-size: 1.2rem;
        padding: 5px;
        transition: transform 0.3s;
    }
    
    .nav-theme-toggle:hover {
        color: #0f172a;
        transform: rotate(15deg);
    }

    @media (max-width: 768px) {
        .navbar-menu { display: none; } /* Simplified mobile for now */
    }
</style>

<!-- Navbar HTML -->
<nav class="navbar" id="mainNavbar">
    <div class="navbar-container">
        <!-- Logo -->
        <a href="<?= $baseUrl ?>" class="navbar-logo">
            <img src="<?= $assetsUrl ?>/img/logo_chiriqui_clean.png" alt="Consultores Chiriquí" style="height: 65px; width: auto; object-fit: contain;">
        </a>

        <!-- Menú -->
        <ul class="navbar-menu">
            <li>
                <a href="<?= $baseUrl ?>" class="navbar-link <?= $_SERVER['REQUEST_URI'] == '/' ? 'active' : '' ?>">
                    Inicio
                </a>
            </li>
            <li>
                <a href="<?= $baseUrl ?>/vacantes" class="navbar-link <?= strpos($_SERVER['REQUEST_URI'], '/vacantes') !== false ? 'active' : '' ?>">
                    Vacantes
                </a>
            </li>

            <?php if ($isLoggedIn): ?>
                <li class="navbar-user-section">
                    <button class="user-btn">
                        <div class="user-avatar">
                            <?= strtoupper(substr($userName, 0, 1)) ?>
                        </div>
                        <span style="font-size: 0.9rem; font-weight: 500;"><?= htmlspecialchars($userName) ?></span>
                    </button>
                    <!-- Dropdown -->
                    <div class="nav-dropdown">
                        <?php if ($userRole === 'empresa' || $userRole === 'empresa_admin'): ?>
                            <a href="<?= $baseUrl ?>/empresa/dashboard" class="dropdown-item">📊 Panel Empresa</a>
                        <?php elseif ($userRole === 'candidato'): ?>
                            <a href="<?= $baseUrl ?>/candidato/dashboard" class="dropdown-item">📋 Mis Postulaciones</a>
                        <?php endif; ?>
                        <div style="height: 1px; background: #e2e8f0; margin: 5px 0;"></div>
                        <a href="<?= $baseUrl ?>/logout" class="dropdown-item" style="color: #ef4444;">🚪 Cerrar Sesión</a>
                    </div>
                </li>
            <?php else: ?>
                <li>
                    <a href="<?= $baseUrl ?>/auth" class="navbar-link">
                        Acceder
                    </a>
                </li>
            <?php endif; ?>

            <!-- Theme Toggle Removed per User Request -->
        </ul>
    </div>
</nav>

<!-- Navbar Script -->
<script>
    // Scroll Effect
    window.addEventListener('scroll', () => {
        const nav = document.getElementById('mainNavbar');
        if (window.scrollY > 50) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    });
</script>
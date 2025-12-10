<?php
// app/Views/components/navbar.php
$baseUrl = defined('BASE_URL') ? BASE_URL : ENV_APP['BASE_URL'];
$assetsUrl = defined('ASSETS_URL') ? ASSETS_URL : ENV_APP['ASSETS_URL'];

$isLoggedIn = isset($_SESSION['user']);
$userName = $isLoggedIn ? ($_SESSION['user']['nombre'] ?? 'Usuario') : '';
$userRole = $isLoggedIn ? ($_SESSION['user']['rol'] ?? '') : '';

// Detectar página activa para el cuadro azul
$currentUri = $_SERVER['REQUEST_URI'];
?>

<style>
    /* ESTILOS HEADER UNIFICADO (Blanco y Limpio) */
    :root {
        --nav-height: 80px;
        --primary: #2563eb;
    }

    body {
        padding-top: 100px !important; /* Espacio para que no se pegue el contenido */
    }

    .navbar {
        background: #ffffff;
        position: fixed;
        top: 0; left: 0; width: 100%;
        height: var(--nav-height);
        z-index: 1000;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
    }

    .navbar-container {
        max-width: 1400px;
        width: 100%;
        margin: 0 auto;
        padding: 0 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .nav-brand {
        display: flex; align-items: center; gap: 10px;
        text-decoration: none; color: #1e293b; font-weight: 800; font-size: 1.3rem;
    }

    .nav-menu {
        display: flex; gap: 40px; align-items: center; list-style: none; margin: 0;
    }

    .nav-link {
        text-decoration: none;
        color: #64748b;
        font-weight: 500;
        font-size: 0.95rem;
        padding: 8px 16px;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .nav-link:hover { background: #f8fafc; color: var(--primary); }

    /* CUADRO AZUL ACTIVO */
    .nav-link.active {
        background-color: var(--primary);
        color: white !important;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
    }

    /* MENÚ DE USUARIO (PÍLDORA) */
    .user-pill {
        display: flex; align-items: center; gap: 12px;
        background: #f8fafc; border: 1px solid #e2e8f0;
        padding: 6px 16px 6px 6px; border-radius: 50px;
        cursor: pointer; position: relative; transition: all 0.2s;
    }
    .user-pill:hover { border-color: #cbd5e1; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }

    .user-avatar-circle {
        width: 36px; height: 36px;
        background: var(--primary); color: white;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 1rem;
    }

    .user-info { display: flex; flex-direction: column; line-height: 1.1; margin-right: 10px; text-align: left;}
    .u-name { font-weight: 600; font-size: 0.9rem; color: #1e293b; }
    .u-role { font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; }

    /* DROPDOWN */
    .nav-dropdown {
        position: absolute; top: 120%; right: 0;
        background: white; border: 1px solid #e2e8f0; border-radius: 12px;
        width: 220px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        padding: 8px; opacity: 0; visibility: hidden; transform: translateY(-10px);
        transition: all 0.2s;
    }
    .user-pill:hover .nav-dropdown { opacity: 1; visibility: visible; transform: translateY(0); }

    .dd-item {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 12px; color: #475569; text-decoration: none;
        border-radius: 8px; font-size: 0.9rem;
    }
    .dd-item:hover { background: #eff6ff; color: var(--primary); }
    .dd-logout { color: #ef4444; }
    .dd-logout:hover { background: #fef2f2; color: #dc2626; }

    @media (max-width: 900px) { .nav-menu { display: none; } }
</style>

<nav class="navbar">
    <div class="navbar-container">
        <a href="<?= $baseUrl ?>" class="nav-brand">
            <img src="<?= $assetsUrl ?>/img/logo.png" alt="Logo" height="40">
            <span>Consultores Chiriquí</span>
        </a>

        <ul class="nav-menu">
            <li>
                <a href="<?= $baseUrl ?>" class="nav-link <?= $currentUri == '/' || $currentUri == '/index.php' ? 'active' : '' ?>">Inicio</a>
            </li>
            <li>
                <a href="<?= $baseUrl ?>/vacantes" class="nav-link <?= strpos($currentUri, 'vacantes') !== false ? 'active' : '' ?>">Vacantes</a>
            </li>
            <li>
                <a href="<?= $baseUrl ?>/guia-candidato" class="nav-link <?= strpos($currentUri, 'guia') !== false ? 'active' : '' ?>">Guía de Postulación</a>
            </li>
        </ul>

        <?php if ($isLoggedIn): ?>
            <div class="user-pill">
                <div class="user-avatar-circle"><?= strtoupper(substr($userName, 0, 1)) ?></div>
                <div class="user-info">
                    <span class="u-name"><?= htmlspecialchars($userName) ?></span>
                    <span class="u-role">
                        <?php 
                            if($userRole == 'candidato') echo 'Candidato';
                            elseif($userRole == 'empresa_admin') echo 'Empresa';
                            elseif($userRole == 'admin_consultora') echo 'Admin';
                        ?>
                    </span>
                </div>
                <i class="fas fa-chevron-down" style="color: #cbd5e1; font-size: 0.8rem;"></i>

                <div class="nav-dropdown">
                    <?php if ($userRole === 'candidato'): ?>
                        <a href="<?= $baseUrl ?>/candidato/dashboard" class="dd-item"><i class="fas fa-columns"></i> Panel Principal</a>
                        <a href="<?= $baseUrl ?>/candidato/perfil" class="dd-item"><i class="fas fa-user-circle"></i> Mi Perfil</a>
                        <a href="<?= $baseUrl ?>/candidato/postulaciones" class="dd-item"><i class="fas fa-clipboard-list"></i> Mis Postulaciones</a>
                    <?php elseif ($userRole === 'empresa_admin'): ?>
                        <a href="<?= $baseUrl ?>/empresa/dashboard" class="dd-item"><i class="fas fa-columns"></i> Panel Empresa</a>
                        <a href="<?= $baseUrl ?>/empresa/perfil" class="dd-item"><i class="fas fa-building"></i> Perfil Empresa</a>
                    <?php elseif ($userRole === 'admin_consultora'): ?>
                        <a href="<?= $baseUrl ?>/consultora/dashboard" class="dd-item"><i class="fas fa-columns"></i> Administración</a>
                    <?php endif; ?>
                    
                    <div style="height:1px; background:#e2e8f0; margin:5px 0;"></div>
                    <a href="<?= $baseUrl ?>/logout" class="dd-item dd-logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                </div>
            </div>
        <?php else: ?>
            <a href="<?= $baseUrl ?>/auth" class="nav-link" style="border:1px solid #cbd5e1;">Acceder</a>
        <?php endif; ?>
    </div>
</nav>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página no encontrada | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <style>
        .error-container {
            text-align: center;
            padding: 5rem 1rem;
            max-width: 600px;
            margin: 0 auto;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 800;
            color: #667eea;
            margin-bottom: 0;
            line-height: 1;
        }
        .error-title {
            font-size: 2rem;
            margin-top: 1rem;
            margin-bottom: 1.5rem;
            color: var(--text-heading);
        }
        .error-message {
            color: var(--text-secondary);
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
        .btn-home {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            background: #5a6fd1;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <div class="error-container">
        <h1 class="error-code">404</h1>
        <h2 class="error-title">¡Ups! Página no encontrada</h2>
        <p class="error-message">
            La página que buscas no existe, se ha movido o no está disponible temporalmente.
        </p>
        <a href="<?= ENV_APP['BASE_URL'] ?>/" class="btn-home">
            🏠 Volver al Inicio
        </a>
    </div>
</body>
</html>

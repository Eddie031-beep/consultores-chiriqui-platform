<?php
session_start();

// Si ya está autenticado, redirigir al panel correspondiente
if(isset($_SESSION['user_id'])) {
    if($_SESSION['user_type'] === 'empresa') {
        header('Location: ../panel-empresa/dashboard.php');
    } elseif($_SESSION['user_type'] === 'candidato') {
        header('Location: ../panel-candidato/dashboard.php');
    } elseif($_SESSION['user_type'] === 'admin') {
        header('Location: ../panel-consultora/dashboard.php');
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Tipo de Usuario - Consultores Chiriquí</title>
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
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            width: 100%;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 50px;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .opciones-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .opcion-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .opcion-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
        }

        .opcion-icono {
            font-size: 3.5em;
            margin-bottom: 20px;
        }

        .opcion-titulo {
            font-size: 1.8em;
            color: #333;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .opcion-descripcion {
            color: #666;
            font-size: 0.95em;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .opcion-ventajas {
            text-align: left;
            margin-bottom: 25px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 10px;
        }

        .opcion-ventajas ul {
            list-style: none;
            padding: 0;
        }

        .opcion-ventajas li {
            padding: 8px 0;
            color: #666;
            font-size: 0.9em;
            border-bottom: 1px solid #eee;
        }

        .opcion-ventajas li:last-child {
            border-bottom: none;
        }

        .opcion-ventajas li::before {
            content: "✓ ";
            color: #4caf50;
            font-weight: bold;
            margin-right: 8px;
        }

        .btn-grupo {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-login {
            flex: 1;
            background: #667eea;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: background 0.3s;
            font-size: 0.95em;
        }

        .btn-login:hover {
            background: #5568d3;
        }

        .btn-registro {
            flex: 1;
            background: #4caf50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: background 0.3s;
            font-size: 0.95em;
        }

        .btn-registro:hover {
            background: #45a049;
        }

        .footer {
            text-align: center;
            color: white;
            margin-top: 40px;
            font-size: 0.9em;
        }

        .footer a {
            color: white;
            text-decoration: underline;
            transition: opacity 0.3s;
        }

        .footer a:hover {
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.8em;
            }

            .opcion-card {
                padding: 25px;
            }

            .opcion-icono {
                font-size: 2.5em;
            }

            .opcion-titulo {
                font-size: 1.4em;
            }

            .btn-grupo {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🌟 Bienvenido a Consultores Chiriquí</h1>
            <p>Elige cómo deseas acceder a la plataforma</p>
        </div>

        <!-- Opciones -->
        <div class="opciones-grid">
            <!-- Opción: Candidato / Persona Postulante -->
            <div class="opcion-card">
                <div class="opcion-icono">👤</div>
                <h2 class="opcion-titulo">Persona Postulante</h2>
                <p class="opcion-descripcion">
                    Busco empleo y deseo postularme a las vacantes disponibles
                </p>
                <div class="opcion-ventajas">
                    <ul>
                        <li>Acceso a todas las vacantes disponibles</li>
                        <li>Gestiona tus postulaciones</li>
                        <li>Recibe notificaciones de empresas</li>
                        <li>Carga y actualiza tu CV</li>
                    </ul>
                </div>
                <div class="btn-grupo">
                    <a href="login-candidato.php" class="btn-login">Iniciar Sesión</a>
                    <a href="registro-candidato.php" class="btn-registro">Registrarse</a>
                </div>
            </div>

            <!-- Opción: Empresa -->
            <div class="opcion-card">
                <div class="opcion-icono">🏢</div>
                <h2 class="opcion-titulo">Empresa</h2>
                <p class="opcion-descripcion">
                    Representante de una empresa que busca publicar vacantes
                </p>
                <div class="opcion-ventajas">
                    <ul>
                        <li>Publica vacantes de tu empresa</li>
                        <li>Gestiona candidatos postulantes</li>
                        <li>Analítica de visualizaciones</li>
                        <li>Evaluación de perfiles</li>
                    </ul>
                </div>
                <div class="btn-grupo">
                    <a href="login-empresa.php" class="btn-login">Iniciar Sesión</a>
                    <a href="registro-empresa.php" class="btn-registro">Registrarse</a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                ¿No sabes cuál elegir? 
                <a href="../public/vacantes.php">Ver vacantes disponibles primero</a>
            </p>
        </div>
    </div>
</body>
</html>
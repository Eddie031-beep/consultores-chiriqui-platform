<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guía de Postulación | Consultores Chiriquí</title>
    
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* --- CORRECCIÓN DE ESTILOS GLOBAL --- */
        body {
            font-family: 'Inter', sans-serif; /* Fuerza la fuente correcta */
            background-color: #f8fafc;
            color: #334155;
            /* Padding superior para evitar que la navbar tape el contenido */
            padding-top: 90px; 
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 1.5rem 4rem;
        }

        /* Encabezado de Página Estándar */
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-title {
            font-size: 2.2rem;
            font-weight: 800; /* Negrita estándar del sitio */
            color: #1e293b;
            margin-bottom: 1rem;
            letter-spacing: -0.5px;
        }

        .page-subtitle {
            font-size: 1.1rem;
            color: #64748b;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Tarjetas de Contenido */
        .guide-card {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            margin-bottom: 2rem;
        }

        /* Títulos dentro del contenido */
        .guide-card h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1.5rem;
            display: flex; align-items: center; gap: 10px;
        }
        .guide-card h2 i { color: #2563eb; }

        /* Texto y Listas */
        .guide-content p { line-height: 1.8; margin-bottom: 1.5rem; }
        .guide-list { padding-left: 1.5rem; margin-bottom: 1.5rem; }
        .guide-list li { margin-bottom: 1rem; line-height: 1.6; position: relative; }
        
        /* Pasos numerados */
        .step-list { counter-reset: step; list-style: none; padding: 0; }
        .step-list li { position: relative; padding-left: 50px; margin-bottom: 2rem; }
        .step-list li::before {
            counter-increment: step;
            content: counter(step);
            position: absolute; left: 0; top: 0;
            width: 35px; height: 35px;
            background: #eff6ff; color: #2563eb;
            border-radius: 50%; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
        }
        .step-title { font-weight: 700; color: #1e293b; font-size: 1.1rem; margin-bottom: 0.5rem; display: block; }

        .cta-box {
            text-align: center; padding: 3rem 1rem;
            background: radial-gradient(circle at center, #1e3a8a 0%, #0f172a 100%);
            color: white; border-radius: 16px;
        }
        .btn-primary-cta {
            display: inline-block; background: #2563eb; color: white; 
            padding: 12px 30px; border-radius: 10px; font-weight: 700; 
            text-decoration: none; margin-top: 20px; transition: background 0.2s;
        }
        .btn-primary-cta:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="container">
        
        <div class="page-header animate-slide-up">
            <h1 class="page-title">Guía de Postulación</h1>
            <p class="page-subtitle">Sigue estos pasos sencillos para encontrar y aplicar a tu próximo empleo ideal en nuestra plataforma.</p>
        </div>

        <div class="guide-card animate-slide-up delay-100">
            <h2><i class="fas fa-clipboard-check"></i> Cómo funciona el proceso</h2>
            
            <div class="guide-content">
                <ol class="step-list">
                    <li>
                        <span class="step-title">Crea tu cuenta de candidato</span>
                        Regístrate gratuitamente. Asegúrate de completar tu perfil con tu experiencia, educación y habilidades clave. Un perfil completo aumenta tus posibilidades.
                    </li>
                    <li>
                        <span class="step-title">Explora las vacantes disponibles</span>
                        Utiliza nuestro buscador y filtros avanzados para encontrar ofertas que se adapten a tu perfil, ubicación y modalidad de trabajo preferida.
                    </li>
                    <li>
                        <span class="step-title">Postúlate con un clic</span>
                        Cuando encuentres una vacante que te interese, presiona el botón "Postularme Ahora". Tu perfil se enviará directamente a la empresa.
                    </li>
                    <li>
                        <span class="step-title">Seguimiento de tus aplicaciones</span>
                        Desde tu panel de control, podrás ver el estado de todas tus postulaciones (Pendiente, Visto, En Proceso) en tiempo real.
                    </li>
                </ol>
            </div>
        </div>

        <div class="guide-card animate-slide-up delay-200">
            <h2><i class="fas fa-lightbulb"></i> Consejos para destacar</h2>
            <div class="guide-content">
                <ul class="guide-list">
                    <li><strong>Mantén tu perfil actualizado:</strong> Agrega tus últimas experiencias y logros.</li>
                    <li><strong>Sé específico en tus habilidades:</strong> Usa palabras clave relevantes para tu sector.</li>
                    <li><strong>Revisa los requisitos:</strong> Asegúrate de cumplir con los puntos clave antes de postularte.</li>
                </ul>
            </div>
        </div>

        <div class="cta-box animate-slide-up delay-300">
            <h2 style="color: white; margin-bottom: 1rem;">¿Listo para empezar?</h2>
            <p style="color: #cbd5e1; max-width: 500px; margin: 0 auto;">
                Miles de empresas están buscando talento como el tuyo. No esperes más.
            </p>
            <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes" class="btn-primary-cta">
                Ver Vacantes Ahora
            </a>
        </div>

    </div>
</body>
</html>

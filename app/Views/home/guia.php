<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guía de Postulación | Consultores Chiriquí</title>
    
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/home-elegant.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Estilos Específicos para la Guía */
        .guide-hero {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            padding: 4rem 2rem;
            text-align: center;
            color: white;
            border-radius: 0 0 50% 50% / 4%;
            margin-bottom: 3rem;
        }

        .guide-hero h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: white !important;
        }

        .guide-intro {
            max-width: 800px;
            margin: 0 auto 3rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .guide-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }

        .guide-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .guide-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-color: var(--primary-color);
        }

        .card-icon {
            font-size: 2rem;
            color: var(--primary-color);
            background: rgba(37, 99, 235, 0.1);
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-main);
        }

        .card-text {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .tip-box {
            margin-top: 1rem;
            padding: 0.75rem;
            background: #eff6ff;
            border-left: 4px solid var(--primary-color);
            border-radius: 4px;
            font-size: 0.85rem;
            color: #1e3a8a;
        }

        [data-theme="dark"] .tip-box {
            background: rgba(37, 99, 235, 0.1);
            color: #bfdbfe;
        }

        .error-list {
            list-style: none;
            padding: 0;
        }

        .error-list li {
            padding-left: 1.5rem;
            position: relative;
            margin-bottom: 0.5rem;
            color: var(--text-muted);
        }

        .error-list li::before {
            content: "✕";
            color: #ef4444;
            position: absolute;
            left: 0;
            font-weight: bold;
        }

        .cta-section {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            margin-bottom: 4rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .btn-cta {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            margin-top: 1.5rem;
            transition: background 0.3s;
        }

        .btn-cta:hover {
            background: var(--primary-hover);
            transform: scale(1.05);
        }
    </style>
</head>
<body>

    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <header class="guide-hero animate-fade-in">
        <div class="container">
            <h1>📝 Cómo postularte con éxito</h1>
            <p style="opacity: 0.9; font-size: 1.2rem;">Tu guía paso a paso para conseguir empleo en Consultores Chiriquí</p>
        </div>
    </header>

    <div class="container">
        <section class="guide-intro animate-slide-up">
            <p>
                Bienvenido al portal de gestión de vacantes líder en la región. Aquí conectamos a empresas públicas y privadas con talento como tú. 
                <strong style="color: var(--primary-color);">¿Lo mejor? No necesitas cargar una hoja de vida tradicional (PDF/Word).</strong> 
                Nuestra plataforma utiliza tu <strong>Perfil Digital</strong> para presentarte ante los reclutadores. Sigue estos consejos para destacar.
            </p>
        </section>

        <div class="guide-grid">
            
            <article class="guide-card animate-slide-up delay-100">
                <div class="card-icon"><i class="fas fa-id-card"></i></div>
                <h3 class="card-title">1. Tu Perfil es tu Hoja de Vida</h3>
                <p class="card-text">
                    Olvídate de adjuntar archivos. El sistema genera tu CV automáticamente basado en los datos que ingresas. Asegúrate de completar:
                </p>
                <ul class="card-text" style="margin-top: 0.5rem; padding-left: 1.2rem;">
                    <li>Información personal y contacto.</li>
                    <li>Formación académica y experiencia.</li>
                    <li>Habilidades técnicas y blandas.</li>
                </ul>
                <div class="tip-box">
                    💡 <strong>Tip:</strong> Un perfil completo al 100% tiene 3 veces más probabilidades de ser contactado.
                </div>
            </article>

            <article class="guide-card animate-slide-up delay-200">
                <div class="card-icon"><i class="fas fa-search-plus"></i></div>
                <h3 class="card-title">2. Lectura Estratégica</h3>
                <p class="card-text">
                    Antes de dar clic en "Aplicar", revisa los detalles. Fíjate si la empresa es pública o privada, la modalidad (remoto/presencial) y los requisitos mínimos.
                </p>
                <div class="tip-box">
                    🎯 Postúlate solo si cumples con lo esencial. Calidad es mejor que cantidad.
                </div>
            </article>

            <article class="guide-card animate-slide-up delay-300">
                <div class="card-icon"><i class="fas fa-star"></i></div>
                <h3 class="card-title">3. Destaca tus Habilidades</h3>
                <p class="card-text">
                    Las empresas buscan algo más que títulos. En tu perfil, asegúrate de listar tus:
                </p>
                <ul class="card-text" style="margin-top: 0.5rem; padding-left: 1.2rem;">
                    <li><strong>Hard Skills:</strong> Excel, Inglés, Programación, Contabilidad.</li>
                    <li><strong>Soft Skills:</strong> Liderazgo, Trabajo en equipo, Puntualidad.</li>
                </ul>
            </article>

            <article class="guide-card animate-slide-up delay-100">
                <div class="card-icon"><i class="fas fa-robot"></i></div>
                <h3 class="card-title">4. Usa nuestro Asistente IA</h3>
                <p class="card-text">
                    ¿Tienes dudas sobre una vacante o no sabes cómo completar un campo? Nuestro Chatbot está disponible 24/7.
                </p>
                <p class="card-text" style="margin-top: 0.5rem;">
                    Úsalo para consultar vacantes rápidas o resolver dudas sobre la plataforma al instante.
                </p>
            </article>

            <article class="guide-card animate-slide-up delay-200">
                <div class="card-icon"><i class="fas fa-folder-open"></i></div>
                <h3 class="card-title">5. Documentación Adicional</h3>
                <p class="card-text">
                    Aunque no subas CV ahora, ten listos tus documentos digitales. Si pasas el primer filtro, las empresas podrían pedirte:
                </p>
                <ul class="card-text" style="margin-top: 0.5rem; padding-left: 1.2rem;">
                    <li>Copia de Cédula.</li>
                    <li>Récord Policivo (si aplica).</li>
                    <li>Referencias laborales.</li>
                </ul>
            </article>

            <article class="guide-card animate-slide-up delay-300">
                <div class="card-icon"><i class="fas fa-check-circle"></i></div>
                <h3 class="card-title">6. Buenas Prácticas</h3>
                <p class="card-text">
                    La honestidad es clave. Mantén tus datos de contacto (teléfono/email) siempre actualizados y revisa tu panel de "Postulaciones" frecuentemente para ver el estado de tus solicitudes.
                </p>
            </article>
        </div>

        <section class="guide-card animate-slide-up" style="margin-bottom: 4rem; border-left: 5px solid #ef4444;">
            <h3 class="card-title" style="color: #ef4444;">⚠️ Errores comunes que debes evitar</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <ul class="error-list">
                    <li>Dejar el perfil incompleto o sin foto profesional.</li>
                    <li>Postularse a vacantes sin leer la ubicación (ej. aplicar a Chiriquí viviendo en Panamá).</li>
                </ul>
                <ul class="error-list">
                    <li>Poner información de contacto errónea.</li>
                    <li>No revisar los mensajes o notificaciones de la plataforma.</li>
                </ul>
            </div>
        </section>

        <section class="cta-section animate-slide-up">
            <h2 style="margin-bottom: 1rem; color: var(--text-heading);">¿Estás listo para dar el siguiente paso?</h2>
            <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto;">
                Antes de postularte, revisa si la vacante encaja contigo y si tu perfil está completo. Si la respuesta es sí, ¡aplica ahora! Consultores Chiriquí te conecta con oportunidades reales.
            </p>
            <div style="margin-top: 2rem;">
                <a href="<?= ENV_APP['BASE_URL'] ?>/vacantes" class="btn-cta">Ver Vacantes Disponibles</a>
                <a href="<?= ENV_APP['BASE_URL'] ?>/auth/registro?tipo=candidato" class="btn-cta" style="background: transparent; border: 2px solid var(--primary-color); color: var(--primary-color); margin-left: 1rem;">Crear mi Perfil</a>
            </div>
        </section>

    </div>

    <?php include __DIR__ . '/../components/chatbot-widget.php'; ?>

</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consultores Chiriquí</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:system-ui;background:#0f172a;color:#e5e7eb;line-height:1.6;}
        .hero{background:linear-gradient(135deg,#1e3a8a 0%,#3b82f6 100%);padding:4rem 2rem;text-align:center;}
        .hero h1{font-size:3rem;margin-bottom:1rem;}
        .hero p{font-size:1.2rem;color:#bfdbfe;max-width:700px;margin:0 auto;}
        .container{max-width:1200px;margin:0 auto;padding:3rem 2rem;}
        .section{margin-bottom:3rem;}
        .section h2{font-size:2rem;margin-bottom:1rem;color:#38bdf8;}
        .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:2rem;margin-top:2rem;}
        .card{background:#020617;padding:2rem;border-radius:1rem;border:1px solid #1e293b;box-shadow:0 25px 50px rgba(0,0,0,.5);}
        .card h3{color:#60a5fa;margin-bottom:.75rem;font-size:1.3rem;}
        .card p{color:#cbd5e1;}
        .icon{font-size:2.5rem;margin-bottom:1rem;}
        .stats{display:flex;justify-content:space-around;flex-wrap:wrap;gap:2rem;margin:2rem 0;}
        .stat{text-align:center;}
        .stat-number{font-size:3rem;font-weight:bold;color:#22c55e;}
        .stat-label{color:#9ca3af;font-size:.9rem;}
        .cta{text-align:center;margin-top:3rem;}
        .btn{display:inline-block;padding:.75rem 1.5rem;background:#22c55e;color:#022c22;text-decoration:none;border-radius:.5rem;font-weight:600;margin:.5rem;}
        .btn-secondary{background:#3b82f6;color:#fff;}
        .contact{background:#020617;padding:2rem;border-radius:1rem;border:1px solid #1e293b;margin-top:2rem;}
        .contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-top:1rem;}
        .contact-item{padding:1rem;background:#0f172a;border-radius:.5rem;}
        @media (max-width:768px){
            .hero h1{font-size:2rem;}
            .grid,.contact-grid{grid-template-columns:1fr;}
        }
    </style>
</head>
<body>
<div class="hero">
    <h1>💼 Consultores Chiriquí, S.A.</h1>
    <p>Conectamos talento con oportunidades en toda Panamá</p>
</div>

<div class="container">
    <div class="section">
        <h2>Quiénes Somos</h2>
        <p style="font-size:1.1rem;color:#cbd5e1;">
            Somos una empresa líder en consultoría de recursos humanos con sede en David, Chiriquí. 
            Especializados en conectar empresas públicas y privadas con el mejor talento disponible 
            en el mercado panameño. Nuestra plataforma innovadora permite gestionar vacantes de manera 
            eficiente y transparente.
        </p>
    </div>

    <div class="stats">
        <div class="stat">
            <div class="stat-number">500+</div>
            <div class="stat-label">Empresas registradas</div>
        </div>
        <div class="stat">
            <div class="stat-number">2,000+</div>
            <div class="stat-label">Vacantes publicadas</div>
        </div>
        <div class="stat">
            <div class="stat-number">15,000+</div>
            <div class="stat-label">Candidatos conectados</div>
        </div>
    </div>

    <div class="section">
        <h2>Nuestros Servicios</h2>
        <div class="grid">
            <div class="card">
                <div class="icon">🏢</div>
                <h3>Gestión de Vacantes</h3>
                <p>Plataforma completa para publicar y administrar ofertas laborales de empresas públicas y privadas.</p>
            </div>
            <div class="card">
                <div class="icon">🤖</div>
                <h3>Asistente Virtual</h3>
                <p>Chatbot inteligente que guía a candidatos y genera estadísticas de interacción en tiempo real.</p>
            </div>
            <div class="card">
                <div class="icon">📊</div>
                <h3>Analytics & Reporting</h3>
                <p>Sistema de métricas que permite a las empresas medir el alcance y efectividad de sus vacantes.</p>
            </div>
            <div class="card">
                <div class="icon">💳</div>
                <h3>Facturación Digital</h3>
                <p>Sistema de facturación automática compatible con DGI, basado en interacciones reales.</p>
            </div>
            <div class="card">
                <div class="icon">🔒</div>
                <h3>Contratos Digitales</h3>
                <p>Generación automática de contratos digitales con términos transparentes y tarifas claras.</p>
            </div>
            <div class="card">
                <div class="icon">🌐</div>
                <h3>Cobertura Nacional</h3>
                <p>Servidores en Chiriquí y Panamá con replicación de datos para alta disponibilidad.</p>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Modelo de Negocio</h2>
        <div class="card">
            <h3>Sistema de Peaje por Interacción</h3>
            <p style="margin-bottom:1rem;">
                Cobramos únicamente por las interacciones reales que genera cada vacante, 
                garantizando que solo pague por resultados medibles:
            </p>
            <ul style="list-style:none;padding:0;">
                <li style="padding:.5rem 0;border-bottom:1px solid #1e293b;">👁️ <strong>Vista de detalle:</strong> B/. 0.10 por visualización</li>
                <li style="padding:.5rem 0;border-bottom:1px solid #1e293b;">👆 <strong>Click en "Aplicar":</strong> B/. 0.15 por click</li>
                <li style="padding:.5rem 0;">💬 <strong>Consulta vía Chatbot:</strong> B/. 0.05 por interacción</li>
            </ul>
            <p style="margin-top:1rem;color:#9ca3af;font-size:.9rem;">
                * Más ITBMS 7% según legislación panameña
            </p>
        </div>
    </div>

    <div class="section">
        <h2>Tecnología e Infraestructura</h2>
        <p style="margin-bottom:1rem;">
            Operamos con infraestructura distribuida en dos centros de datos:
        </p>
        <div class="grid">
            <div class="card">
                <h3>📍 Chiriquí - Plaza Las Lomas</h3>
                <p>Servidor principal con base de datos maestra. Procesamiento de transacciones en tiempo real.</p>
            </div>
            <div class="card">
                <h3>📍 Panamá - TYGO Ciudad del Saber</h3>
                <p>Servidor de réplica con balanceo de carga. Garantiza disponibilidad 24/7.</p>
            </div>
        </div>
        <p style="margin-top:1rem;color:#9ca3af;">
            ISP: TYGO en ambas ubicaciones | Replicación en tiempo real | Backup automático diario
        </p>
    </div>

    <div class="section">
        <div class="contact">
            <h2 style="color:#38bdf8;margin-bottom:1rem;">Contáctanos</h2>
            <div class="contact-grid">
                <div class="contact-item">
                    <strong style="color:#60a5fa;">📞 Teléfono</strong><br>
                    +507 6000-0000<br>
                    +507 777-8888
                </div>
                <div class="contact-item">
                    <strong style="color:#60a5fa;">📧 Email</strong><br>
                    info@consultoraschiriqui.com<br>
                    ventas@consultoraschiriqui.com
                </div>
                <div class="contact-item">
                    <strong style="color:#60a5fa;">📍 Dirección Chiriquí</strong><br>
                    Plaza Las Lomas, David<br>
                    Chiriquí, Panamá
                </div>
                <div class="contact-item">
                    <strong style="color:#60a5fa;">📍 Dirección Panamá</strong><br>
                    Ciudad del Saber<br>
                    Panamá, Panamá
                </div>
            </div>
        </div>
    </div>

    <div class="cta">
        <h2 style="margin-bottom:1rem;">¿Listo para comenzar?</h2>
        <a href="<?= ENV_APP['BASE_URL'] ?>/chatbot" class="btn">🤖 Explorar Vacantes</a>
        <a href="<?= ENV_APP['BASE_URL'] ?>/login/empresa" class="btn btn-secondary">🏢 Acceso Empresas</a>
        <a href="<?= ENV_APP['BASE_URL'] ?>/" class="btn btn-secondary">🏠 Volver al Inicio</a>
    </div>
</div>

<footer style="background:#020617;padding:2rem;text-align:center;margin-top:3rem;border-top:1px solid #1e293b;">
    <p style="color:#9ca3af;">
        © 2025 Consultores Chiriquí, S.A. | RUC: 123456-1-123456 DV: 12<br>
        Sistema desarrollado para Examen Final - Desarrollo de Software IV
    </p>
</footer>
</body>
</html>
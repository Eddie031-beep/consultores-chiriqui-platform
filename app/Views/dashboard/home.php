<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consultores Chiriquí - Plataforma de Vacantes</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:system-ui;background:#0f172a;color:#e5e7eb;line-height:1.6;}
        .hero{background:linear-gradient(135deg,#1e3a8a 0%,#3b82f6 100%);padding:5rem 2rem;text-align:center;}
        .hero h1{font-size:3.5rem;margin-bottom:1rem;color:#fff;}
        .hero p{font-size:1.3rem;color:#bfdbfe;max-width:800px;margin:1rem auto;}
        .hero .buttons{margin-top:2rem;}
        .btn{display:inline-block;padding:.9rem 1.8rem;margin:.5rem;border-radius:.5rem;text-decoration:none;font-weight:600;font-size:1rem;transition:all .2s;}
        .btn-primary{background:#22c55e;color:#022c22;}
        .btn-primary:hover{background:#16a34a;transform:translateY(-2px);}
        .btn-secondary{background:#fff;color:#1e3a8a;}
        .btn-secondary:hover{background:#e5e7eb;transform:translateY(-2px);}
        .btn-outline{border:2px solid #fff;color:#fff;background:transparent;}
        .btn-outline:hover{background:#fff;color:#1e3a8a;}
        .container{max-width:1200px;margin:0 auto;padding:3rem 2rem;}
        .section{margin:3rem 0;}
        .section h2{font-size:2.2rem;margin-bottom:1.5rem;color:#38bdf8;text-align:center;}
        .features{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:2rem;margin-top:2rem;}
        .feature{background:#020617;padding:2rem;border-radius:1rem;border:1px solid #1e293b;text-align:center;transition:all .3s;}
        .feature:hover{border-color:#38bdf8;transform:translateY(-5px);box-shadow:0 20px 40px rgba(56,189,248,.15);}
        .feature-icon{font-size:3rem;margin-bottom:1rem;}
        .feature h3{color:#60a5fa;margin-bottom:.75rem;font-size:1.3rem;}
        .feature p{color:#cbd5e1;font-size:.95rem;}
        .stats{display:flex;justify-content:space-around;flex-wrap:wrap;gap:2rem;padding:3rem 0;background:#020617;border-radius:1rem;margin:2rem 0;}
        .stat{text-align:center;}
        .stat-number{font-size:3.5rem;font-weight:bold;color:#22c55e;}
        .stat-label{color:#9ca3af;font-size:1rem;}
        .cta-box{background:linear-gradient(135deg,#16a34a 0%,#22c55e 100%);padding:3rem;border-radius:1rem;text-align:center;margin:3rem 0;}
        .cta-box h2{color:#fff;font-size:2rem;margin-bottom:1rem;}
        .cta-box p{color:#d1fae5;font-size:1.1rem;margin-bottom:2rem;}
        footer{background:#020617;padding:2rem;text-align:center;border-top:1px solid #1e293b;margin-top:3rem;}
        @media (max-width:768px){
            .hero h1{font-size:2.2rem;}
            .hero p{font-size:1rem;}
            .features{grid-template-columns:1fr;}
        }
    </style>
</head>
<body>
<div class="hero">
    <h1>💼 Consultores Chiriquí</h1>
    <p>La plataforma líder en gestión de vacantes para empresas públicas y privadas en Panamá</p>
    <div class="buttons">
        <a href="<?= ENV_APP['BASE_URL'] ?>/chatbot" class="btn btn-primary">🤖 Buscar Vacantes</a>
        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/info" class="btn btn-secondary">ℹ️ Conocer más</a>
        <a href="<?= ENV_APP['BASE_URL'] ?>/login/empresa" class="btn btn-outline">🏢 Acceso Empresas</a>
    </div>
</div>

<div class="container">
    <div class="section">
        <h2>¿Por qué elegirnos?</h2>
        <div class="features">
            <div class="feature">
                <div class="feature-icon">🚀</div>
                <h3>Rápido y Eficiente</h3>
                <p>Publica vacantes en minutos y recibe candidatos cualificados de inmediato.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">🤖</div>
                <h3>Asistente IA</h3>
                <p>Chatbot inteligente que ayuda a candidatos 24/7 y genera métricas valiosas.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">📊</div>
                <h3>Analytics Avanzado</h3>
                <p>Visualiza interacciones, clics y engagement de tus publicaciones en tiempo real.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">💳</div>
                <h3>Pago por Uso</h3>
                <p>Solo pagas por las interacciones reales. Sin cuotas fijas ni costos ocultos.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">🔒</div>
                <h3>Seguro y Confiable</h3>
                <p>Infraestructura distribuida con backup automático y disponibilidad 24/7.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">🌐</div>
                <h3>Cobertura Nacional</h3>
                <p>Servidores en Chiriquí y Panamá para mejor rendimiento en todo el país.</p>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Números que hablan</h2>
        <div class="stats">
            <div class="stat">
                <div class="stat-number">500+</div>
                <div class="stat-label">Empresas registradas</div>
            </div>
            <div class="stat">
                <div class="stat-number">2,000+</div>
                <div class="stat-label">Vacantes activas</div>
            </div>
            <div class="stat">
                <div class="stat-number">15K+</div>
                <div class="stat-label">Candidatos conectados</div>
            </div>
            <div class="stat">
                <div class="stat-number">98%</div>
                <div class="stat-label">Satisfacción cliente</div>
            </div>
        </div>
    </div>

    <div class="cta-box">
        <h2>¿Listo para encontrar al candidato perfecto?</h2>
        <p>Únete a cientos de empresas que ya confían en nosotros</p>
        <a href="<?= ENV_APP['BASE_URL'] ?>/login/empresa" class="btn btn-primary" style="background:#fff;color:#16a34a;">
            Comenzar ahora →
        </a>
    </div>

    <div class="section">
        <h2>Acceso Rápido</h2>
        <div style="display:flex;justify-content:center;gap:1rem;flex-wrap:wrap;margin-top:2rem;">
            <a href="<?= ENV_APP['BASE_URL'] ?>/login/consultora" class="btn btn-secondary">👨‍💼 Login Consultora</a>
            <a href="<?= ENV_APP['BASE_URL'] ?>/login/empresa" class="btn btn-secondary">🏢 Login Empresa</a>
            <a href="<?= ENV_APP['BASE_URL'] ?>/chatbot" class="btn btn-primary">🤖 Explorar Vacantes</a>
        </div>
    </div>
</div>

<footer>
    <p style="color:#9ca3af;margin-bottom:.5rem;">
        © 2025 Consultores Chiriquí, S.A. | RUC: 123456-1-123456 DV: 12
    </p>
    <p style="color:#6b7280;font-size:.85rem;">
        Plaza Las Lomas, David, Chiriquí | Ciudad del Saber, Panamá<br>
        📞 +507 6000-0000 | 📧 info@consultoraschiriqui.com
    </p>
    <p style="color:#6b7280;font-size:.8rem;margin-top:1rem;">
        Sistema desarrollado para Examen Final - Desarrollo de Software IV<br>
        Universidad Tecnológica de Panamá
    </p>
</footer>
</body>
</html>
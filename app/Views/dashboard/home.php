<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($titulo ?? 'Inicio') ?></title>
    <style>
        body{
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #0f172a;
            color: #e5e7eb;
            margin: 0;
            padding: 2rem;
        }
        .card{
            max-width: 720px;
            margin: 3rem auto;
            background: #020617;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 25px 50px rgba(15,23,42,.7);
            border: 1px solid rgba(148,163,184,.3);
        }
        h1{
            margin-top: 0;
            font-size: 1.8rem;
        }
        .tag{
            display: inline-block;
            font-size: .75rem;
            padding: .25rem .5rem;
            border-radius: 999px;
            background: rgba(56,189,248,.15);
            color: #67e8f9;
            margin-bottom: .75rem;
        }
        .meta{
            font-size: .9rem;
            color: #9ca3af;
            margin-top: 1rem;
        }
        a{
            color: #38bdf8;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="tag">Examen Final DS4</div>
    <h1><?= htmlspecialchars($titulo ?? '') ?></h1>
    <p><?= htmlspecialchars($mensaje ?? '') ?></p>

    <div class="meta">
        <p>BD: <strong>consultores_chiriqui</strong> conectada correctamente.</p>
        <p>Próximos pasos:</p>
        <ul>
            <li>Agregar login para empresas y consultora.</li>
            <li>CRUD de empresas y vacantes.</li>
            <li>Módulo de chatbot e interacciones.</li>
            <li>Módulo de facturación (peajes).</li>
        </ul>
        <p>
            Ruta actual:
            <code>/ExamenFinalDS4/consultores-chiriqui-platform/public/</code>
        </p>
    </div>
</div>
</body>
</html>

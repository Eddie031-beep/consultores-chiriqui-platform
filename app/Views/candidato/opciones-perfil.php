<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Completar Perfil</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg-primary); color: var(--text-primary); }
        .container { max-width: 900px; margin: 4rem auto; text-align: center; padding: 0 1rem; }
        .options-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 3rem; }
        .option-card {
            background: var(--bg-card); border: 2px solid var(--border-color); border-radius: 16px;
            padding: 3rem 2rem; cursor: pointer; transition: all 0.3s ease;
        }
        .option-card:hover { border-color: #667eea; transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .icon { font-size: 4rem; margin-bottom: 1.5rem; display: block; }
        .btn { width: 100%; padding: 1rem; border-radius: 8px; border: none; font-weight: bold; cursor: pointer; margin-top: 1.5rem; }
        .btn-primary { background: #667eea; color: white; }
        .btn-outline { background: transparent; border: 2px solid #667eea; color: #667eea; }
        
        /* Modal */
        .modal-overlay { display: none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
        .modal { background: var(--bg-card); padding: 2rem; border-radius: 12px; width: 90%; max-width: 500px; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="container">
        <h1>🚀 Completa tu Perfil Profesional</h1>
        <p>Para postularte a las mejores vacantes, necesitamos conocerte.</p>

        <div class="options-grid">
            <div class="option-card" onclick="document.getElementById('modalCV').style.display = 'flex'">
                <span class="icon">📄</span>
                <h3>Cargar Hoja de Vida</h3>
                <p>Sube tu CV en PDF o Word. El sistema extraerá tus datos automáticamente.</p>
                <button class="btn btn-primary">Subir Archivo</button>
            </div>

            <div class="option-card" onclick="window.location.href='<?= ENV_APP['BASE_URL'] ?>/candidato/perfil-manual'">
                <span class="icon">✍️</span>
                <h3>Cargar Manualmente</h3>
                <p>Completa el formulario paso a paso con tus datos personales y experiencia.</p>
                <button class="btn btn-outline">Llenar Formulario</button>
            </div>
        </div>
    </div>

    <div id="modalCV" class="modal-overlay">
        <div class="modal">
            <h2>Subir Hoja de Vida</h2>
            <form action="<?= ENV_APP['BASE_URL'] ?>/candidato/subir-cv" method="POST" enctype="multipart/form-data" style="margin-top: 1.5rem;">
                <input type="file" name="cv_archivo" required accept=".pdf,.doc,.docx" style="margin-bottom: 1rem; width: 100%;">
                <div style="display:flex; gap:1rem;">
                    <button type="button" onclick="document.getElementById('modalCV').style.display = 'none'" class="btn btn-outline" style="margin:0;">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="margin:0;">Subir</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
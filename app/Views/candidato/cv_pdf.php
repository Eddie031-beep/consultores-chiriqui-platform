<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vista Previa CV - <?= htmlspecialchars($perfil['nombre']) ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #525659; /* Fondo gris oscuro estilo visor PDF */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0; 
            padding: 0;
            display: flex; 
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        /* --- BARRA DE HERRAMIENTAS SUPERIOR --- */
        .toolbar {
            width: 100%;
            background-color: #323639;
            color: #f1f1f1;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .toolbar-title {
            font-size: 1rem;
            font-weight: 500;
            display: flex; align-items: center; gap: 10px;
        }

        .btn-action {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }
        .btn-action:hover { background-color: #1d4ed8; }

        .btn-close {
            background: transparent;
            border: 1px solid #666;
            color: #ccc;
            margin-left: 10px;
        }
        .btn-close:hover { background: rgba(255,255,255,0.1); color: white; border-color: #999; }

        /* --- CONTENEDOR DE LA HOJA --- */
        .page-container {
            margin: 30px 0;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
        }

        /* --- HOJA A4 --- */
        .cv-page {
            background: white;
            width: 210mm; /* Ancho estándar A4 */
            min-height: 297mm; /* Alto mínimo A4 */
            padding: 20mm;
            box-sizing: border-box;
            color: #333;
            position: relative;
        }

        /* --- ESTILOS INTERNOS DEL CV --- */
        .cv-header { border-bottom: 2px solid #2563eb; padding-bottom: 20px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-start; }
        .cv-name { margin: 0; font-size: 22pt; color: #1e293b; text-transform: uppercase; letter-spacing: 1px; font-weight: 800; line-height: 1.1; }
        .cv-profession { margin: 5px 0 0 0; font-size: 11pt; color: #2563eb; font-weight: 600; }
        .cv-contact { font-size: 9pt; color: #64748b; text-align: right; line-height: 1.5; }
        
        .section-title {
            font-size: 11pt; color: #2563eb; text-transform: uppercase; font-weight: 700;
            border-bottom: 1px solid #e2e8f0; margin-bottom: 15px; padding-bottom: 5px; margin-top: 25px;
        }
        
        .item-row { display: flex; justify-content: space-between; margin-bottom: 2px; }
        .item-title { font-weight: 700; color: #1e293b; font-size: 11pt; }
        .item-company { font-weight: 600; color: #475569; font-size: 10pt; }
        .item-date { color: #64748b; font-size: 9pt; font-style: italic; }
        .item-desc { font-size: 10pt; color: #334155; line-height: 1.5; margin-top: 5px; margin-bottom: 15px; text-align: justify; }

        .skills-grid { display: flex; flex-wrap: wrap; gap: 8px; }
        .skill-tag { background: #f1f5f9; padding: 4px 10px; border-radius: 4px; color: #334155; font-size: 9pt; border: 1px solid #cbd5e1; }

        /* Ocultar la barra gris al imprimir con CTRL+P */
        @media print {
            .toolbar { display: none; }
            body { background: white; }
            .page-container { box-shadow: none; margin: 0; }
        }
    </style>
</head>
<body>

    <div class="toolbar" data-html2canvas-ignore="true">
        <div class="toolbar-title">
            <i class="fas fa-file-pdf" style="color: #ef4444;"></i> &nbsp; Vista Previa del Documento
        </div>
        <div style="display:flex;">
            <button onclick="descargarPDF()" class="btn-action btn-download">
                <i class="fas fa-download"></i> Descargar PDF
            </button>
            <button onclick="window.close()" class="btn-action btn-close">
                Cerrar
            </button>
        </div>
    </div>

    <div class="page-container">
        
        <div id="cv-content" class="cv-page">
            
            <div class="cv-header">
                <div>
                    <h1 class="cv-name"><?= htmlspecialchars($perfil['nombre'] . ' ' . $perfil['apellido']) ?></h1>
                    <p class="cv-profession"><?= htmlspecialchars($experiencia[0]['puesto'] ?? 'Profesional') ?></p>
                </div>
                <div class="cv-contact">
                    <?= htmlspecialchars($perfil['email']) ?><br>
                    <?= htmlspecialchars($perfil['telefono']) ?><br>
                    <?= htmlspecialchars($perfil['provincia'] ?? 'Panamá') ?>, <?= htmlspecialchars($perfil['pais'] ?? 'Panamá') ?><br>
                    ID: <?= htmlspecialchars($perfil['cedula'] ?? '') ?>
                </div>
            </div>

            <?php if(!empty($perfil['habilidades'])): ?>
            <div class="section-title">Habilidades</div>
            <div class="skills-grid">
                <?php 
                    $skills = explode(',', $perfil['habilidades']);
                    foreach($skills as $skill) {
                        $s = trim($skill);
                        if(!empty($s)) echo '<span class="skill-tag">'.htmlspecialchars($s).'</span>';
                    }
                ?>
            </div>
            <?php endif; ?>

            <div class="section-title">Experiencia Laboral</div>
            <?php if(empty($experiencia)): ?>
                <p style="color:#999; font-style:italic; font-size:10pt;">No hay experiencia registrada.</p>
            <?php else: ?>
                <?php foreach($experiencia as $exp): ?>
                <div>
                    <div class="item-row">
                        <div class="item-title"><?= htmlspecialchars($exp['puesto']) ?></div>
                        <div class="item-date">
                            <?= date('M Y', strtotime($exp['fecha_inicio'])) ?> - 
                            <?= $exp['fecha_fin'] ? date('M Y', strtotime($exp['fecha_fin'])) : 'Presente' ?>
                        </div>
                    </div>
                    <div class="item-company"><?= htmlspecialchars($exp['empresa']) ?></div>
                    <div class="item-desc"><?= nl2br(htmlspecialchars($exp['descripcion'])) ?></div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="section-title">Formación Académica</div>
            <?php if(empty($educacion)): ?>
                <p style="color:#999; font-style:italic; font-size:10pt;">No hay educación registrada.</p>
            <?php else: ?>
                <?php foreach($educacion as $edu): ?>
                <div style="margin-bottom: 10px;">
                    <div class="item-row">
                        <div class="item-title"><?= htmlspecialchars($edu['titulo']) ?></div>
                        <div class="item-date">
                            <?= $edu['fecha_graduacion'] ? date('Y', strtotime($edu['fecha_graduacion'])) : 'Cursando' ?>
                        </div>
                    </div>
                    <div class="item-company"><?= htmlspecialchars($edu['institucion']) ?></div>
                    <div style="font-size:10pt; color:#64748b;"><?= htmlspecialchars($edu['nivel']) ?></div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
        </div>

    <script>
        function descargarPDF() {
            const btn = document.querySelector('.btn-download');
            const originalText = btn.innerHTML;
            
            // Indicador de carga
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Generando...';
            btn.style.opacity = 0.7;
            btn.disabled = true;

            const element = document.getElementById('cv-content');
            
            // Configuración exacta para A4
            const opt = {
                margin: 0,
                filename: 'CV_<?= htmlspecialchars($perfil['nombre']) ?>.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true, scrollY: 0 },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(element).save().then(() => {
                // Restaurar botón al terminar
                btn.innerHTML = originalText;
                btn.style.opacity = 1;
                btn.disabled = false;
            });
        }
    </script>
</body>
</html>

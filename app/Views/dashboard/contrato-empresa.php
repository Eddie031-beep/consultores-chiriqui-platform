<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contrato de Servicios</title>
    <style>
        body{font-family:system-ui;background:#f8fafc;color:#1e293b;padding:2rem;}
        .container{max-width:800px;margin:0 auto;}
        
        .paper{
            background: white;
            padding: 3rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: 1px solid #e2e8f0;
            min-height: 800px;
        }

        .header{text-align: center; border-bottom: 2px solid #1e293b; padding-bottom: 20px; margin-bottom: 30px;}
        .title{font-size: 1.5rem; font-weight: bold; text-transform: uppercase;}
        
        .content{line-height: 1.6; white-space: pre-wrap; font-family: 'Courier New', Courier, monospace; font-size: 0.95rem;}

        .footer{margin-top: 50px; border-top: 1px solid #cbd5e1; padding-top: 20px; font-size: 0.85rem; color: #64748b; display: flex; justify-content: space-between;}

        .meta-info{
            background: #f1f5f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem;
        }

        .btn-print{
            display: block; width: 100%; text-align: center; background: #2563eb; color: white; 
            padding: 12px; text-decoration: none; border-radius: 8px; margin-top: 20px; font-weight: bold;
        }
        .btn-print:hover{background: #1d4ed8;}

        @media print {
            body { background: white; padding: 0; }
            .paper { box-shadow: none; border: none; }
            .btn-print, .back-link { display: none; }
        }

        .back-link { display: inline-block; margin-bottom: 20px; color: #64748b; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/empresas" class="back-link">← Volver al Listado</a>

        <div class="paper">
            <div class="header">
                <div class="title">Contrato Digital de Servicios</div>
                <div>Consultores Chiriquí S.A.</div>
            </div>

            <div class="meta-info">
                <strong>Empresa Cliente:</strong> <?= htmlspecialchars($contrato['empresa_nombre']) ?><br>
                <strong>Fecha de Aceptación:</strong> <?= $contrato['fecha_aceptacion'] ?><br>
                <strong>Versión:</strong> <?= $contrato['version_contrato'] ?>
            </div>

            <div class="content"><?= htmlspecialchars($contrato['texto_resumen']) ?></div>

            <div class="footer">
                <div>Firma Digital: Auto-generada al registrarse</div>
                <div>IP Registro: <?= htmlspecialchars($contrato['ip_aceptacion']) ?></div>
            </div>
        </div>

        <a href="javascript:window.print()" class="btn-print">🖨️ Imprimir Contrato</a>
    </div>
</body>
</html>

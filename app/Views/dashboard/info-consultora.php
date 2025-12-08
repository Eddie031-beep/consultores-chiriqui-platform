<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración - Consultora</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css?v=<?= time() ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-grid" style="display: block; max-width: 900px; margin: 0 auto;">
        
        <div class="page-header">
            <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/dashboard" class="back-btn">
                <i class="fas fa-arrow-left"></i> Volver al Panel
            </a>
            <h2>⚙️ Configuración del Sistema</h2>
        </div>

        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);">
            <div class="section-header">
                <div class="section-title">Información Corporativa</div>
            </div>

            <form action="<?= ENV_APP['BASE_URL'] ?>/consultora/configuracion" method="POST" onsubmit="event.preventDefault(); alert('Funcionalidad de guardado en desarrollo.');">
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #475569;">Nombre de la Plataforma</label>
                    <input type="text" value="Consultores Chiriquí, S.A." style="width: 100%; padding: 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #475569;">Email de Contacto</label>
                        <input type="email" value="admin@consultoraschiriqui.com" style="width: 100%; padding: 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #475569;">Teléfono Soporte</label>
                        <input type="text" value="+507 775-0000" style="width: 100%; padding: 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                    </div>
                </div>

                <div style="margin-bottom: 2rem; padding: 1.5rem; background: #f0f9ff; border-radius: 8px; border: 1px dashed #bae6fd;">
                    <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                        <div>
                            <strong style="color: #0369a1;">Modo Mantenimiento</strong>
                            <p style="margin: 0; font-size: 0.85rem; color: #64748b;">Desactiva el acceso público a la plataforma temporalmente.</p>
                        </div>
                        <div style="position: relative; width: 50px; height: 26px; background: #cbd5e1; border-radius: 99px;">
                            <div style="position: absolute; left: 3px; top: 3px; width: 20px; height: 20px; background: white; border-radius: 50%;"></div>
                        </div>
                    </label>
                </div>

                <div style="text-align: right;">
                    <button type="submit" style="background: var(--accent-primary); color: white; border: none; padding: 0.75rem 2rem; border-radius: 8px; font-weight: 600; cursor: pointer;">Guardar Cambios</button>
                </div>

            </form>
        </div>

        <div style="margin-top: 2rem; background: white; padding: 2rem; border-radius: 12px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);">
            <div class="section-header">
                <div class="section-title">Infraestructura</div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div style="padding: 1rem; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <h4 style="margin: 0 0 0.5rem 0; color: #1e293b;">📍 Master Node</h4>
                    <p style="margin: 0; font-size: 0.9rem; color: #64748b;">David, Chiriquí (Online)</p>
                    <div style="margin-top: 0.5rem; width: 10px; height: 10px; background: #22c55e; border-radius: 50%;"></div>
                </div>
                <div style="padding: 1rem; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <h4 style="margin: 0 0 0.5rem 0; color: #1e293b;">📍 Replica Node</h4>
                    <p style="margin: 0; font-size: 0.9rem; color: #64748b;">Ciudad de Panamá (Syncing)</p>
                    <div style="margin-top: 0.5rem; width: 10px; height: 10px; background: #3b82f6; border-radius: 50%;"></div>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
<?php
use App\Helpers\Auth;
$user = Auth::user();
$iniciales = strtoupper(substr($user['nombre'], 0, 1) . substr($user['apellido'], 0, 1));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Vistas | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .report-header {
            background: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem;
            display: flex; justify-content: space-between; align-items: center;
            border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .stat-card {
            background: #f8fafc; padding: 1rem 1.5rem; border-radius: 8px; border: 1px solid #e2e8f0;
            text-align: center; min-width: 120px;
        }
        .stat-val { font-size: 1.5rem; font-weight: 800; color: #1e293b; }
        .stat-label { font-size: 0.8rem; color: #64748b; text-transform: uppercase; font-weight: 600; }
        
        .type-badge {
            padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
        }
        .badge-view { background: #eff6ff; color: #1d4ed8; }
        .badge-click { background: #ecfdf5; color: #047857; }
        .badge-chat { background: #fdf4ff; color: #a21caf; }

        .user-guest { color: #64748b; font-style: italic; }
        .user-auth { color: #1e293b; font-weight: 600; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="dashboard-wrapper">
        <div class="report-header">
            <div>
                <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes" style="display: inline-flex; align-items: center; gap: 5px; color: #64748b; text-decoration: none; font-weight: 500; margin-bottom: 10px;">
                    <i class="fas fa-arrow-left"></i> Volver a Mis Vacantes
                </a>
                <h1 style="margin: 0; font-size: 1.8rem; color: #1e293b;">Reporte de Interacciones</h1>
                <p style="margin: 5px 0 0; color: #64748b;">
                    Vacante: <strong style="color: #2563eb;"><?= htmlspecialchars($vacante['titulo']) ?></strong>
                </p>
            </div>
            
            <div style="display: flex; gap: 1rem;">
                <div class="stat-card">
                    <div class="stat-val"><?= count($interacciones) ?></div>
                    <div class="stat-label">Total Eventos</div>
                </div>
                <!-- Podríamos agregar más stats aquí -->
            </div>
        </div>

        <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <table class="premium-table" style="margin: 0;">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Tipo de Evento</th>
                        <th>Origen</th>
                        <th>Identificación del Usuario</th>
                        <th>Detalles Técnicos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($interacciones)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 3rem; color: #94a3b8;">
                                <i class="far fa-eye-slash" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                                Aún no hay interacciones registradas para esta vacante.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($interacciones as $i): ?>
                        <tr>
                            <td style="font-weight: 500; color: #334155;">
                                <?= date('d/m/Y h:i A', strtotime($i['fecha_hora'])) ?>
                            </td>
                            <td>
                                <?php if($i['tipo_interaccion'] === 'ver_detalle'): ?>
                                    <span class="type-badge badge-view"><i class="far fa-eye"></i> Vista</span>
                                <?php elseif($i['tipo_interaccion'] === 'click_aplicar'): ?>
                                    <span class="type-badge badge-click"><i class="fas fa-mouse-pointer"></i> Click Aplicar</span>
                                <?php else: ?>
                                    <span class="type-badge badge-chat"><i class="far fa-comments"></i> Chat</span>
                                <?php endif; ?>
                            </td>
                            <td><?= ucfirst($i['origen']) ?></td>
                            <td>
                                <?php if($i['solicitante_id']): ?>
                                    <div class="user-auth">
                                        <i class="fas fa-user-check" style="color: #10b981;"></i> 
                                        <?= htmlspecialchars($i['nombre'] . ' ' . $i['apellido']) ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: #94a3b8;"><?= htmlspecialchars($i['email']) ?></div>
                                <?php else: ?>
                                    <div class="user-guest">
                                        <i class="fas fa-user-secret"></i> Invitado (No registrado)
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="font-family: monospace; font-size: 0.85rem; color: #64748b;">
                                IP: <?= htmlspecialchars($i['ip']) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

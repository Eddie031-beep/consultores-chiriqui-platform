<?php
use App\Helpers\Auth;
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generar Factura | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css?v=<?= time() ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-grid" style="display: block; max-width: 900px; margin: 0 auto;">
        
        <!-- Header -->
        <div class="page-header">
            <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion" class="back-btn">
                <i class="fas fa-arrow-left"></i> Volver a Facturación
            </a>
            <h2>🧾 Nueva Factura Fiscal</h2>
        </div>

        <!-- Error Message -->
        <?php if (!empty($error)): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #fecaca; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="form-card">
            
            <div style="margin-bottom: 2rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem;">
                <h3 style="margin: 0; color: #1e293b; font-size: 1.1rem;">Detalles de Facturación</h3>
                <p style="margin: 5px 0 0 0; color: #64748b; font-size: 0.9rem;">Selecciona la empresa y el período para calcular los cargos.</p>
            </div>

            <form action="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion/generar" method="POST">
                
                <h4 style="margin-bottom: 15px; color: #334155;">Seleccionar Vacante Cerrada</h4>
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Empresa / Vacante</label>
                    <div style="position: relative;">
                        <i class="fas fa-briefcase" style="position: absolute; left: 15px; top: 13px; color: #94a3b8;"></i>
                        <select name="vacante_id" class="form-select" onchange="this.form.submit()" style="padding-left: 2.5rem;">
                            <option value="">-- Seleccione una vacante para facturar --</option>
                            <?php if (!empty($empresasConVacantes)): ?>
                                <?php foreach ($empresasConVacantes as $empId => $empData): ?>
                                    <optgroup label="<?= htmlspecialchars($empData['nombre']) ?>">
                                        <?php foreach ($empData['vacantes'] as $vac): ?>
                                            <option value="<?= $vac['vacante_id'] ?>" <?= (isset($selectedVacanteId) && $selectedVacanteId == $vac['vacante_id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($vac['vacante_titulo']) ?> (Cierre: <?= !empty($vac['fecha_cierre']) ? date('d/m/Y', strtotime($vac['fecha_cierre'])) : 'Sin fecha' ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>No hay vacantes cerradas disponibles</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                
                <?php if (empty($empresasConVacantes)): ?>
                    <div style="padding: 1rem; background: #fef2f2; color: #dc2626; border-radius: 6px; margin-bottom: 1rem; border: 1px solid #fecaca;">
                        <i class="fas fa-info-circle"></i> No hay vacantes cerradas pendientes de facturación en este momento.
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label">Fecha Límite de Pago</label>
                    <div style="position: relative;">
                         <i class="far fa-calendar-alt" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                         <input type="date" name="fecha_vencimiento" required class="form-input" style="padding-left: 2.5rem;" value="<?= $fecha_vencimiento ?>">
                    </div>
                </div>

                <div style="background: #f0f9ff; padding: 1rem; border-radius: 8px; border: 1px dashed #bae6fd; margin-top: 1rem;">
                    <div style="display: flex; gap: 10px;">
                        <i class="fas fa-info-circle" style="color: #0284c7; margin-top: 3px;"></i>
                        <div>
                            <strong style="display: block; color: #0369a1; font-size: 0.9rem; margin-bottom: 2px;">Información de Facturación</strong>
                            <p style="margin: 0; color: #0c4a6e; font-size: 0.85rem;">
                                Se facturará el <strong>consumo total</strong> de interacciones (Vistas, Postulaciones, Chat) registrado durante toda la vida de la vacante seleccionada, desde su publicación hasta su cierre.
                            </p>
                        </div>
                    </div>
                </div>
                <!-- SECCIÓN: VISTA PREVIA (Si existe el cálculo) -->
                <?php if (isset($preview)): ?>
                    <div style="margin-top: 2rem; border-top: 2px dashed #cbd5e1; padding-top: 1.5rem;">
                        <h4 style="color: #334155; margin-bottom: 1rem;">🔎 Vista Previa del Detalle</h4>
                        
                        <div class="table-container" style="box-shadow: none; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                            <table class="premium-table" style="margin: 0;">
                                <thead style="background: #f8fafc;">
                                    <tr>
                                        <th style="padding: 0.75rem;">Concepto</th>
                                        <th style="padding: 0.75rem; text-align: center;">Cant.</th>
                                        <th style="padding: 0.75rem; text-align: right;">Unitario</th>
                                        <th style="padding: 0.75rem; text-align: right;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($preview['detalles'] as $det): ?>
                                    <tr>
                                        <td style="padding: 0.75rem; color: #475569;">
                                            <?= ucfirst(str_replace('_', ' ', $det['tipo_interaccion'])) ?>
                                        </td>
                                        <td style="padding: 0.75rem; text-align: center; color: #1e293b; font-weight: 500;">
                                            <?= $det['cantidad'] ?>
                                        </td>
                                        <td style="padding: 0.75rem; text-align: right; color: #64748b;">
                                            B/. <?= number_format($det['precio_unitario'], 2) ?>
                                        </td>
                                        <td style="padding: 0.75rem; text-align: right; color: #0f172a; font-weight: 600;">
                                            B/. <?= number_format($det['total_linea'], 2) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    
                                    <?php if(empty($preview['detalles'])): ?>
                                        <tr><td colspan="4" style="text-align:center; padding: 1rem; color: #94a3b8;">No se encontraron interacciones en este período.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot style="background: #f1f5f9; font-weight: 600;">
                                    <tr>
                                        <td colspan="3" style="text-align: right; padding: 0.75rem;">Subtotal:</td>
                                        <td style="text-align: right; padding: 0.75rem;">B/. <?= number_format($preview['subtotal'], 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="text-align: right; padding: 0.75rem;">ITBMS (7%):</td>
                                        <td style="text-align: right; padding: 0.75rem;">B/. <?= number_format($preview['itbms'], 2) ?></td>
                                    </tr>
                                    <tr style="background: #e2e8f0; color: #0f172a; font-size: 1.1rem;">
                                        <td colspan="3" style="text-align: right; padding: 1rem;">TOTAL A FACTURAR:</td>
                                        <td style="text-align: right; padding: 1rem;">B/. <?= number_format($preview['total'], 2) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-actions" style="margin-top: 2rem; display: flex; justify-content: space-between; align-items: center;">
                    <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/facturacion" class="btn-secondary">Cancelar</a>
                    
                    <div style="display: flex; gap: 10px;">
                        <!-- Botón de Vista Previa / Actualizar Cálculo -->
                        <button type="submit" name="accion" value="preview" class="btn-secondary" style="border: 1px solid #94a3b8; background: #fff; color: #334155;">
                            <i class="fas fa-calculator"></i> <?= isset($preview) ? 'Actualizar Cálculo' : 'Ver Vista Previa' ?>
                        </button>

                        <!-- Botón de Generar (Solo visible si hay preview y total > 0) -->
                        <?php if (isset($preview) && $preview['total'] > 0): ?>
                            <button type="submit" name="accion" value="generar" class="btn-primary" style="background: #16a34a; border: none; cursor: pointer;" onclick="return confirm('¿Está seguro de generar esta factura fiscal? Esta acción es definitiva.');">
                                <i class="fas fa-check-circle"></i> Confirmar y Generar Factura
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <script src="<?= ENV_APP['ASSETS_URL'] ?>/js/transitions.js"></script>
</body>
</html>

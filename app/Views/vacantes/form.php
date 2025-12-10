<?php
use App\Helpers\Auth;
$user = Auth::user();

// Helpers
$v = $vacante ?? [];
$valor = function(string $campo, $default = '') use ($v) {
    return $v[$campo] ?? $_POST[$campo] ?? $default;
};
$modalidadActual = $valor('modalidad', 'presencial');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $modo === 'editar' ? 'Editar Vacante' : 'Nueva Vacante' ?></title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css?v=<?= time() ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Estilos del Formulario Moderno */
        .form-container { max-width: 900px; margin: 0 auto; padding-top: 2rem; padding-bottom: 4rem; }
        
        .modern-card {
            background: white; border-radius: 20px; box-shadow: 0 10px 30px -5px rgba(0,0,0,0.08);
            padding: 3rem; border: 1px solid #f1f5f9;
        }

        .form-header { text-align: center; margin-bottom: 3rem; }
        .form-header h2 { font-size: 1.8rem; color: #1e293b; margin-bottom: 0.5rem; font-weight: 800; }
        .form-header p { color: #64748b; }

        .form-group { margin-bottom: 2rem; }
        label { display: block; margin-bottom: 0.8rem; color: #334155; font-weight: 700; font-size: 0.95rem; }
        
        input, textarea, select {
            width: 100%; padding: 1rem 1.2rem; background: #ffffff;
            border: 2px solid #cbd5e1; border-radius: 10px; color: #0f172a;
            outline: none; transition: all 0.2s; font-size: 1rem; font-weight: 500;
            box-sizing: border-box;
        }
        input:focus, textarea:focus, select:focus {
            border-color: #2563eb; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        /* --- SELECTOR DE MODALIDAD ANIMADO --- */
        .modalidad-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;
        }
        
        .modalidad-option {
            position: relative;
        }
        
        .modalidad-option input {
            position: absolute; opacity: 0; width: 0; height: 0;
        }
        
        .modalidad-card {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 1.5rem; border: 2px solid #e2e8f0; border-radius: 12px;
            cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #fff; color: #64748b; height: 100%;
        }
        
        .modalidad-card i { font-size: 2rem; margin-bottom: 0.8rem; transition: transform 0.3s; }
        .modalidad-card span { font-weight: 600; font-size: 1rem; }

        /* Estado Hover */
        .modalidad-card:hover {
            border-color: #94a3b8; transform: translateY(-2px);
        }

        /* Estado Seleccionado (Checkeado) */
        .modalidad-option input:checked + .modalidad-card {
            border-color: #2563eb; background: #eff6ff; color: #2563eb;
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.2);
            transform: translateY(-3px);
        }
        .modalidad-option input:checked + .modalidad-card i {
            transform: scale(1.1);
        }

        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }
        @media(max-width: 768px) { .row, .modalidad-grid { grid-template-columns: 1fr; } }

        .btn-submit {
            background: #2563eb; color: white; padding: 1.2rem; border: none;
            border-radius: 12px; cursor: pointer; font-weight: 700; width: 100%;
            margin-top: 2rem; font-size: 1.1rem; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.4);
            transition: transform 0.2s;
        }
        .btn-submit:hover { transform: translateY(-2px); background: #1d4ed8; }
        
        .back-link { display: inline-flex; align-items: center; gap: 8px; color: #64748b; margin-bottom: 2rem; text-decoration: none; font-weight: 600; }
        .back-link:hover { color: #2563eb; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="form-container">
        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes" class="back-link">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
        
        <div class="modern-card animate-slide-up">
            <div class="form-header">
                <h2><?= $modo === 'editar' ? 'Editar Vacante' : 'Crear Nueva Vacante' ?></h2>
                <p>Complete los detalles para atraer al mejor talento.</p>
            </div>

            <form method="post" action="">
                <?php if ($modo === 'editar'): ?>
                    <input type="hidden" name="id" value="<?= (int)($v['id'] ?? 0) ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="titulo">Título de la vacante</label>
                    <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($valor('titulo')) ?>" required placeholder="Ej: Desarrollador Full Stack">
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción del Puesto</label>
                    <textarea id="descripcion" name="descripcion" style="min-height: 150px; line-height: 1.6;" required placeholder="Responsabilidades, requisitos, beneficios..."><?= htmlspecialchars($valor('descripcion')) ?></textarea>
                </div>

                <div class="form-group">
                    <label>Modalidad de Trabajo</label>
                    <div class="modalidad-grid">
                        
                        <label class="modalidad-option">
                            <input type="radio" name="modalidad" value="presencial" <?= $modalidadActual === 'presencial' ? 'checked' : '' ?>>
                            <div class="modalidad-card">
                                <i class="fas fa-building"></i>
                                <span>Presencial</span>
                            </div>
                        </label>

                        <label class="modalidad-option">
                            <input type="radio" name="modalidad" value="hibrido" <?= $modalidadActual === 'hibrido' ? 'checked' : '' ?>>
                            <div class="modalidad-card">
                                <i class="fas fa-sync-alt"></i>
                                <span>Híbrido</span>
                            </div>
                        </label>

                        <label class="modalidad-option">
                            <input type="radio" name="modalidad" value="remoto" <?= $modalidadActual === 'remoto' ? 'checked' : '' ?>>
                            <div class="modalidad-card">
                                <i class="fas fa-laptop-house"></i>
                                <span>Remoto</span>
                            </div>
                        </label>

                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="tipo_contrato">Tipo de contrato</label>
                        <input type="text" id="tipo_contrato" name="tipo_contrato" value="<?= htmlspecialchars($valor('tipo_contrato')) ?>" required placeholder="Ej: Tiempo completo">
                    </div>
                    <div class="form-group">
                        <label for="ubicacion">Ubicación</label>
                        <input type="text" id="ubicacion" name="ubicacion" value="<?= htmlspecialchars($valor('ubicacion')) ?>" required placeholder="Ciudad, Provincia">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="cantidad_plazas">Vacantes Disponibles</label>
                        <input type="number" id="cantidad_plazas" name="cantidad_plazas" value="<?= htmlspecialchars($valor('cantidad_plazas', '1')) ?>" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="fecha_cierre">Fecha de Cierre</label>
                        <input type="date" id="fecha_cierre" name="fecha_cierre" value="<?= htmlspecialchars($valor('fecha_cierre')) ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="salario_min">Salario Mín. (B/.)</label>
                        <input type="number" step="0.01" id="salario_min" name="salario_min" value="<?= htmlspecialchars($valor('salario_min')) ?>" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label for="salario_max">Salario Máx. (B/.)</label>
                        <input type="number" step="0.01" id="salario_max" name="salario_max" value="<?= htmlspecialchars($valor('salario_max')) ?>" placeholder="0.00">
                    </div>
                </div>

                <?php if ($user['rol'] === 'admin_consultora'): ?>
                    <div style="background: #f0f9ff; padding: 1.5rem; border-radius: 12px; border: 1px solid #bae6fd; margin-bottom: 2rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                            <div>
                                <label class="form-label" style="color: #0369a1; margin-bottom: 5px;">Costo por Vista (Peaje)</label>
                                <p style="margin: 0; font-size: 0.85rem; color: #0284c7;">Tarifa que pagas cada vez que un candidato ve el detalle.</p>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="font-size: 1.2rem; font-weight: bold; color: #0284c7;">B/.</span>
                                <input type="number" id="costo" name="costo_por_vista" step="0.01" min="1.00" max="10.00" value="<?= htmlspecialchars($valor('costo_por_vista', '1.00')) ?>" required style="width: 120px; text-align: center; font-weight: 800; border-color: #bae6fd;">
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="costo_por_vista" value="<?= htmlspecialchars($valor('costo_por_vista', '1.00')) ?>">
                <?php endif; ?>

                <button type="submit" class="btn-submit">
                    <?= $modo === 'editar' ? 'Guardar Cambios' : '🚀 Publicar Vacante' ?>
                </button>
            </form>
        </div>
    </div>
</body>
</html>

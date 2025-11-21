<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - Manual</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <style>
        .layout { display: grid; grid-template-columns: 280px 1fr; gap: 2rem; max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: var(--bg-card); padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px var(--shadow-color); margin-bottom: 2rem; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem; }
        input, select { width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 8px; background: var(--bg-primary); color: var(--text-primary); }
        .btn-save { background: #667eea; color: white; border: none; padding: 1rem 2rem; border-radius: 8px; font-weight: bold; cursor: pointer; float: right; }
        
        @media(max-width: 768px) { .layout { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../components/navbar.php'; ?>

    <div class="layout">
        <aside>
            <div class="card" style="text-align: center;">
                <div style="width:100px; height:100px; background:#667eea; border-radius:50%; margin:0 auto 1rem; display:flex; align-items:center; justify-content:center; font-size:2.5rem; color:white;">
                    <?= strtoupper(substr($perfil['nombre'], 0, 1)) ?>
                </div>
                <h3><?= htmlspecialchars($perfil['nombre'] . ' ' . $perfil['apellido']) ?></h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem;"><?= htmlspecialchars($perfil['email']) ?></p>
                <hr style="margin: 1rem 0; border-color: var(--border-color);">
                <div style="text-align: left; font-size: 0.9rem;">
                    <p><strong>Nacionalidad:</strong> <?= htmlspecialchars($perfil['nacionalidad'] ?? 'Sin definir') ?></p>
                    <p><strong>Cédula:</strong> <?= htmlspecialchars($perfil['cedula'] ?? '-') ?></p>
                </div>
            </div>
        </aside>

        <main>
            <form action="<?= ENV_APP['BASE_URL'] ?>/candidato/guardar-manual" method="POST">
                
                <div class="card">
                    <h2 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Datos Personales</h2>
                    <div class="form-grid">
                        <div>
                            <label>Nombre *</label>
                            <input type="text" name="nombre" value="<?= htmlspecialchars($perfil['nombre']) ?>" required>
                        </div>
                        <div>
                            <label>Apellido *</label>
                            <input type="text" name="apellido" value="<?= htmlspecialchars($perfil['apellido']) ?>" required>
                        </div>
                        <div>
                            <label>Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($perfil['fecha_nacimiento'] ?? '') ?>">
                        </div>
                        <div>
                            <label>Género</label>
                            <select name="genero">
                                <option value="">Seleccione...</option>
                                <option value="masculino" <?= ($perfil['genero']??'')=='masculino'?'selected':'' ?>>Masculino</option>
                                <option value="femenino" <?= ($perfil['genero']??'')=='femenino'?'selected':'' ?>>Femenino</option>
                            </select>
                        </div>
                        <div>
                            <label>Estado Civil</label>
                            <select name="estado_civil">
                                <option value="soltero" <?= ($perfil['estado_civil']??'')=='soltero'?'selected':'' ?>>Soltero/a</option>
                                <option value="casado" <?= ($perfil['estado_civil']??'')=='casado'?'selected':'' ?>>Casado/a</option>
                            </select>
                        </div>
                        <div>
                            <label>Nacionalidad</label>
                            <select name="nacionalidad">
                                <option value="Panamá" selected>Panamá</option>
                                <option value="Extranjero">Extranjero</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h2 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Datos de Contacto</h2>
                    <div class="form-grid">
                        <div>
                            <label>Teléfono Celular *</label>
                            <input type="tel" name="telefono" value="<?= htmlspecialchars($perfil['telefono']) ?>" required>
                        </div>
                        <div>
                            <label>Otro Teléfono</label>
                            <input type="tel" name="telefono_secundario" value="<?= htmlspecialchars($perfil['telefono_secundario'] ?? '') ?>">
                        </div>
                        <div>
                            <label>País</label>
                            <input type="text" name="pais" value="Panamá" readonly>
                        </div>
                        <div>
                            <label>Provincia</label>
                            <select name="provincia">
                                <option value="Chiriquí">Chiriquí</option>
                                <option value="Panamá">Panamá</option>
                                <option value="Veraguas">Veraguas</option>
                                <option value="Coclé">Coclé</option>
                            </select>
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <label>Dirección Completa</label>
                            <input type="text" name="direccion" value="<?= htmlspecialchars($perfil['direccion'] ?? '') ?>" placeholder="Calle, Casa, Referencia...">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-save">Guardar Cambios</button>
            </form>
        </main>
    </div>
</body>
</html>
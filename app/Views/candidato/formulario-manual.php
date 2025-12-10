<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil Completo - Manual</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .layout { display: grid; grid-template-columns: 280px 1fr; gap: 2rem; max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: var(--bg-card); padding: 2rem; border-radius: 12px; box-shadow: 0 2px 8px var(--shadow-color); margin-bottom: 2rem; border: 1px solid var(--border-color); }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem; color: var(--text-primary); }
        input, select, textarea { width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 8px; background: var(--bg-primary); color: var(--text-primary); }
        h2 { color: var(--text-heading); margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; display: flex; align-items: center; gap: 10px; }
        
        .btn-save { background: #667eea; color: white; border: none; padding: 1rem 2rem; border-radius: 8px; font-weight: bold; cursor: pointer; float: right; transition: all 0.2s; }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4); }

        /* Dynamic Sections */
        .dynamic-item { background: var(--bg-secondary); padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; position: relative; border: 1px solid var(--border-color); }
        .btn-remove { position: absolute; top: 10px; right: 10px; color: #ef4444; background: none; border: none; cursor: pointer; font-size: 1.1rem; }
        .btn-add { background: transparent; border: 2px dashed var(--border-color); color: var(--text-secondary); width: 100%; padding: 1rem; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.2s; }
        .btn-add:hover { border-color: #667eea; color: #667eea; background: rgba(102, 126, 234, 0.05); }

        @media(max-width: 768px) { .layout { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <style>
        body { padding-top: 100px; }
        .alert-box {
            padding: 1rem 1.5rem; 
            border-radius: 12px; 
            margin-bottom: 2rem;
            display: flex; 
            align-items: center; 
            gap: 12px; 
            font-weight: 600;
            animation: slideDown 0.4s ease-out;
            max-width: 1200px;
            margin: 0 auto 2rem;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        @keyframes slideDown { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
    </style>

    <?php if(isset($_SESSION['mensaje'])): ?>
        <div class="alert-box <?= $_SESSION['mensaje']['tipo'] === 'success' ? 'alert-success' : 'alert-error' ?>">
            <i class="fas <?= $_SESSION['mensaje']['tipo'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
            <?= $_SESSION['mensaje']['texto'] ?>
        </div>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <div class="layout">
        <aside>
            <div class="card" style="text-align: center; position: sticky; top: 20px;">
                <div style="width:100px; height:100px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius:50%; margin:0 auto 1rem; display:flex; align-items:center; justify-content:center; font-size:2.5rem; color:white;">
                    <?= strtoupper(substr($perfil['nombre'], 0, 1)) ?>
                </div>
                <h3><?= htmlspecialchars($perfil['nombre'] . ' ' . $perfil['apellido']) ?></h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem;"><?= htmlspecialchars($perfil['email']) ?></p>
                <hr style="margin: 1rem 0; border-color: var(--border-color);">
                <div style="text-align: left; font-size: 0.9rem;">
                    <p><i class="fas fa-map-marker-alt" style="width:20px"></i> <?= htmlspecialchars($perfil['provincia'] ?? 'Panamá') ?></p>
                    <p><i class="fas fa-id-card" style="width:20px"></i> <?= htmlspecialchars($perfil['cedula'] ?? '-') ?></p>
                </div>
            </div>
        </aside>

        <main>
            <div style="margin-bottom: 20px;">
                <a href="<?= ENV_APP['BASE_URL'] ?>/candidato/opciones-perfil" style="text-decoration: none; color: #64748b; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-arrow-left"></i> Volver a elegir método de carga
                </a>
            </div>

            <form action="<?= ENV_APP['BASE_URL'] ?>/candidato/guardar-manual" method="POST">
                
                <!-- 1. Datos Personales -->
                <div class="card">
                    <h2><i class="fas fa-user"></i> Datos Personales</h2>
                    <div class="form-grid">
                        <div><label>Nombre *</label><input type="text" name="nombre" value="<?= htmlspecialchars($perfil['nombre']) ?>" required></div>
                        <div><label>Apellido *</label><input type="text" name="apellido" value="<?= htmlspecialchars($perfil['apellido']) ?>" required></div>
                        <div><label>Fecha Nacimiento</label><input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($perfil['fecha_nacimiento'] ?? '') ?>"></div>
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
                                <option value="">Seleccione...</option>
                                <option value="soltero" <?= ($perfil['estado_civil']??'')=='soltero'?'selected':'' ?>>Soltero/a</option>
                                <option value="casado" <?= ($perfil['estado_civil']??'')=='casado'?'selected':'' ?>>Casado/a</option>
                                <option value="unido" <?= ($perfil['estado_civil']??'')=='unido'?'selected':'' ?>>Unido/a</option>
                                <option value="divorciado" <?= ($perfil['estado_civil']??'')=='divorciado'?'selected':'' ?>>Divorciado/a</option>
                                <option value="viudo" <?= ($perfil['estado_civil']??'')=='viudo'?'selected':'' ?>>Viudo/a</option>
                            </select>
                        </div>
                        <div>
                             <label>Nacionalidad</label>
                             <select name="nacionalidad">
                                 <option value="Panamá" selected>Panamá</option>
                                 <option value="Extranjero">Extranjero</option>
                             </select>
                        </div>
                         <div><label>Teléfono *</label><input type="tel" name="telefono" value="<?= htmlspecialchars($perfil['telefono']) ?>" required></div>
                         <div style="grid-column: 1/-1"><label>Dirección</label><input type="text" name="direccion" value="<?= htmlspecialchars($perfil['direccion'] ?? '') ?>"></div>
                    </div>
                </div>

                <!-- 2. Experiencia Laboral -->
                <div class="card">
                    <h2><i class="fas fa-briefcase"></i> Experiencia Laboral</h2>
                    <div id="experience-container">
                        <!-- Items rendered by JS if existing, else empty -->
                    </div>
                    <button type="button" class="btn-add" onclick="addExperience()">+ Añadir Experiencia</button>
                    <!-- Template hidden -->
                </div>

                <!-- 3. Educación -->
                <div class="card">
                    <h2><i class="fas fa-graduation-cap"></i> Formación Académica</h2>
                    <div id="education-container"></div>
                    <button type="button" class="btn-add" onclick="addEducation()">+ Añadir Estudio</button>
                </div>

                <!-- 4. Habilidades -->
                <div class="card">
                    <h2><i class="fas fa-star"></i> Habilidades</h2>
                    <label>Escribe tus habilidades separadas por comas (Ej: Liderazgo, Excel, Inglés)</label>
                    <textarea name="habilidades" rows="3" placeholder="Ej: Trabajo en equipo, PHP, Ventas..."><?= htmlspecialchars($perfil['habilidades'] ?? '') ?></textarea>
                </div>

                <div style="overflow: hidden;">
                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> Guardar Todo</button>
                </div>

            </form>
        </main>
    </div>

    <!-- Templates for JS -->
    <template id="tpl-experience">
        <div class="dynamic-item">
            <button type="button" class="btn-remove" onclick="this.parentElement.remove()"><i class="fas fa-trash"></i></button>
            <div class="form-grid">
                <div><label>Empresa</label><input type="text" name="exp_empresa[]" placeholder="Nombre de la empresa" required></div>
                <div><label>Puesto / Cargo</label><input type="text" name="exp_puesto[]" placeholder="Ej: Vendedor" required></div>
                <div><label>Fecha Inicio</label><input type="date" name="exp_inicio[]"></div>
                <div><label>Fecha Fin</label><input type="date" name="exp_fin[]"></div>
                <div style="grid-column: 1/-1">
                    <label>Descripción de funciones</label>
                    <textarea name="exp_descripcion[]" rows="2"></textarea>
                </div>
            </div>
        </div>
    </template>

    <template id="tpl-education">
        <div class="dynamic-item">
            <button type="button" class="btn-remove" onclick="this.parentElement.remove()"><i class="fas fa-trash"></i></button>
            <div class="form-grid">
                <div><label>Institución / Universidad</label><input type="text" name="edu_institucion[]" required></div>
                <div><label>Título Obtenido</label><input type="text" name="edu_titulo[]" required></div>
                <div>
                    <label>Nivel</label>
                    <select name="edu_nivel[]">
                        <option value="Secundaria">Secundaria</option>
                        <option value="Técnico">Técnico</option>
                        <option value="Licenciatura">Licenciatura</option>
                        <option value="Postgrado">Postgrado</option>
                        <option value="Maestría">Maestría</option>
                    </select>
                </div>
                <div><label>Fecha Graduación</label><input type="date" name="edu_fecha[]"></div>
            </div>
        </div>
    </template>

    <script>
        // Init with one empty item if nothing exists, or pre-fill (advanced: passed via PHP JSON)
        // For now start empty or 1 item
        
        function addExperience() {
            const tpl = document.getElementById('tpl-experience');
            const clone = tpl.content.cloneNode(true);
            document.getElementById('experience-container').appendChild(clone);
        }

        function addEducation() {
            const tpl = document.getElementById('tpl-education');
            const clone = tpl.content.cloneNode(true);
            document.getElementById('education-container').appendChild(clone);
        }

        // Add 1 default if empty (optional)
        // addExperience(); 
        // addEducation();
        
        // Populate existing data (Simulated for now, can be injected via PHP)
        // We'll leave this empty for now to avoid JS errors if vars are undefined, 
        // next step would be pass $experiencia from Controller
        const existingExp = <?= json_encode($experiencia ?? []) ?>;
        const existingEdu = <?= json_encode($educacion ?? []) ?>;
        
        if(existingExp.length > 0) {
            existingExp.forEach(exp => {
                addExperience();
                const last = document.querySelector('#experience-container .dynamic-item:last-child');
                last.querySelector('[name="exp_empresa[]"]').value = exp.empresa;
                last.querySelector('[name="exp_puesto[]"]').value = exp.puesto;
                last.querySelector('[name="exp_inicio[]"]').value = exp.fecha_inicio;
                last.querySelector('[name="exp_fin[]"]').value = exp.fecha_fin;
                last.querySelector('[name="exp_descripcion[]"]').value = exp.descripcion;
            });
        } else {
            addExperience(); // Default empty
        }

        if(existingEdu.length > 0) {
            existingEdu.forEach(edu => {
                addEducation();
                const last = document.querySelector('#education-container .dynamic-item:last-child');
                last.querySelector('[name="edu_institucion[]"]').value = edu.institucion;
                last.querySelector('[name="edu_titulo[]"]').value = edu.titulo;
                last.querySelector('[name="edu_nivel[]"]').value = edu.nivel;
                last.querySelector('[name="edu_fecha[]"]').value = edu.fecha_graduacion;
            });
        } else {
            addEducation(); // Default empty
        }
    </script>
</body>
</html>
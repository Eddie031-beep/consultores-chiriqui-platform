<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil | Consultores Chiriquí</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/global-dark-mode.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .container { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: var(--bg-card); padding: 2rem; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 4px 6px var(--shadow-color); margin-bottom: 2rem; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-primary); }
        .form-control, select, textarea { 
            width: 100%; padding: 0.8rem; 
            border: 1px solid var(--border-color); 
            border-radius: 8px; 
            background: var(--bg-primary); 
            color: var(--text-primary); 
            transition: all 0.3s ease;
        }
        
        /* Interactive Animations */
        select:hover, input:hover, textarea:hover { border-color: #667eea; }
        select:focus, input:focus, textarea:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2); outline: none; }
        
        /* Select Animation */
        select {
            cursor: pointer;
            background-image: linear-gradient(45deg, transparent 50%, var(--text-secondary) 50%), linear-gradient(135deg, var(--text-secondary) 50%, transparent 50%);
            background-position: calc(100% - 20px) calc(1em + 2px), calc(100% - 15px) calc(1em + 2px);
            background-size: 5px 5px, 5px 5px;
            background-repeat: no-repeat;
            appearance: none;
        }

        .btn-save { background: #667eea; color: white; padding: 1rem 2rem; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; width: 100%; font-size: 1.1rem; transition: transform 0.2s, box-shadow 0.2s; }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4); background: #5a6fd6; }
        
        .current-cv { background: rgba(102, 126, 234, 0.1); padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border: 1px dashed #667eea; color: #667eea; display: flex; align-items: center; gap: 10px; }
        .section-title { border-bottom: 1px solid var(--border-color); padding-bottom: 10px; margin-bottom: 20px; color: var(--text-heading); display: flex; align-items: center; gap: 10px; }
        
        /* Dynamic Sections Styles (Borrowed from Manual Form) */
        .dynamic-item { background: var(--bg-secondary); padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; position: relative; border: 1px solid var(--border-color); animation: fadeIn 0.3s ease; }
        .btn-remove { position: absolute; top: 10px; right: 10px; color: #ef4444; background: none; border: none; cursor: pointer; font-size: 1.1rem; transition: transform 0.2s; }
        .btn-remove:hover { transform: scale(1.1); }
        .btn-add { background: transparent; border: 2px dashed var(--border-color); color: var(--text-secondary); width: 100%; padding: 1rem; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.2s; }
        .btn-add:hover { border-color: #667eea; color: #667eea; background: rgba(102, 126, 234, 0.05); }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        .hidden-input { display: none; margin-top: 0.5rem; }
        .visible-input { display: block; animation: slideDown 0.3s ease; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="container">
        <h1 style="margin-bottom: 2rem; color: var(--text-heading);">✏️ Editar Mi Perfil</h1>
        
        <form action="<?= ENV_APP['BASE_URL'] ?>/candidato/actualizar" method="POST" enctype="multipart/form-data">
            
            <!-- 1. Hoja de Vida -->
            <div class="card">
                <h3 class="section-title"><i class="fas fa-file-alt"></i> Hoja de Vida (CV)</h3>
                <?php if (!empty($perfil['cv_ruta'])): ?>
                    <div class="current-cv">
                        <span>📄 Tienes un CV cargado actualmente.</span>
                        <a href="<?= ENV_APP['BASE_URL'] ?>/<?= $perfil['cv_ruta'] ?>" target="_blank" style="text-decoration: underline; font-weight: bold;">Ver CV actual</a>
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Subir nuevo CV (PDF/Word) - <em>Deja vacío para mantener el actual</em></label>
                    <input type="file" name="cv_archivo" class="form-control" accept=".pdf,.doc,.docx">
                </div>
            </div>

            <!-- 2. Datos Personales -->
            <div class="card">
                <h3 class="section-title"><i class="fas fa-user"></i> Datos Personales</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($perfil['nombre']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Apellido</label>
                        <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($perfil['apellido']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Cédula (No editable)</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($perfil['cedula'] ?? '') ?>" disabled style="opacity: 0.7; cursor: not-allowed;">
                    </div>
                    <div class="form-group">
                        <label>Fecha Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="form-control" value="<?= htmlspecialchars($perfil['fecha_nacimiento'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Género</label>
                        <select name="genero" class="form-control">
                            <option value="">Seleccione...</option>
                            <option value="masculino" <?= ($perfil['genero']??'')=='masculino'?'selected':'' ?>>Masculino</option>
                            <option value="femenino" <?= ($perfil['genero']??'')=='femenino'?'selected':'' ?>>Femenino</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Estado Civil</label>
                        <select name="estado_civil" class="form-control">
                            <option value="">Seleccione...</option>
                            <option value="soltero" <?= ($perfil['estado_civil']??'')=='soltero'?'selected':'' ?>>Soltero/a</option>
                            <option value="casado" <?= ($perfil['estado_civil']??'')=='casado'?'selected':'' ?>>Casado/a</option>
                            <option value="unido" <?= ($perfil['estado_civil']??'')=='unido'?'selected':'' ?>>Unido/a</option>
                            <option value="divorciado" <?= ($perfil['estado_civil']??'')=='divorciado'?'selected':'' ?>>Divorciado/a</option>
                            <option value="viudo" <?= ($perfil['estado_civil']??'')=='viudo'?'selected':'' ?>>Viudo/a</option>
                        </select>
                    </div>
                     <div class="form-group">
                         <label>Nacionalidad</label>
                         <select name="nacionalidad" class="form-control">
                             <option value="Panamá" <?= ($perfil['nacionalidad']??'')=='Panamá'?'selected':'' ?>>Panamá</option>
                             <option value="Extranjero" <?= ($perfil['nacionalidad']??'')=='Extranjero'?'selected':'' ?>>Extranjero</option>
                         </select>
                     </div>
                </div>
            </div>

            <!-- 3. Información de Contacto -->
            <div class="card">
                <h3 class="section-title"><i class="fas fa-address-book"></i> Contacto</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="tel" name="telefono" class="form-control" value="<?= htmlspecialchars($perfil['telefono'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email (No editable)</label>
                        <input type="email" class="form-control" value="<?= htmlspecialchars($perfil['email']) ?>" disabled style="opacity: 0.7; cursor: not-allowed;">
                    </div>
                    
                    <!-- Provincia con opción "Otra" -->
                    <div class="form-group">
                        <label>Provincia</label>
                        <select name="provincia_select" id="provincia_select" class="form-control" onchange="toggleProvinciaInput(this)">
                            <?php 
                                $provincias = ['Chiriquí', 'Panamá', 'Veraguas', 'Bocas del Toro', 'Coclé', 'Colón', 'Darién', 'Herrera', 'Los Santos', 'Panamá Oeste'];
                                $currentProv = $perfil['provincia'] ?? 'Chiriquí';
                                $isOther = !in_array($currentProv, $provincias) && !empty($currentProv);
                            ?>
                            <?php foreach($provincias as $p): ?>
                                <option value="<?= $p ?>" <?= ($currentProv == $p)?'selected':'' ?>><?= $p ?></option>
                            <?php endforeach; ?>
                            <option value="otra" <?= $isOther ? 'selected' : '' ?>>Otra / Escribir Manualmente</option>
                        </select>
                        <input type="text" name="provincia" id="provincia_input" class="form-control <?= $isOther ? 'visible-input' : 'hidden-input' ?>" value="<?= htmlspecialchars($currentProv) ?>" placeholder="Escribe tu provincia...">
                    </div>

                    <div class="form-group">
                        <label>País</label>
                        <input type="text" name="pais" class="form-control" value="<?= htmlspecialchars($perfil['pais'] ?? 'Panamá') ?>">
                    </div>
                    <div class="form-group" style="grid-column: 1/-1;">
                        <label>Dirección / Ciudad</label>
                        <input type="text" name="ciudad" class="form-control" value="<?= htmlspecialchars($perfil['ciudad'] ?? '') ?>" placeholder="Ej: David, Bugaba...">
                    </div>
                    <div class="form-group" style="grid-column: 1/-1;">
                        <label>Dirección Detallada</label>
                        <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($perfil['direccion'] ?? '') ?>" placeholder="Calle, casa, referencia...">
                    </div>
                </div>
            </div>

            <!-- 4. Experiencia Laboral DO NOT REMOVE -->
            <div class="card">
                <h3 class="section-title"><i class="fas fa-briefcase"></i> Experiencia Laboral</h3>
                <div id="experience-container"></div>
                <button type="button" class="btn-add" onclick="addExperience()">+ Añadir Experiencia</button>
            </div>

            <!-- 5. Educación DO NOT REMOVE -->
            <div class="card">
                <h3 class="section-title"><i class="fas fa-graduation-cap"></i> Formación Académica</h3>
                <div id="education-container"></div>
                <button type="button" class="btn-add" onclick="addEducation()">+ Añadir Estudio</button>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <a href="<?= ENV_APP['BASE_URL'] ?>/candidato/dashboard" class="btn-save" style="background: var(--bg-secondary); color: var(--text-primary); border: 1px solid var(--border-color); text-align:center; text-decoration:none;">Cancelar</a>
                <button type="submit" class="btn-save">💾 Guardar Todos los Cambios</button>
            </div>
        </form>
    </div>

    <!-- Templates for JS -->
    <template id="tpl-experience">
        <div class="dynamic-item">
            <button type="button" class="btn-remove" onclick="this.parentElement.remove()"><i class="fas fa-trash"></i></button>
            <div class="form-grid">
                <div><label>Empresa</label><input type="text" name="exp_empresa[]" placeholder="Nombre de la empresa" required class="form-control"></div>
                <div><label>Puesto / Cargo</label><input type="text" name="exp_puesto[]" placeholder="Ej: Vendedor" required class="form-control"></div>
                <div><label>Fecha Inicio</label><input type="date" name="exp_inicio[]" class="form-control"></div>
                <div><label>Fecha Fin</label><input type="date" name="exp_fin[]" class="form-control"></div>
                <div style="grid-column: 1/-1">
                    <label>Descripción</label>
                    <textarea name="exp_descripcion[]" rows="2" class="form-control"></textarea>
                </div>
            </div>
        </div>
    </template>

    <template id="tpl-education">
        <div class="dynamic-item">
            <button type="button" class="btn-remove" onclick="this.parentElement.remove()"><i class="fas fa-trash"></i></button>
            <div class="form-grid">
                <div><label>Institución</label><input type="text" name="edu_institucion[]" required class="form-control"></div>
                <div><label>Título</label><input type="text" name="edu_titulo[]" required class="form-control"></div>
                <div>
                    <label>Nivel</label>
                    <select name="edu_nivel[]" class="form-control">
                        <option value="Secundaria">Secundaria</option>
                        <option value="Técnico">Técnico</option>
                        <option value="Licenciatura">Licenciatura</option>
                        <option value="Postgrado">Postgrado</option>
                        <option value="Maestría">Maestría</option>
                    </select>
                </div>
                <div><label>Graduación</label><input type="date" name="edu_fecha[]" class="form-control"></div>
            </div>
        </div>
    </template>

    <script>
        function toggleProvinciaInput(select) {
            const input = document.getElementById('provincia_input');
            if (select.value === 'otra') {
                input.style.display = 'block';
                input.value = '';
                input.focus();
            } else {
                input.style.display = 'none';
                input.value = select.value;
            }
        }

        // Initialize Province input state
        const provSelect = document.getElementById('provincia_select');
        const provInput = document.getElementById('provincia_input');
        if(provSelect.value !== 'otra') {
            provInput.value = provSelect.value;
        }

        // --- Dynamic Fields Logic ---
        function addExperience() {
            const tpl = document.getElementById('tpl-experience');
            document.getElementById('experience-container').appendChild(tpl.content.cloneNode(true));
        }

        function addEducation() {
            const tpl = document.getElementById('tpl-education');
            document.getElementById('education-container').appendChild(tpl.content.cloneNode(true));
        }

        // Populate Existing Data
        const existingExp = <?= json_encode($experiencia ?? []) ?>;
        const existingEdu = <?= json_encode($educacion ?? []) ?>;

        if (existingExp.length > 0) {
            existingExp.forEach(exp => {
                addExperience();
                const items = document.querySelectorAll('#experience-container .dynamic-item');
                const last = items[items.length - 1];
                last.querySelector('[name="exp_empresa[]"]').value = exp.empresa;
                last.querySelector('[name="exp_puesto[]"]').value = exp.puesto;
                last.querySelector('[name="exp_inicio[]"]').value = exp.fecha_inicio;
                last.querySelector('[name="exp_fin[]"]').value = exp.fecha_fin;
                last.querySelector('[name="exp_descripcion[]"]').value = exp.descripcion;
            });
        }

        if (existingEdu.length > 0) {
            existingEdu.forEach(edu => {
                addEducation();
                const items = document.querySelectorAll('#education-container .dynamic-item');
                const last = items[items.length - 1];
                last.querySelector('[name="edu_institucion[]"]').value = edu.institucion;
                last.querySelector('[name="edu_titulo[]"]').value = edu.titulo;
                last.querySelector('[name="edu_nivel[]"]').value = edu.nivel;
                last.querySelector('[name="edu_fecha[]"]').value = edu.fecha_graduacion;
            });
        }
    </script>
</body>
</html>

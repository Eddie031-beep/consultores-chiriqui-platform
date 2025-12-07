<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Empresa - Consultora</title>
    <style>
        body{font-family:system-ui;background:#020617;color:#e5e7eb;padding:2rem;}
        .container{max-width:800px;margin:0 auto;}
        h1{color:#38bdf8;margin-bottom:2rem;}
        .back-btn{display:inline-block;margin-bottom:1rem;color:#94a3b8;text-decoration:none;}
        .back-btn:hover{color:#38bdf8;}
        
        .card{background:#0f172a;padding:2rem;border-radius:12px;border:1px solid #1e293b;}
        
        .form-group{margin-bottom:1.5rem;}
        label{display:block;margin-bottom:0.5rem;color:#94a3b8;font-size:0.9rem;}
        input, select {
            width: 100%; padding: 10px; background: #1e293b; border: 1px solid #334155; 
            border-radius: 8px; color: white; outline: none; box-sizing: border-box;
        }
        input:focus, select:focus {border-color: #38bdf8;}
        
        .row {display: flex; gap: 15px;}
        .col {flex: 1;}

        .btn-submit {
            background: #2563eb; color: white; padding: 12px 24px; border: none; 
            border-radius: 8px; cursor: pointer; font-weight: 600; width: 100%;
            margin-top: 1rem;
        }
        .btn-submit:hover {background: #1d4ed8;}
        .error-msg {color: #f87171; font-size: 0.85rem; margin-top: 5px;}
    </style>
</head>
<body>
    <div class="container">
        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/empresas" class="back-btn">← Volver al Listado</a>
        <h1>✏️ Editar Empresa</h1>
        
        <div class="card">
            <form action="<?= ENV_APP['BASE_URL'] ?>/consultora/empresas/<?= $empresa['id'] ?>/editar" method="POST">
                
                <div class="row">
                    <div class="col form-group">
                        <label>Tipo de Empresa</label>
                        <select name="tipo">
                            <option value="privada" <?= $empresa['tipo'] == 'privada' ? 'selected' : '' ?>>Privada</option>
                            <option value="publica" <?= $empresa['tipo'] == 'publica' ? 'selected' : '' ?>>Pública</option>
                            <option value="ong" <?= $empresa['tipo'] == 'ong' ? 'selected' : '' ?>>ONG</option>
                        </select>
                    </div>
                    <div class="col form-group">
                        <label>Estado</label>
                        <select name="estado">
                            <option value="activa" <?= $empresa['estado'] == 'activa' ? 'selected' : '' ?>>Activa</option>
                            <option value="inactiva" <?= $empresa['estado'] == 'inactiva' ? 'selected' : '' ?>>Inactiva</option>
                            <option value="pendiente" <?= $empresa['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nombre de la Empresa</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($empresa['nombre']) ?>" required>
                    <?php if(isset($errores['nombre'])): ?><div class="error-msg"><?= $errores['nombre'] ?></div><?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Sector / Industria</label>
                    <input type="text" name="sector" value="<?= htmlspecialchars($empresa['sector'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label>Dirección Física</label>
                    <input type="text" name="direccion" value="<?= htmlspecialchars($empresa['direccion']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Provincia</label>
                    <select name="provincia">
                        <?php 
                        $provincias = ['Chiriquí', 'Panamá', 'Bocas del Toro', 'Coclé', 'Colón', 'Darién', 'Herrera', 'Los Santos', 'Veraguas', 'Panamá Oeste'];
                        foreach($provincias as $prov) {
                            $selected = ($empresa['provincia'] == $prov) ? 'selected' : '';
                            echo "<option value='$prov' $selected>$prov</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col form-group">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" value="<?= htmlspecialchars($empresa['telefono'] ?? '') ?>">
                    </div>
                    <div class="col form-group">
                        <label>Email de Contacto</label>
                        <input type="email" name="email_contacto" value="<?= htmlspecialchars($empresa['email_contacto'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Sitio Web</label>
                    <input type="url" name="sitio_web" value="<?= htmlspecialchars($empresa['sitio_web'] ?? '') ?>">
                </div>

                <button type="submit" class="btn-submit">Guardar Cambios</button>
            </form>
        </div>
    </div>
</body>
</html>

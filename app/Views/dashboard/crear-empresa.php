<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Empresa - Consultora</title>
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
        <h1>🏢 Registrar Nueva Empresa</h1>
        
        <div class="card">
            <form action="<?= ENV_APP['BASE_URL'] ?>/consultora/empresas/crear" method="POST">
                
                <div class="row">
                    <div class="col form-group">
                        <label>Tipo de Empresa</label>
                        <select name="tipo">
                            <option value="privada">Privada</option>
                            <option value="publica">Pública</option>
                            <option value="ong">ONG</option>
                        </select>
                    </div>
                    <div class="col form-group">
                        <label>Sector / Industria</label>
                        <input type="text" name="sector" placeholder="Ej. Tecnología, Salud..." required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nombre de la Empresa</label>
                    <input type="text" name="nombre" placeholder="Nombre Comercial o Razón Social" required>
                    <?php if(isset($errores['nombre'])): ?><div class="error-msg"><?= $errores['nombre'] ?></div><?php endif; ?>
                </div>

                <div class="row">
                    <div class="col form-group">
                        <label>RUC</label>
                        <input type="text" name="ruc" placeholder="1234567" required>
                        <?php if(isset($errores['ruc'])): ?><div class="error-msg"><?= $errores['ruc'] ?></div><?php endif; ?>
                    </div>
                    <div class="col form-group">
                        <label>DV</label>
                        <input type="text" name="dv" placeholder="00" required>
                        <?php if(isset($errores['dv'])): ?><div class="error-msg"><?= $errores['dv'] ?></div><?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Dirección Física</label>
                    <input type="text" name="direccion" placeholder="Calle, Edificio, Oficina..." required>
                </div>

                <div class="form-group">
                    <label>Provincia</label>
                    <select name="provincia">
                        <option value="Chiriquí">Chiriquí</option>
                        <option value="Panamá">Panamá</option>
                        <option value="Bocas del Toro">Bocas del Toro</option>
                        <option value="Coclé">Coclé</option>
                        <option value="Colón">Colón</option>
                        <option value="Darién">Darién</option>
                        <option value="Herrera">Herrera</option>
                        <option value="Los Santos">Los Santos</option>
                        <option value="Veraguas">Veraguas</option>
                        <option value="Panamá Oeste">Panamá Oeste</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col form-group">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" placeholder="Ej. 775-1234">
                    </div>
                    <div class="col form-group">
                        <label>Email de Contacto</label>
                        <input type="email" name="email_contacto" placeholder="contacto@empresa.com">
                        <?php if(isset($errores['email'])): ?><div class="error-msg"><?= $errores['email'] ?></div><?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Sitio Web (Opcional)</label>
                    <input type="url" name="sitio_web" placeholder="https://www.empresa.com">
                </div>

                <button type="submit" class="btn-submit">Registrar Empresa y Generar Contrato</button>
            </form>
        </div>
    </div>
</body>
</html>

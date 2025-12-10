<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Empresa - Consultora</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css?v=<?= time() ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* --- CORRECCIÓN DE ESPACIADO PARA NAVBAR FIJA --- */
        body {
            padding-top: 100px; /* Espacio de seguridad para la navbar */
            background-color: #f8fafc;
        }

        .container { max-width: 900px; margin: 0 auto; padding-bottom: 4rem; }
        
        .form-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            border: 1px solid #e2e8f0;
        }

        .form-header { text-align: center; margin-bottom: 3rem; }
        .form-header h1 { color: #1e293b; font-size: 1.8rem; margin-bottom: 0.5rem; font-weight: 800; }
        .form-header p { color: #64748b; font-size: 1rem; }

        /* --- ESPACIADO MEJORADO --- */
        .form-group { 
            margin-bottom: 2.5rem; /* Más separación vertical entre campos */
        }

        label { 
            display: block; 
            margin-bottom: 0.8rem; /* Separación entre label e input */
            color: #334155; 
            font-weight: 700; 
            font-size: 0.95rem; 
        }
        
        /* --- CAMPOS MÁS VISIBLES Y CLAROS --- */
        input[type="text"], input[type="email"], input[type="url"], select {
            width: 100%; 
            padding: 1rem 1.2rem; /* Relleno interno más amplio */
            background: #ffffff;
            border: 2px solid #94a3b8; /* Borde gris medio (más visible que antes) */
            border-radius: 10px;
            color: #0f172a; /* Texto negro intenso */
            outline: none; 
            transition: all 0.2s;
            font-size: 1rem;
            font-weight: 500;
            box-sizing: border-box;
        }

        input:focus, select:focus {
            border-color: #2563eb; /* Azul intenso al enfocar */
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
            background: #ffffff;
        }
        
        /* Placeholder más visible */
        ::placeholder { color: #94a3b8; opacity: 1; }

        /* Campos de solo lectura */
        input.readonly-field {
            background-color: #f1f5f9;
            color: #64748b;
            border-color: #cbd5e1;
            cursor: not-allowed;
        }

        /* Checkbox destacado */
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 1.5rem;
            background: #f0fdf4;
            border: 2px solid #86efac;
            border-radius: 12px;
            margin: 2rem 0;
        }
        .checkbox-wrapper input[type="checkbox"] {
            width: 24px; height: 24px; accent-color: #16a34a; cursor: pointer;
        }
        .checkbox-label {
            margin: 0; font-weight: 700; color: #14532d; font-size: 1rem; cursor: pointer;
        }

        .section-divider {
            margin: 4rem 0 2rem 0; /* Mucho espacio antes de nueva sección */
            padding-bottom: 0.8rem;
            border-bottom: 2px solid #e2e8f0;
            color: #2563eb;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Grid de columnas con separación amplia */
        .row { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 2.5rem; /* Separación horizontal amplia para que no choquen */
        }
        @media(max-width: 768px) { .row { grid-template-columns: 1fr; gap: 1.5rem; } }

        .btn-submit {
            background: #2563eb;
            color: white; 
            padding: 1.2rem; 
            border: none;
            border-radius: 12px; 
            cursor: pointer; 
            font-weight: 700; 
            width: 100%;
            margin-top: 2rem; 
            font-size: 1.1rem;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.4);
            transition: transform 0.2s;
        }
        .btn-submit:hover { transform: translateY(-2px); background: #1d4ed8; }
        
        .back-link { display: inline-flex; align-items: center; gap: 8px; color: #64748b; margin-bottom: 2rem; text-decoration: none; font-weight: 600; }
        .back-link:hover { color: #2563eb; }

        /* --- ESTILOS DE ALERTA --- */
        .alert-box {
            padding: 1rem 1.5rem; 
            border-radius: 12px; 
            margin-bottom: 2rem;
            display: flex; 
            align-items: center; 
            gap: 12px; 
            font-weight: 600;
            animation: slideDown 0.4s ease-out;
            box-shadow: 0 4px 6px -2px rgba(0,0,0,0.05);
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        
        @keyframes slideDown { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
    </style>
</head>
<body>
    <div class="container">
        <!-- Lógica de Alertas -->
        <?php if(isset($_SESSION['mensaje'])): ?>
            <div class="alert-box <?= $_SESSION['mensaje']['tipo'] === 'success' ? 'alert-success' : 'alert-error' ?>">
                <i class="fas <?= $_SESSION['mensaje']['tipo'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
                <div><?= $_SESSION['mensaje']['texto'] ?></div>
            </div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>

        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/empresas" class="back-link">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
        
        <div class="form-card">
            <div class="form-header">
                <h1>Editar Empresa</h1>
                <p>Información operativa y fiscal.</p>
            </div>

            <form action="<?= ENV_APP['BASE_URL'] ?>/consultora/empresas/<?= $empresa['id'] ?>/editar" method="POST">
                
                <div class="row">
                    <div class="form-group">
                        <label for="tipo">Tipo de Empresa</label>
                        <select name="tipo" id="tipo">
                            <option value="privada" <?= $empresa['tipo'] == 'privada' ? 'selected' : '' ?>>Privada</option>
                            <option value="publica" <?= $empresa['tipo'] == 'publica' ? 'selected' : '' ?>>Pública</option>
                            <option value="ong" <?= $empresa['tipo'] == 'ong' ? 'selected' : '' ?>>ONG</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select name="estado" id="estado">
                            <option value="activa" <?= $empresa['estado'] == 'activa' ? 'selected' : '' ?>>Activa</option>
                            <option value="inactiva" <?= $empresa['estado'] == 'inactiva' ? 'selected' : '' ?>>Inactiva</option>
                            <option value="pendiente" <?= $empresa['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre Comercial</label>
                    <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($empresa['nombre']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="sector">Sector / Industria</label>
                    <input type="text" name="sector" id="sector" value="<?= htmlspecialchars($empresa['sector'] ?? '') ?>" required>
                </div>

                <div class="section-divider">
                    <i class="fas fa-file-invoice-dollar"></i> Datos Fiscales / Facturación
                </div>

                <div class="form-group">
                    <label for="razon_social">Razón Social (Nombre Legal)</label>
                    <input type="text" name="razon_social" id="razon_social" value="<?= htmlspecialchars($empresa['razon_social'] ?? '') ?>" placeholder="Ej: Inversiones Chiriquí S.A.">
                </div>

                <div class="row">
                    <div class="form-group">
                        <label for="ruc">RUC</label>
                        <input type="text" name="ruc" id="ruc" value="<?= htmlspecialchars($empresa['ruc']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="dv">DV (Dígito Verificador)</label>
                        <input type="text" name="dv" id="dv" value="<?= htmlspecialchars($empresa['dv']) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Fecha de Registro (Solo lectura)</label>
                    <input type="text" class="readonly-field" value="<?= date('d/m/Y H:i', strtotime($empresa['fecha_registro'])) ?>" readonly>
                </div>

                <div class="checkbox-wrapper">
                    <input type="checkbox" name="datos_facturacion_completos" id="chk_fiscal" value="1" <?= ($empresa['datos_facturacion_completos'] == 1) ? 'checked' : '' ?>>
                    <label for="chk_fiscal" class="checkbox-label">Marcar: Datos de facturación verificados y completos</label>
                </div>

                <div class="section-divider">
                    <i class="fas fa-map-marker-alt"></i> Ubicación y Contacto
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección Física</label>
                    <input type="text" name="direccion" id="direccion" value="<?= htmlspecialchars($empresa['direccion']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="provincia">Provincia</label>
                    <select name="provincia" id="provincia">
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
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" value="<?= htmlspecialchars($empresa['telefono'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="email_contacto">Email de Contacto</label>
                        <input type="email" name="email_contacto" id="email_contacto" value="<?= htmlspecialchars($empresa['email_contacto'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="sitio_web">Sitio Web</label>
                    <input type="url" name="sitio_web" id="sitio_web" value="<?= htmlspecialchars($empresa['sitio_web'] ?? '') ?>">
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </form>
        </div>
    </div>
</body>
</html>

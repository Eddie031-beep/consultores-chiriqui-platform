<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Empresa - Consultora</title>
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/dashboard-consultora.css?v=<?= time() ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .container { max-width: 900px; margin: 0 auto; padding-top: 2rem; }
        
        .form-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            padding: 3rem;
            animation: fadeIn 0.8s ease-out;
            position: relative;
            z-index: 10;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        .form-header h1 { color: #1e293b; font-size: 1.8rem; margin-bottom: 0.5rem; }
        .form-header p { color: #64748b; }

        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; color: #475569; font-weight: 600; font-size: 0.9rem; }
        input, select {
            width: 100%; padding: 0.85rem 1rem;
            background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 8px; color: #1e293b; outline: none; transition: all 0.2s;
            font-size: 0.95rem;
        }
        input:focus, select:focus {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            background: white;
        }

        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        @media(max-width: 768px) { .row { grid-template-columns: 1fr; } }

        .btn-submit {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white; padding: 1rem 2rem; border: none;
            border-radius: 10px; cursor: pointer; font-weight: 700; width: 100%;
            margin-top: 1.5rem; font-size: 1rem;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.4);
        }

        /* Province Animation */
        #province-preview {
            margin-top: 10px;
            height: 0;
            opacity: 0;
            overflow: hidden;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e40af;
            font-weight: 600;
        }
        #province-preview.active {
            height: 100px;
            opacity: 1;
            margin-bottom: 1.5rem;
        }
        .icon-province { font-size: 2rem; margin-right: 15px; animation: bounce 2s infinite; }

        @keyframes bounce { 0%, 100% {transform: translateY(0);} 50% {transform: translateY(-5px);} }
        @keyframes fadeIn { from {opacity:0; transform:translateY(20px);} to {opacity:1; transform:translateY(0);} }

        .back-link { display: inline-flex; align-items: center; gap: 5px; color: #64748b; margin-bottom: 2rem; text-decoration: none; font-weight: 500; }
        .back-link:hover { color: var(--accent-primary); }

        /* Floating decoration */
        .decoration-circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(45deg, #e0f2fe, #f0f9ff);
            z-index: -1;
        }
    </style>
</head>
<body>
    
    <div class="decoration-circle" style="width: 300px; height: 300px; top: -50px; left: -50px;"></div>
    <div class="decoration-circle" style="width: 200px; height: 200px; bottom: 50px; right: -20px; background: #f0fdf4;"></div>

    <div class="container">
        <a href="<?= ENV_APP['BASE_URL'] ?>/consultora/empresas" class="back-link">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
        
        <div class="form-card">
            <div class="form-header">
                <h1>Registrar Nueva Empresa</h1>
                <p>Complete la información para generar el contrato de servicio.</p>
            </div>

            <form action="<?= ENV_APP['BASE_URL'] ?>/consultora/empresas/crear" method="POST">
                
                <div class="row">
                    <div class="form-group">
                        <label>Tipo de Empresa</label>
                        <select name="tipo">
                            <option value="privada">Privada</option>
                            <option value="publica">Pública</option>
                            <option value="ong">ONG</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sector / Industria</label>
                        <input type="text" name="sector" placeholder="Ej. Tecnología, Salud..." required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nombre de la Empresa</label>
                    <input type="text" name="nombre" placeholder="Nombre Comercial o Razón Social" required>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label>RUC</label>
                        <input type="text" name="ruc" placeholder="1234567" required>
                    </div>
                    <div class="form-group">
                        <label>DV</label>
                        <input type="text" name="dv" placeholder="00" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Dirección Física</label>
                    <input type="text" name="direccion" placeholder="Calle, Edificio, Oficina..." required>
                </div>

                <div class="form-group">
                    <label>Provincia</label>
                    <select name="provincia" id="province-select" onchange="showProvinceAnimation(this)">
                        <option value="">Seleccione una provincia...</option>
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

                <!-- Animated Box -->
                <div id="province-preview">
                    <i class="fas fa-map-marked-alt icon-province"></i>
                    <span id="province-text">Selecciona una ubicación</span>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" placeholder="Ej. 775-1234">
                    </div>
                    <div class="form-group">
                        <label>Email de Contacto</label>
                        <input type="email" name="email_contacto" placeholder="contacto@empresa.com">
                    </div>
                </div>

                <div class="form-group">
                    <label>Sitio Web</label>
                    <input type="url" name="sitio_web" placeholder="https://www.empresa.com">
                </div>

                <div class="form-group" style="background: #f0fdf4; padding: 1rem; border-radius: 8px; border: 1px dashed #86efac; display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" name="generar_contrato" value="1" checked style="width: 20px; height: 20px; accent-color: #16a34a;">
                    <div>
                        <strong style="color: #166534; display: block;">Generar Acuerdo de Servicio Comercial</strong>
                        <span style="font-size: 0.85rem; color: #15803d;">Se creará un contrato digital PDF versión v1.0 automáticamente.</span>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-file-contract"></i> Registrar Empresa y Generar Contrato
                </button>
            </form>
        </div>
    </div>

    <script>
        function showProvinceAnimation(select) {
            const preview = document.getElementById('province-preview');
            const text = document.getElementById('province-text');
            const val = select.value;

            if (val) {
                text.textContent = "Ubicación Regional: " + val;
                preview.classList.add('active');
                
                // Color tweaks based on province (mockup)
                if (val === 'Chiriquí') preview.style.background = 'linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%)'; // Greenish
                else if (val === 'Panamá') preview.style.background = 'linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%)'; // Blueish
                else preview.style.background = 'linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%)'; // Gray default
                
            } else {
                preview.classList.remove('active');
            }
        }
    </script>
</body>
</html>

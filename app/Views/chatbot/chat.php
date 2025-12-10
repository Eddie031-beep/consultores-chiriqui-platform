<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistente Virtual 24/7 - Consultores Chiriquí</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 10px;
        }

        .chat-container {
            width: 100%;
            max-width: 700px;
            background: #fff;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            height: 95vh;
            max-height: 850px;
            border-radius: 20px;
            overflow: hidden;
            animation: slideUp 0.4s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ============ HEADER ============ */
        .chat-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            position: relative;
        }
        
        .bot-avatar-large {
            width: 50px;
            height: 50px;
            background: white;
            color: #667eea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .chat-info {
            flex: 1;
        }

        .chat-info h3 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
            letter-spacing: -0.5px;
        }

        .chat-info p {
            margin: 5px 0 0;
            font-size: 0.85rem;
            opacity: 0.95;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: #4ade80;
            border-radius: 50%;
            display: inline-block;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        .back-home {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            font-size: 1.3rem;
            transition: transform 0.2s;
            padding: 8px;
        }

        .back-home:hover {
            color: white;
            transform: scale(1.1);
        }

        /* ============ MENSAJES ============ */
        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #f8fafc;
            background-image: 
                linear-gradient(rgba(255,255,255,0.5) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.5) 1px, transparent 1px);
            background-size: 20px 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            scroll-behavior: smooth;
        }

        /* Scrollbar personalizado */
        .chat-messages::-webkit-scrollbar {
            width: 8px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* ============ BURBUJAS ============ */
        .message {
            max-width: 85%;
            display: flex;
            flex-direction: column;
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .message.user {
            align-self: flex-end;
            align-items: flex-end;
        }

        .message.bot {
            align-self: flex-start;
            align-items: flex-start;
        }

        .bubble {
            padding: 14px 18px;
            border-radius: 20px;
            font-size: 0.95rem;
            line-height: 1.6;
            word-wrap: break-word;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s;
        }

        .bubble:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }

        /* Usuario (Gradiente Azul/Morado) */
        .message.user .bubble {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom-right-radius: 6px;
        }

        /* Bot (Blanco con borde) */
        .message.bot .bubble {
            background: white;
            color: #1e293b;
            border: 2px solid #e2e8f0;
            border-bottom-left-radius: 6px;
        }

        .timestamp {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 6px;
            padding: 0 8px;
            font-weight: 500;
        }

        /* Links dentro del chat */
        .bubble a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            border-bottom: 2px solid transparent;
            transition: border-color 0.2s;
        }

        .bubble a:hover {
            border-bottom-color: #667eea;
        }

        .message.user .bubble a {
            color: white;
            border-bottom-color: rgba(255,255,255,0.5);
        }

        .message.user .bubble a:hover {
            border-bottom-color: white;
        }

        /* ============ TYPING INDICATOR ============ */
        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 14px 18px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 20px;
            border-bottom-left-radius: 6px;
            width: fit-content;
            margin-bottom: 10px;
            animation: fadeIn 0.3s;
        }

        .dot {
            width: 10px;
            height: 10px;
            background: #94a3b8;
            border-radius: 50%;
            animation: bounce 1.4s infinite ease-in-out both;
        }

        .dot:nth-child(1) { animation-delay: -0.32s; }
        .dot:nth-child(2) { animation-delay: -0.16s; }

        @keyframes bounce {
            0%, 80%, 100% {
                transform: scale(0);
                opacity: 0.5;
            }
            40% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* ============ INPUT AREA ============ */
        .chat-input-area {
            padding: 20px;
            background: white;
            border-top: 2px solid #e2e8f0;
            display: flex;
            gap: 12px;
            align-items: flex-end;
            box-shadow: 0 -4px 12px rgba(0,0,0,0.05);
        }

        textarea {
            flex: 1;
            padding: 14px 18px;
            border: 2px solid #cbd5e1;
            border-radius: 24px;
            resize: none;
            height: 52px;
            max-height: 130px;
            font-family: inherit;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s;
            background: #f8fafc;
        }

        textarea:focus {
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-send {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-send:hover:not(:disabled) {
            transform: scale(1.08) rotate(15deg);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .btn-send:active:not(:disabled) {
            transform: scale(0.95);
        }

        .btn-send:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
            box-shadow: none;
        }

        /* ============ SUGERENCIAS RÁPIDAS ============ */
        .quick-suggestions {
            display: flex;
            gap: 8px;
            padding: 0 20px 15px;
            overflow-x: auto;
            background: white;
        }

        .quick-suggestions::-webkit-scrollbar {
            height: 4px;
        }

        .quick-suggestions::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .suggestion-btn {
            padding: 8px 16px;
            background: #f1f5f9;
            border: 2px solid #e2e8f0;
            border-radius: 20px;
            font-size: 0.85rem;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
            color: #475569;
        }

        .suggestion-btn:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
            transform: translateY(-2px);
        }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 768px) {
            body {
                padding: 0;
            }

            .chat-container {
                max-width: 100%;
                height: 100vh;
                max-height: none;
                border-radius: 0;
            }

            .chat-header {
                border-radius: 0;
            }

            .bubble {
                font-size: 0.9rem;
                padding: 12px 16px;
            }
        }

        /* ============ ANIMACIONES EXTRAS ============ */
        .shake {
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
    </style>
</head>
<body>

    <div class="chat-container">
        <!-- HEADER -->
        <div class="chat-header">
            <div class="bot-avatar-large">
                <i class="fas fa-robot"></i>
            </div>
            <div class="chat-info">
                <h3>🤖 Asistente Virtual Inteligente</h3>
                <p>
                    <span class="status-dot"></span>
                    Conectado en Tiempo Real | Base de Datos Activa
                </p>
            </div>
            <a href="<?= ENV_APP['BASE_URL'] ?>" class="back-home" title="Volver al inicio">
                <i class="fas fa-times-circle"></i>
            </a>
        </div>

        <!-- SUGERENCIAS RÁPIDAS -->
        <div class="quick-suggestions">
            <button class="suggestion-btn" onclick="enviarSugerencia('buscar empleo en tecnología')">
                💼 Buscar empleos
            </button>
            <button class="suggestion-btn" onclick="enviarSugerencia('ver estadísticas')">
                📊 Ver estadísticas
            </button>
            <button class="suggestion-btn" onclick="enviarSugerencia('cuánto debo de facturación')">
                💰 Mi facturación
            </button>
            <?php 
                // Verificar rol de consultora para mostrar botón de generar factura
                $currentUser = \App\Helpers\Auth::user();
                if ($currentUser && $currentUser['rol'] === 'admin_consultora'): 
            ?>
            <button class="suggestion-btn" onclick="enviarSugerencia('generar factura')" style="background:#dcfce7; color:#166534; border-color:#86efac;">
                📄 Generar Factura
            </button>
            <?php endif; ?>
            <button class="suggestion-btn" onclick="enviarSugerencia('dónde están ubicados')">
                📍 Ubicación
            </button>
            <button class="suggestion-btn" onclick="enviarSugerencia('cómo me registro')">
                📝 Registrarme
            </button>
        </div>

        <!-- ÁREA DE MENSAJES -->
        <div class="chat-messages" id="messagesBox">
            <div class="message bot">
                <div class="bubble">
                    👋 <strong>¡Hola! Bienvenido al Asistente Virtual de Consultores Chiriquí.</strong><br><br>
                    Estoy conectado a la <strong>base de datos en tiempo real</strong> y puedo ayudarte con:<br><br>
                    🔍 Buscar vacantes específicas<br>
                    📊 Ver estadísticas de interacciones<br>
                    💰 Consultar facturación y peajes<br>
                    📝 Información sobre registro<br>
                    📍 Ubicación de oficinas<br>
                    ❓ Resolver cualquier duda<br><br>
                    <em>Escribe tu pregunta o usa los botones de arriba 👆</em>
                </div>
                <div class="timestamp"><?= date('H:i A') ?></div>
            </div>

            <!-- Indicador de escritura (oculto por defecto) -->
            <div id="typingIndicator" class="message bot" style="display: none;">
                <div class="typing-indicator">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
            </div>
        </div>

        <!-- INPUT AREA -->
        <form id="chatForm" class="chat-input-area" onsubmit="enviarMensaje(event)">
            <textarea 
                id="userBox" 
                placeholder="Escribe tu consulta aquí... (presiona Enter para enviar)"
                rows="1" 
                required
                maxlength="500"
            ></textarea>
            <button type="submit" class="btn-send" id="sendBtn">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>

    <script>
        const form = document.getElementById('chatForm');
        const userBox = document.getElementById('userBox');
        const messagesBox = document.getElementById('messagesBox');
        const typingIndicator = document.getElementById('typingIndicator');
        const sendBtn = document.getElementById('sendBtn');

        // Auto-resize del textarea
        userBox.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 130) + 'px';
        });

        // Enviar con Enter (sin Shift)
        userBox.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                enviarMensaje(e);
            }
        });

        // FUNCIÓN PRINCIPAL: Enviar mensaje
        async function enviarMensaje(e) {
            e.preventDefault();
            const texto = userBox.value.trim();
            
            if (!texto) {
                userBox.classList.add('shake');
                setTimeout(() => userBox.classList.remove('shake'), 500);
                return;
            }

            // 1. Agregar mensaje del usuario
            agregarBurbuja(texto, 'user');
            userBox.value = '';
            userBox.style.height = '52px';
            userBox.disabled = true;
            sendBtn.disabled = true;

            // 2. Mostrar indicador de escritura
            mostrarTyping(true);
            scrollearAlFondo();

            try {
                const formData = new FormData();
                formData.append('pregunta', texto);

                const response = await fetch('<?= ENV_APP['BASE_URL'] ?>/chatbot', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                // 3. Ocultar typing y mostrar respuesta
                mostrarTyping(false);
                
                if (data.success) {
                    agregarBurbuja(data.respuesta, 'bot', data.timestamp);
                    // Renderizar botones dinámicos si existen
                    if (data.acciones && data.acciones.length > 0) {
                        renderAcciones(data.acciones);
                    }
                } else {
                    agregarBurbuja("⚠️ <strong>Error:</strong> No pude procesar tu solicitud. Intenta de nuevo.", 'bot');
                }

            } catch (error) {
                mostrarTyping(false);
                agregarBurbuja("❌ <strong>Error de conexión.</strong><br>Verifica tu internet e intenta nuevamente.", 'bot');
                console.error('Error:', error);
            }

            // 4. Re-habilitar input
            userBox.disabled = false;
            sendBtn.disabled = false;
            userBox.focus();
            scrollearAlFondo();
        }

        // FUNCIÓN: Agregar burbuja al chat
        function agregarBurbuja(htmlTexto, tipo, hora = '') {
            const div = document.createElement('div');
            div.className = `message ${tipo}`;
            
            if (!hora) {
                const now = new Date();
                hora = now.toLocaleTimeString('es-PA', { 
                    hour: '2-digit', 
                    minute: '2-digit',
                    hour12: true 
                });
            }

            div.innerHTML = `
                <div class="bubble">${htmlTexto}</div>
                <div class="timestamp">${hora}</div>
            `;

            messagesBox.insertBefore(div, typingIndicator);
            
            // Efecto de entrada
            setTimeout(() => div.style.opacity = '1', 10);
        }

        // FUNCIÓN: Renderizar botones de acción rápida
        function renderAcciones(acciones) {
            const div = document.createElement('div');
            div.className = 'message bot';
            div.style.marginTop = '-10px'; // Pegado a la burbuja anterior
            
            let htmlBotones = '<div style="display:flex; flex-wrap:wrap; gap:8px; margin-left:10px;">';
            
            acciones.forEach(acc => {
                // Escapar comillas para el onclick
                const textoAccion = acc.accion.replace(/'/g, "\\'");
                htmlBotones += `<button onclick="enviarSugerencia('${textoAccion}')" 
                                style="padding: 8px 14px; background:white; border:1px solid #667eea; color:#667eea; 
                                border-radius:15px; cursor:pointer; font-size:0.85rem; transition:all 0.2s;">
                                ${acc.texto}
                                </button>`;
            });
            
            htmlBotones += '</div>';
            div.innerHTML = htmlBotones;
            
            messagesBox.insertBefore(div, typingIndicator);
        }

        // FUNCIÓN: Mostrar/ocultar typing
        function mostrarTyping(show) {
            typingIndicator.style.display = show ? 'flex' : 'none';
            if (show) {
                messagesBox.appendChild(typingIndicator);
            }
        }

        // FUNCIÓN: Scroll suave al fondo
        function scrollearAlFondo() {
            setTimeout(() => {
                messagesBox.scrollTo({
                    top: messagesBox.scrollHeight,
                    behavior: 'smooth'
                });
            }, 100);
        }

        // FUNCIÓN: Enviar sugerencia rápida
        function enviarSugerencia(texto) {
            userBox.value = texto;
            userBox.focus();
            enviarMensaje(new Event('submit'));
        }

        // Inicializar: Focus en el input
        window.addEventListener('load', () => {
            userBox.focus();
            scrollearAlFondo();
        });
    </script>

</body>
</html>
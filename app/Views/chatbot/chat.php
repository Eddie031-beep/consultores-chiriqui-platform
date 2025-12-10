<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistente Virtual - Consultores Chiriquí</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* --- ESTILOS DEL CHAT MODERNO --- */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            display: flex;
            justify-content: center;
            height: 100vh;
        }

        .chat-container {
            width: 100%;
            max-width: 600px; /* Ancho similar a móvil/tablet */
            background: #fff;
            display: flex;
            flex-direction: column;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            height: 100%;
        }

        /* HEADER */
        .chat-header {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            z-index: 10;
        }
        
        .bot-avatar-large {
            width: 45px; height: 45px;
            background: white; color: #2563eb;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .chat-info h3 { margin: 0; font-size: 1.1rem; }
        .chat-info p { margin: 2px 0 0; font-size: 0.8rem; opacity: 0.9; display: flex; align-items: center; gap: 5px; }
        .status-dot { width: 8px; height: 8px; background: #4ade80; border-radius: 50%; display: inline-block; }

        /* AREA DE MENSAJES */
        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background-color: #f8fafc;
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 20px 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            scroll-behavior: smooth;
        }

        /* BURBUJAS */
        .message {
            max-width: 80%;
            display: flex;
            flex-direction: column;
            position: relative;
            animation: fadeIn 0.3s ease;
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
            padding: 12px 16px;
            border-radius: 18px;
            font-size: 0.95rem;
            line-height: 1.5;
            position: relative;
            word-wrap: break-word;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        /* Estilo Usuario (Azul) */
        .message.user .bubble {
            background: #2563eb;
            color: white;
            border-bottom-right-radius: 4px;
        }

        /* Estilo Bot (Blanco/Gris) */
        .message.bot .bubble {
            background: white;
            color: #1e293b;
            border: 1px solid #e2e8f0;
            border-bottom-left-radius: 4px;
        }

        .timestamp {
            font-size: 0.7rem;
            color: #94a3b8;
            margin-top: 5px;
            padding: 0 5px;
        }

        /* LINKS EN EL CHAT */
        .bubble a { color: #2563eb; text-decoration: none; font-weight: 600; }
        .message.user .bubble a { color: white; text-decoration: underline; }

        /* INPUT AREA */
        .chat-input-area {
            padding: 15px;
            background: white;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }

        textarea {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #cbd5e1;
            border-radius: 24px;
            resize: none;
            height: 48px; /* Altura inicial */
            max-height: 120px;
            font-family: inherit;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
        }
        textarea:focus { border-color: #2563eb; }

        .btn-send {
            width: 48px; height: 48px;
            background: #2563eb; color: white;
            border: none; border-radius: 50%;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            transition: transform 0.2s, background 0.2s;
        }
        .btn-send:hover { background: #1d4ed8; transform: scale(1.05); }
        .btn-send:disabled { background: #94a3b8; cursor: not-allowed; }

        /* --- ANIMACIÓN DE "ESCRIBIENDO..." --- */
        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 12px 16px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            border-bottom-left-radius: 4px;
            width: fit-content;
            margin-bottom: 10px;
            animation: fadeIn 0.3s;
        }

        .dot {
            width: 8px; height: 8px;
            background: #94a3b8;
            border-radius: 50%;
            animation: bounce 1.4s infinite ease-in-out both;
        }

        .dot:nth-child(1) { animation-delay: -0.32s; }
        .dot:nth-child(2) { animation-delay: -0.16s; }

        @keyframes bounce {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Botón volver flotante para móviles */
        .back-home {
            position: absolute; top: 15px; right: 20px;
            color: rgba(255,255,255,0.8); text-decoration: none; font-size: 1.2rem;
        }
        .back-home:hover { color: white; }

    </style>
</head>
<body>

    <div class="chat-container">
        <div class="chat-header">
            <div class="bot-avatar-large">
                <i class="fas fa-robot"></i>
            </div>
            <div class="chat-info">
                <h3>Asistente Virtual</h3>
                <p><span class="status-dot"></span> En línea | Consultores Chiriquí</p>
            </div>
            <a href="<?= ENV_APP['BASE_URL'] ?>" class="back-home" title="Salir"><i class="fas fa-times"></i></a>
        </div>

        <div class="chat-messages" id="messagesBox">
            
            <div class="message bot">
                <div class="bubble">
                    👋 ¡Hola! Soy tu asistente virtual inteligente.<br>
                    Puedo ayudarte a buscar vacantes 🔍, ver estadísticas 📊 o resolver dudas.<br>
                    <strong>¿Qué necesitas hoy?</strong>
                </div>
                <div class="timestamp"><?= date('H:i A') ?></div>
            </div>

            <div id="typingIndicator" class="message bot" style="display: none;">
                <div class="typing-indicator">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
            </div>

        </div>

        <form id="chatForm" class="chat-input-area" onsubmit="enviarMensaje(event)">
            <textarea id="userBox" placeholder="Escribe tu consulta aquí..." rows="1" required></textarea>
            <button type="submit" class="btn-send">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>

    <script>
        const form = document.getElementById('chatForm');
        const userBox = document.getElementById('userBox');
        const messagesBox = document.getElementById('messagesBox');
        const typingIndicator = document.getElementById('typingIndicator');

        // Permitir enviar con Enter (sin Shift)
        userBox.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                enviarMensaje(e);
            }
        });

        async function enviarMensaje(e) {
            e.preventDefault();
            const texto = userBox.value.trim();
            if (!texto) return;

            // 1. Agregar mensaje del USUARIO
            agregarBurbuja(texto, 'user');
            userBox.value = '';
            userBox.disabled = true; // Bloquear input mientras piensa

            // 2. Mostrar "Escribiendo..."
            mostrarTyping(true);
            scrollearAlFondo();

            try {
                // 3. Petición AJAX al servidor
                const formData = new FormData();
                formData.append('pregunta', texto);

                // Simular un pequeño delay para que se vea la animación (opcional, se ve más natural)
                // await new Promise(r => setTimeout(r, 600)); 

                const response = await fetch('<?= ENV_APP['BASE_URL'] ?>/chatbot', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                // 4. Ocultar "Escribiendo..." y mostrar respuesta del BOT
                mostrarTyping(false);
                
                if (data.success) {
                    agregarBurbuja(data.respuesta, 'bot', data.timestamp);
                } else {
                    agregarBurbuja("⚠️ Ocurrió un error al procesar tu solicitud.", 'bot');
                }

            } catch (error) {
                mostrarTyping(false);
                agregarBurbuja("❌ Error de conexión. Intenta de nuevo.", 'bot');
                console.error(error);
            }

            userBox.disabled = false;
            userBox.focus();
            scrollearAlFondo();
        }

        function agregarBurbuja(htmlTexto, tipo, hora = '') {
            const div = document.createElement('div');
            div.className = `message ${tipo}`;
            
            // Hora actual si no viene del server
            if (!hora) {
                const now = new Date();
                hora = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }

            div.innerHTML = `
                <div class="bubble">${htmlTexto}</div>
                <div class="timestamp">${hora}</div>
            `;

            // Insertar ANTES del indicador de typing (para que el typing siempre quede abajo si está visible)
            messagesBox.insertBefore(div, typingIndicator);
        }

        function mostrarTyping(show) {
            typingIndicator.style.display = show ? 'flex' : 'none';
            if(show) {
                // Mover al final
                messagesBox.appendChild(typingIndicator);
            }
        }

        function scrollearAlFondo() {
            messagesBox.scrollTop = messagesBox.scrollHeight;
        }
    </script>

</body>
</html>
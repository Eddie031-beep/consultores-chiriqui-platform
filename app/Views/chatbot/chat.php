<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistente Virtual - Consultores Chiriquí</title>
    <!-- Modern Font & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --bg-chat: #f8fafc;
            --bg-user: #2563eb;
            --bg-bot: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body { 
            font-family: 'Inter', sans-serif;
            background: #f1f5f9; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            height: 100vh; 
            margin: 0; 
        }

        .chat-wrapper {
            width: 100%;
            max-width: 900px;
            height: 85vh;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            display: flex;
        }

        /* Sidebar Info */
        .chat-sidebar {
            width: 300px;
            background: #f8fafc;
            border-right: 1px solid #e2e8f0;
            padding: 2rem;
            display: flex;
            flex-direction: column;
        }

        @media (max-width: 768px) {
            .chat-sidebar { display: none; }
        }

        .bot-profile {
            text-align: center;
            margin-bottom: 2rem;
        }
        .bot-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem auto;
            color: white;
            font-size: 2rem;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
        }

        .suggestions {
            flex: 1;
        }
        .suggestion-btn {
            display: block;
            width: 100%;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            text-align: left;
            color: var(--text-main);
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .suggestion-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: #eff6ff;
        }

        /* Main Chat Area */
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #ffffff;
            position: relative;
        }

        .chat-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
        }

        .chat-messages {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .message {
            max-width: 75%;
            display: flex;
            gap: 1rem;
            animation: fadeIn 0.3s ease-out;
        }

        .message.bot {
            align-self: flex-start;
        }
        .message.user {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .msg-bubble {
            padding: 1rem 1.25rem;
            border-radius: 12px;
            font-size: 0.95rem;
            line-height: 1.5;
            position: relative;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .bot .msg-bubble {
            background: white;
            color: var(--text-main);
            border-bottom-left-radius: 2px;
            border: 1px solid #e2e8f0;
        }

        .user .msg-bubble {
            background: var(--primary);
            color: white;
            border-bottom-right-radius: 2px;
        }

        .msg-time {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 5px;
            display: block;
        }

        .chat-input-area {
            padding: 1.5rem;
            background: white;
            border-top: 1px solid #e2e8f0;
        }

        .input-wrapper {
            display: flex;
            gap: 1rem;
            background: #f1f5f9;
            padding: 0.5rem;
            border-radius: 12px;
            border: 1px solid transparent;
            transition: all 0.2s;
        }
        .input-wrapper:focus-within {
            background: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .chat-input {
            flex: 1;
            border: none;
            background: transparent;
            padding: 0.75rem;
            outline: none;
            font-size: 0.95rem;
            color: var(--text-main);
        }

        .send-btn {
            background: var(--primary);
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        .send-btn:hover { background: var(--primary-dark); }

        .back-link {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            text-decoration: none;
            color: #64748b;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
        }
        .back-link:hover { color: var(--primary); }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="chat-wrapper">
    <!-- Sidebar -->
    <div class="chat-sidebar">
        <div class="bot-profile">
            <div class="bot-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <h2 style="font-size: 1.25rem; color: #1e293b; margin: 0;">Asistente Virtual</h2>
            <p style="color: #64748b; font-size: 0.9rem; margin-top: 5px;">Consultores Chiriquí</p>
        </div>

        <div class="suggestions">
            <h4 style="color: #64748b; font-size: 0.85rem; text-transform: uppercase; margin-bottom: 1rem;">Sugerencias</h4>
            <button class="suggestion-btn" onclick="sendSuggestion('¿Qué vacantes hay disponibles?')">
                👔 Ver vacantes disponibles
            </button>
            <button class="suggestion-btn" onclick="sendSuggestion('¿Cómo me registro?')">
                📝 ¿Cómo registrarme?
            </button>
            <button class="suggestion-btn" onclick="sendSuggestion('Ubicación de la empresa')">
                📍 ¿Dónde están ubicados?
            </button>
        </div>
        
        <div style="font-size: 0.8rem; color: #94a3b8; text-align: center;">
            v2.0 • Powered by PHP AI
        </div>
    </div>

    <!-- Main Chat -->
    <div class="chat-main">
        <div class="chat-header">
            <div>
                <strong style="display: block; color: #1e293b;">Chat Soporte</strong>
                <span style="font-size: 0.85rem; color: #22c55e;">● En línea</span>
            </div>
            <a href="<?= ENV_APP['BASE_URL'] ?>/" class="back-link">
                <i class="fas fa-sign-out-alt"></i> Salir
            </a>
        </div>

        <div class="chat-messages" id="chatBody">
            <!-- Bot Welcome -->
            <div class="message bot">
                <div class="msg-bubble">
                    👋 <strong>¡Hola!</strong> Soy el asistente virtual inteligente.<br>
                    Puedo buscar vacantes en tiempo real o responder dudas sobre la plataforma.
                    <span class="msg-time"><?= date('H:i') ?></span>
                </div>
            </div>
        </div>

        <div class="chat-input-area">
            <form id="chatForm" class="input-wrapper">
                <input type="text" id="userMsg" class="chat-input" placeholder="Escribe tu mensaje aquí..." autocomplete="off">
                <button type="submit" class="send-btn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const form = document.getElementById('chatForm');
    const input = document.getElementById('userMsg');
    const body = document.getElementById('chatBody');

    // Function to handle clicks on suggestion buttons
    function sendSuggestion(text) {
        input.value = text;
        form.dispatchEvent(new Event('submit'));
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const msg = input.value.trim();
        if(!msg) return;

        // Add User Message
        addMessage(msg, 'user');
        input.value = '';

        // Add Loading
        const loadingId = addMessage('...', 'bot', true);

        try {
            const formData = new FormData();
            formData.append('pregunta', msg);

            const response = await fetch('<?= ENV_APP['BASE_URL'] ?>/chatbot', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            // Remove loading
            document.getElementById(loadingId).remove();

            if(data.success) {
                addMessage(data.respuesta, 'bot');
            } else {
                addMessage("Lo siento, tuve un error de conexión.", 'bot');
            }
        } catch (error) {
            if(document.getElementById(loadingId)) document.getElementById(loadingId).remove();
            addMessage("Error al conectar con el servidor.", 'bot');
        }
    });

    function addMessage(text, type, loading = false) {
        const div = document.createElement('div');
        div.className = `message ${type}`;
        if(loading) div.id = 'loadingMsg';
        
        let content = text;
        if(loading) {
            content = '<i class="fas fa-circle-notch fa-spin"></i> Procesando...';
        }

        div.innerHTML = `
            <div class="msg-bubble">
                ${content}
                ${!loading ? `<span class="msg-time">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>` : ''}
            </div>
        `;
        
        body.appendChild(div);
        body.scrollTop = body.scrollHeight;
        return div.id;
    }
</script>

</body>
</html>

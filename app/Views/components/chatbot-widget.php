<style>
    /* 🌟 VARIABLES & ANIMATIONS */
    :root {
        --chat-primary: #4f46e5;
        --chat-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        --chat-bg: #f8fafc;
        --chat-white: #ffffff;
    }

    @keyframes fadeInScale {
        from { opacity: 0; transform: scale(0.9) translateY(20px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(20px); }
        to { opacity: 1; transform: translateX(0); }
    }

    @keyframes pulseSoft {
        0% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4); }
        70% { box-shadow: 0 0 0 15px rgba(79, 70, 229, 0); }
        100% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0); }
    }

    /* 🔘 BOTÓN FLOTANTE (FAB) */
    .chat-fab {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 64px;
        height: 64px;
        background: var(--chat-gradient);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        cursor: pointer;
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.4);
        z-index: 9999;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        animation: pulseSoft 3s infinite;
    }
    .chat-fab:hover { transform: scale(1.1) rotate(10deg); box-shadow: 0 15px 30px rgba(79, 70, 229, 0.5); }

    /* 🪟 VENTANA DEL CHAT */
    .chat-window {
        position: fixed;
        bottom: 110px;
        right: 30px;
        width: 380px;
        height: 550px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15), 0 0 0 1px rgba(0,0,0,0.05);
        z-index: 9999;
        display: none;
        flex-direction: column;
        overflow: hidden;
        animation: fadeInScale 0.3s ease-out forwards;
        font-family: 'Segoe UI', system-ui, sans-serif;
    }

    /* HEADER */
    .chat-header {
        background: var(--chat-gradient);
        padding: 20px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .chat-header-info h4 { margin: 0; font-size: 1.1rem; font-weight: 700; }
    .chat-header-info span { font-size: 0.8rem; opacity: 0.9; display: flex; align-items: center; gap: 5px; }
    .status-dot { width: 8px; height: 8px; background: #22c55e; border-radius: 50%; border: 1px solid white; }
    
    .chat-close {
        background: rgba(255,255,255,0.2);
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }
    .chat-close:hover { background: rgba(255,255,255,0.4); }

    /* MENSAJES */
    .chat-messages {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        background: var(--chat-bg);
        display: flex;
        flex-direction: column;
        gap: 15px;
        scroll-behavior: smooth;
    }

    .msg {
        max-width: 85%;
        padding: 12px 16px;
        border-radius: 16px;
        font-size: 0.95rem;
        line-height: 1.5;
        position: relative;
        animation: slideInRight 0.3s ease-out;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .msg-bot {
        background: white;
        color: #1e293b;
        align-self: flex-start;
        border-bottom-left-radius: 4px;
    }

    .msg-user {
        background: var(--chat-gradient);
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 4px;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
    }

    /* INPUT */
    .chat-input-area {
        padding: 15px;
        background: white;
        border-top: 1px solid #f1f5f9;
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .chat-input-area input {
        flex: 1;
        padding: 12px 15px;
        border: 2px solid #f1f5f9;
        border-radius: 25px;
        font-size: 0.95rem;
        transition: all 0.2s;
        background: #f8fafc;
    }

    .chat-input-area input:focus {
        border-color: var(--chat-primary);
        background: white;
        outline: none;
    }

    .send-btn {
        background: var(--chat-primary);
        color: white;
        border: none;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .send-btn:hover { transform: scale(1.1); box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); }

    /* BOTONES DE ACCIÓN */
    .action-btn {
        background: white;
        border: 1px solid #e2e8f0;
        color: #475569;
        padding: 8px 14px;
        border-radius: 20px;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 500;
        box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    }
    .action-btn:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        transform: translateY(-2px);
        color: var(--chat-primary);
    }

    /* Scrollbar */
    .chat-messages::-webkit-scrollbar { width: 6px; }
    .chat-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .chat-messages::-webkit-scrollbar-track { background: transparent; }
</style>

<!-- FAB -->
<div class="chat-fab" onclick="toggleChat()">
    <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
    </svg>
</div>

<!-- WINDOW -->
<div class="chat-window" id="chatWindow">
    <div class="chat-header">
        <div class="chat-header-info">
            <h4>Asistente IA</h4>
            <span><div class="status-dot"></div> En línea ahora</span>
        </div>
        <button class="chat-close" onclick="toggleChat()">✕</button>
    </div>
    
    <div class="chat-messages" id="chatMessages">
        <div class="msg msg-bot">
            👋 <strong>¡Hola!</strong> Soy tu asistente virtual inteligente.<br>
            ¿En qué puedo ayudarte hoy?
        </div>
    </div>

    <div class="chat-input-area">
        <input type="text" id="chatInput" placeholder="Escribe tu mensaje..." onkeypress="handleEnter(event)">
        <button class="send-btn" onclick="sendMessage()">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
        </button>
    </div>
</div>

<script>
    function toggleChat() {
        const w = document.getElementById('chatWindow');
        const isHidden = (w.style.display === 'none' || w.style.display === '');
        
        w.style.display = isHidden ? 'flex' : 'none';
        
        if (isHidden) {
            setTimeout(() => document.getElementById('chatInput').focus(), 100);
        }
    }

    function handleEnter(e) { if(e.key === 'Enter') sendMessage(); }

    function sendMessage() {
        const input = document.getElementById('chatInput');
        const text = input.value.trim();
        if(!text) return;

        // Mostrar mensaje usuario
        addMessage(text, 'user');
        input.value = '';
        input.disabled = true;

        const baseUrl = '<?= ENV_APP['BASE_URL'] ?>'; 
        const formData = new FormData();
        formData.append('pregunta', text);

        fetch(baseUrl + '/chatbot', {
            method: 'POST',
            body: formData
        })
        .then(res => res.text()) // Primero obtener texto
        .then(text => {
            try {
                return JSON.parse(text); // Intentar parsear a JSON
            } catch (e) {
                // Si falla, es un error PHP (Fatal Error, Warning, etc)
                throw new Error("Respuesta del servidor no válida:<br>" + text.substring(0, 200) + "...");
            }
        })
        .then(data => {
            input.disabled = false;
            input.focus();
            if(data.success) {
                addMessage(data.respuesta, 'bot');
                if(data.acciones && data.acciones.length > 0) {
                    renderAcciones(data.acciones);
                }
            } else {
                addMessage("⚠️ Hubo un error de conexión.", 'bot');
            }
        })
        .catch(err => {
            console.error(err);
            input.disabled = false;
            // Mostrar el error real en la burbuja
            addMessage('❌ ' + err.message, 'bot');
        });
    }

    function addMessage(htmlText, type) {
        const div = document.createElement('div');
        div.className = `msg msg-${type}`;
        div.innerHTML = htmlText; 
        const container = document.getElementById('chatMessages');
        container.appendChild(div);
        
        // Scroll suave al último mensaje
        setTimeout(() => {
            container.scrollTop = container.scrollHeight;
        }, 50);
    }


    function renderAcciones(acciones) {
        const container = document.getElementById('chatMessages');
        const actionsDiv = document.createElement('div');
        actionsDiv.style.display = 'flex';
        actionsDiv.style.gap = '8px';
        actionsDiv.style.flexWrap = 'wrap';
        actionsDiv.style.marginBottom = '10px';
        actionsDiv.style.marginLeft = '10px';
        actionsDiv.style.animation = 'slideInRight 0.3s ease-out';

        acciones.forEach(accion => {
            const btn = document.createElement('button');
            btn.textContent = accion.texto;
            btn.className = 'action-btn'; // Clase CSS definida arriba
            
            btn.onclick = () => {
                const input = document.getElementById('chatInput');
                input.value = accion.accion;
                sendMessage();
                actionsDiv.remove();
            };
            actionsDiv.appendChild(btn);
        });

        container.appendChild(actionsDiv);
        container.scrollTop = container.scrollHeight;
    }
</script>

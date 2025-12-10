<style>
    /* Botón Flotante (FAB) */
    .chat-fab {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        background: #2563eb; /* Azul Corporativo */
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
        z-index: 9999;
        transition: transform 0.3s;
    }
    .chat-fab:hover { transform: scale(1.1); }

    /* Ventana del Chat (Oculta por defecto) */
    .chat-window {
        position: fixed;
        bottom: 100px;
        right: 30px;
        width: 350px;
        height: 450px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        z-index: 9999;
        display: none; /* Oculto */
        flex-direction: column;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    
    .chat-header {
        background: #0f172a; /* Negro/Azul */
        color: white;
        padding: 15px;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .chat-messages {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        background: #f8fafc;
        font-size: 0.9rem;
    }

    .chat-input-area {
        padding: 10px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        gap: 5px;
        background: white;
    }
    
    .chat-input-area input {
        flex: 1;
        padding: 8px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        outline: none;
    }
    
    .msg { margin-bottom: 10px; max-width: 80%; padding: 8px 12px; border-radius: 10px; }
    .msg-bot { background: #e2e8f0; color: #1e293b; align-self: flex-start; border-bottom-left-radius: 0; }
    .msg-user { background: #2563eb; color: white; align-self: flex-end; margin-left: auto; border-bottom-right-radius: 0; }
</style>

<div class="chat-fab" onclick="toggleChat()">💬</div>

<div class="chat-window" id="chatWindow">
    <div class="chat-header">
        <span>Asistente IA</span>
        <span style="cursor:pointer" onclick="toggleChat()">✕</span>
    </div>
    <div class="chat-messages" id="chatMessages">
        <div class="msg msg-bot">Hola, soy tu asistente virtual. ¿Buscas vacantes o información de empresas?</div>
    </div>
    <div class="chat-input-area">
        <input type="text" id="chatInput" placeholder="Escribe aquí..." onkeypress="handleEnter(event)">
        <button onclick="sendMessage()" style="background:#2563eb; color:white; border:none; padding:5px 15px; border-radius:4px;">→</button>
    </div>
</div>

    <script>
    function toggleChat() {
        const w = document.getElementById('chatWindow');
        w.style.display = (w.style.display === 'none' || w.style.display === '') ? 'flex' : 'none';
        if(w.style.display === 'flex') setTimeout(() => document.getElementById('chatInput').focus(), 100);
    }

    function handleEnter(e) { if(e.key === 'Enter') sendMessage(); }

    function sendMessage() {
        const input = document.getElementById('chatInput');
        const text = input.value.trim();
        if(!text) return;

        // 1. Mostrar mensaje usuario
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
        .then(res => res.json())
        .then(data => {
            input.disabled = false;
            input.focus();
            if(data.success) {
                addMessage(data.respuesta, 'bot');
                if(data.acciones && data.acciones.length > 0) {
                    renderAcciones(data.acciones);
                }
            } else {
                addMessage("⚠️ Hubo un error.", 'bot');
            }
        })
        .catch(err => {
            console.error(err);
            input.disabled = false;
            addMessage('❌ Error de conexión.', 'bot');
        });
    }

    function addMessage(htmlText, type) {
        const div = document.createElement('div');
        div.className = `msg msg-${type}`;
        div.innerHTML = htmlText; // Usar innerHTML para soportar <br> y <b>
        const container = document.getElementById('chatMessages');
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }

    function renderAcciones(acciones) {
        const container = document.getElementById('chatMessages');
        const actionsDiv = document.createElement('div');
        actionsDiv.style.display = 'flex';
        actionsDiv.style.gap = '5px';
        actionsDiv.style.flexWrap = 'wrap';
        actionsDiv.style.marginBottom = '10px';
        actionsDiv.style.marginLeft = '10px';

        acciones.forEach(accion => {
            const btn = document.createElement('button');
            btn.textContent = accion;
            btn.style.padding = '5px 10px';
            btn.style.border = '1px solid #2563eb';
            btn.style.borderRadius = '15px';
            btn.style.background = 'white';
            btn.style.color = '#2563eb';
            btn.style.cursor = 'pointer';
            btn.style.fontSize = '0.8rem';
            
            btn.onclick = () => {
                const input = document.getElementById('chatInput');
                input.value = accion;
                sendMessage();
                actionsDiv.remove(); // Quitar botones al elegir
            };
            actionsDiv.appendChild(btn);
        });

        container.appendChild(actionsDiv);
        container.scrollTop = container.scrollHeight;
    }
</script>

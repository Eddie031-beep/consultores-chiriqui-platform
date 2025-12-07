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
    }

    function handleEnter(e) { if(e.key === 'Enter') sendMessage(); }

    function sendMessage() {
        const input = document.getElementById('chatInput');
        const text = input.value.trim();
        if(!text) return;

        // 1. Mostrar mensaje usuario
        addMessage(text, 'user');
        input.value = '';

        // 2. Llamar a tu ChatbotController existente
        // Note: ENV_APP constant might need to be checked if it exists, otherwise fallback or dynamic URL
        // Assuming the user knows their environment, but I will check if ENV_APP is standard or if I should use base_url()
        const baseUrl = '<?= ENV_APP['BASE_URL'] ?>'; 
        
        fetch(baseUrl + '/chatbot', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'pregunta=' + encodeURIComponent(text)
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                addMessage(data.respuesta, 'bot');
            }
        })
        .catch(err => {
            console.error(err);
            addMessage('Error de conexión...', 'bot');
        });
    }

    function addMessage(text, type) {
        const div = document.createElement('div');
        div.className = `msg msg-${type}`;
        div.textContent = text;
        const container = document.getElementById('chatMessages');
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }
</script>

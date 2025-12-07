<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistente Virtual - Consultores Chiriquí</title>
    <!-- CSS Limpio y Elegante -->
    <link rel="stylesheet" href="<?= ENV_APP['ASSETS_URL'] ?>/css/home-elegant.css">
    <style>
        body { background: #f8fafc; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .chat-container { width: 100%; max-width: 500px; height: 80vh; background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); overflow: hidden; display: flex; flex-direction: column; }
        .chat-header { background: #4f46e5; padding: 20px; color: white; display: flex; align-items: center; gap: 15px; }
        .chat-header h1 { margin: 0; font-size: 1.2rem; font-weight: 600; }
        .chat-body { flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 15px; background: #f8fafc; }
        .message { padding: 12px 16px; border-radius: 12px; max-width: 80%; line-height: 1.5; font-size: 0.95rem; }
        .bot { background: white; border: 1px solid #e2e8f0; align-self: flex-start; border-top-left-radius: 2px; color: #334155; }
        .user { background: #4f46e5; color: white; align-self: flex-end; border-top-right-radius: 2px; }
        .chat-footer { padding: 20px; background: white; border-top: 1px solid #e2e8f0; }
        .input-group { display: flex; gap: 10px; }
        .chat-input { flex: 1; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border 0.2s; }
        .chat-input:focus { border-color: #4f46e5; }
        .send-btn { background: #4f46e5; color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: background 0.2s; }
        .send-btn:hover { background: #4338ca; }
        .back-home { position: absolute; top: 20px; left: 20px; text-decoration: none; color: #64748b; font-weight: 600; display: flex; align-items: center; gap: 5px; }
        .back-home:hover { color: #4f46e5; }
    </style>
</head>
<body>

<a href="<?= ENV_APP['BASE_URL'] ?>/" class="back-home">← Volver al Inicio</a>

<div class="chat-container">
    <div class="chat-header">
        <div style="font-size: 2rem;">🤖</div>
        <div>
            <h1>Asistente Virtual</h1>
            <span style="font-size: 0.85rem; opacity: 0.9;">En línea • Responde al instante</span>
        </div>
    </div>
    
    <div class="chat-body" id="chatBody">
        <div class="message bot">
            ¡Hola! 👋 Soy el asistente virtual de Consultores Chiriquí. <br><br>
            Puedo ayudarte a buscar <strong>vacantes</strong>, explicarte cómo <strong>postularte</strong> o darte información sobre <strong>nosotros</strong>. <br><br>
            ¿Qué necesitas hoy?
        </div>
    </div>
    
    <div class="chat-footer">
        <form id="chatForm" class="input-group">
            <input type="text" id="userMsg" class="chat-input" placeholder="Escribe tu consulta aquí..." autocomplete="off">
            <button type="submit" class="send-btn">Enviar</button>
        </form>
    </div>
</div>

<script>
    const form = document.getElementById('chatForm');
    const input = document.getElementById('userMsg');
    const body = document.getElementById('chatBody');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const msg = input.value.trim();
        if(!msg) return;

        // Add User Message
        addMessage(msg, 'user');
        input.value = '';

        // Simulate typing
        const loadingId = addMessage('Escribiendo...', 'bot', true);

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
            document.getElementById(loadingId).remove();
            addMessage("Error al conectar con el servidor.", 'bot');
        }
    });

    function addMessage(text, type, loading = false) {
        const div = document.createElement('div');
        div.className = `message ${type}`;
        if(loading) div.id = 'loadingMsg';
        div.innerHTML = text;
        body.appendChild(div);
        body.scrollTop = body.scrollHeight;
        return div.id;
    }
</script>

</body>
</html>

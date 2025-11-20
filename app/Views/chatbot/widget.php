<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Chatbot - Consultores Chiriquí</title>
    <style>
        * {margin:0;padding:0;box-sizing:border-box;}
        body {font-family:system-ui;background:#0f172a;color:#e5e7eb;}
        .container {max-width:900px;margin:0 auto;padding:2rem;}
        .header {text-align:center;margin-bottom:2rem;}
        .header h1 {font-size:2rem;margin-bottom:.5rem;color:#38bdf8;}
        .chatbox {background:#020617;border-radius:1rem;padding:1.5rem;border:1px solid #1e293b;margin-bottom:2rem;box-shadow:0 25px 50px rgba(0,0,0,.5);}
        .messages {height:400px;overflow-y:auto;margin-bottom:1rem;padding:.5rem;}
        .message {margin-bottom:1rem;display:flex;gap:.75rem;}
        .message.user {flex-direction:row-reverse;}
        .message .avatar {width:36px;height:36px;border-radius:50%;background:#334155;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:.9rem;}
        .message.bot .avatar {background:#3b82f6;}
        .message .bubble {background:#1e293b;padding:.75rem 1rem;border-radius:.75rem;max-width:70%;font-size:.9rem;line-height:1.5;}
        .message.user .bubble {background:#16a34a;color:#fff;}
        .vacantes-list {margin-top:.75rem;display:flex;flex-direction:column;gap:.5rem;}
        .vacante-card {background:#0f172a;padding:.75rem;border-radius:.5rem;border:1px solid #334155;font-size:.85rem;cursor:pointer;transition:all .2s;}
        .vacante-card:hover {border-color:#38bdf8;background:#1e293b;}
        .vacante-title {font-weight:600;color:#60a5fa;margin-bottom:.25rem;}
        .vacante-meta {color:#9ca3af;font-size:.8rem;}
        .input-area {display:flex;gap:.75rem;}
        .input-area input {flex:1;padding:.75rem;border-radius:.5rem;border:1px solid #334155;background:#0f172a;color:#e5e7eb;font-size:.9rem;}
        .input-area button {padding:.75rem 1.5rem;border-radius:.5rem;border:none;background:#22c55e;color:#022c22;font-weight:600;cursor:pointer;font-size:.9rem;}
        .input-area button:hover {background:#16a34a;}
        .vacantes-grid {display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1rem;margin-top:2rem;}
        .vacante-card-public {background:#020617;padding:1.25rem;border-radius:.75rem;border:1px solid #1e293b;transition:all .2s;}
        .vacante-card-public:hover {border-color:#38bdf8;transform:translateY(-2px);}
        .badge {padding:.2rem .5rem;border-radius:999px;font-size:.7rem;display:inline-block;}
        .badge-modalidad {background:#3b82f633;color:#60a5fa;}
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>💼 Asistente de Empleos</h1>
        <p style="color:#9ca3af;">Pregúntame sobre vacantes disponibles en Panamá y Chiriquí</p>
    </div>

    <div class="chatbox">
        <div class="messages" id="messages">
            <div class="message bot">
                <div class="avatar">🤖</div>
                <div class="bubble">
                    ¡Hola! Soy tu asistente de búsqueda de empleo. ¿En qué puedo ayudarte hoy? Puedes preguntarme sobre vacantes disponibles, ubicaciones, tipos de trabajo y más.
                </div>
            </div>
        </div>
        <div class="input-area">
            <input type="text" id="userInput" placeholder="Escribe tu mensaje..." autocomplete="off">
            <button onclick="enviarMensaje()">Enviar</button>
        </div>
    </div>

    <h2 style="margin-bottom:1rem;color:#cbd5e1;">Vacantes destacadas</h2>
    <div class="vacantes-grid">
        <?php foreach (array_slice($vacantes, 0, 9) as $v): ?>
            <div class="vacante-card-public" onclick="verVacante(<?= $v['id'] ?>)">
                <div class="vacante-title"><?= htmlspecialchars($v['titulo']) ?></div>
                <div style="color:#9ca3af;font-size:.85rem;margin:.5rem 0;">
                    <?= htmlspecialchars($v['empresa_nombre']) ?>
                </div>
                <div style="margin-top:.5rem;">
                    <span class="badge badge-modalidad"><?= htmlspecialchars($v['modalidad']) ?></span>
                </div>
                <div class="vacante-meta" style="margin-top:.5rem;">
                    📍 <?= htmlspecialchars($v['ubicacion']) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
const BASE_URL = '<?= ENV_APP['BASE_URL'] ?>';
const sessionId = '<?= session_id() ?>';
const messagesDiv = document.getElementById('messages');
const inputField = document.getElementById('userInput');

inputField.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') enviarMensaje();
});

function enviarMensaje() {
    const mensaje = inputField.value.trim();
    if (!mensaje) return;

    agregarMensaje('user', mensaje);
    inputField.value = '';

    fetch(BASE_URL + '/chatbot/api', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            session_id: sessionId,
            mensaje: mensaje
        })
    })
    .then(r => r.json())
    .then(data => {
        agregarMensaje('bot', data.respuesta, data.vacantes || []);
    })
    .catch(() => {
        agregarMensaje('bot', 'Lo siento, hubo un error. Intenta de nuevo.');
    });
}

function agregarMensaje(tipo, texto, vacantes = []) {
    const div = document.createElement('div');
    div.className = `message ${tipo}`;
    
    const avatar = document.createElement('div');
    avatar.className = 'avatar';
    avatar.textContent = tipo === 'bot' ? '🤖' : '👤';
    
    const bubble = document.createElement('div');
    bubble.className = 'bubble';
    bubble.textContent = texto;
    
    if (vacantes.length > 0) {
        const list = document.createElement('div');
        list.className = 'vacantes-list';
        
        vacantes.forEach(v => {
            const card = document.createElement('div');
            card.className = 'vacante-card';
            card.innerHTML = `
                <div class="vacante-title">${v.titulo}</div>
                <div class="vacante-meta">
                    ${v.empresa} • ${v.ubicacion} • ${v.modalidad}
                </div>
            `;
            card.onclick = () => verVacante(v.id);
            list.appendChild(card);
        });
        
        bubble.appendChild(list);
    }
    
    div.appendChild(avatar);
    div.appendChild(bubble);
    messagesDiv.appendChild(div);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

function verVacante(id) {
    // Registrar interacción
    fetch(BASE_URL + '/chatbot/interaccion', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            vacante_id: id,
            tipo: 'ver_detalle',
            session_id: sessionId
        })
    });
    
    alert('Esta funcionalidad mostraría el detalle completo de la vacante #' + id);
}
</script>
</body>
</html>
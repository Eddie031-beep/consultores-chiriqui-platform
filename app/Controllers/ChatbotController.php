<?php
namespace App\Controllers;

use App\Core\Controller;
use PDO;

class ChatbotController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db_connect('local');
    }

    public function widget(): void
    {
        // Vista pública del chatbot
        $sql = "SELECT v.*, e.nombre AS empresa_nombre 
                FROM vacantes v 
                JOIN empresas e ON e.id = v.empresa_id 
                WHERE v.estado = 'abierta' 
                ORDER BY v.fecha_publicacion DESC 
                LIMIT 20";
        $stmt = $this->db->query($sql);
        $vacantes = $stmt->fetchAll();

        $this->view('chatbot/widget', compact('vacantes'));
    }

    public function api(): void
    {
        header('Content-Type: application/json');

        $sessionId = $_POST['session_id'] ?? session_id();
        $mensaje = trim($_POST['mensaje'] ?? '');

        if ($mensaje === '') {
            echo json_encode(['error' => 'Mensaje vacío']);
            return;
        }

        // Buscar vacantes relacionadas
        $palabrasClave = explode(' ', strtolower($mensaje));
        $vacantes = [];

        $sql = "SELECT v.*, e.nombre AS empresa_nombre 
                FROM vacantes v 
                JOIN empresas e ON e.id = v.empresa_id 
                WHERE v.estado = 'abierta' 
                AND (LOWER(v.titulo) LIKE :q1 OR LOWER(v.descripcion) LIKE :q2 OR LOWER(v.ubicacion) LIKE :q3)
                LIMIT 5";

        foreach ($palabrasClave as $palabra) {
            if (strlen($palabra) > 3) {
                $stmt = $this->db->prepare($sql);
                $q = "%{$palabra}%";
                $stmt->execute(['q1' => $q, 'q2' => $q, 'q3' => $q]);
                $vacantes = array_merge($vacantes, $stmt->fetchAll());
                if (!empty($vacantes)) break;
            }
        }

        // Generar respuesta
        $respuesta = $this->generarRespuesta($mensaje, $vacantes);

        // Guardar en log
        $vacanteId = !empty($vacantes) ? (int)$vacantes[0]['id'] : null;
        $sqlLog = "INSERT INTO chatbot_logs (session_id, pregunta, respuesta, vacante_id) 
                   VALUES (:sid, :pregunta, :respuesta, :vid)";
        $stmtLog = $this->db->prepare($sqlLog);
        $stmtLog->execute([
            'sid' => $sessionId,
            'pregunta' => $mensaje,
            'respuesta' => $respuesta,
            'vid' => $vacanteId,
        ]);

        // Registrar interacción si hay vacante relacionada
        if ($vacanteId) {
            $sqlInt = "INSERT INTO interacciones_vacante 
                       (vacante_id, tipo_interaccion, origen, session_id, ip, user_agent) 
                       VALUES (:vid, 'chat_consulta', 'chatbot', :sid, :ip, :ua)";
            $stmtInt = $this->db->prepare($sqlInt);
            $stmtInt->execute([
                'vid' => $vacanteId,
                'sid' => $sessionId,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                'ua' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            ]);
        }

        echo json_encode([
            'respuesta' => $respuesta,
            'vacantes' => array_map(function($v) {
                return [
                    'id' => $v['id'],
                    'titulo' => $v['titulo'],
                    'empresa' => $v['empresa_nombre'],
                    'ubicacion' => $v['ubicacion'],
                    'modalidad' => $v['modalidad'],
                ];
            }, array_slice($vacantes, 0, 3)),
        ]);
    }

    private function generarRespuesta(string $mensaje, array $vacantes): string
    {
        $msg = strtolower($mensaje);

        // Saludos
        if (preg_match('/\b(hola|buenos|buenas|saludos|hey)\b/', $msg)) {
            return '¡Hola! 👋 Soy el asistente de Consultores Chiriquí. ¿En qué puedo ayudarte? Puedo mostrarte vacantes disponibles, darte información sobre empresas o responder preguntas sobre empleos.';
        }

        // Ayuda
        if (preg_match('/\b(ayuda|ayudar|como|funciona)\b/', $msg)) {
            return 'Puedo ayudarte con: 📋 Ver vacantes disponibles, 🔍 Buscar empleos por ubicación o tipo, 💼 Información sobre empresas, 📝 Requisitos de las vacantes. ¿Qué te gustaría saber?';
        }

        // Búsqueda de vacantes
        if (!empty($vacantes)) {
            $count = count($vacantes);
            return "Encontré {$count} vacante(s) que podrían interesarte. Te muestro las principales abajo. ¿Quieres más información sobre alguna?";
        }

        // Sin resultados
        if (preg_match('/\b(trabajo|empleo|vacante|puesto)\b/', $msg)) {
            return 'No encontré vacantes específicas con esos términos, pero tenemos varias oportunidades disponibles. ¿Te gustaría que te muestre todas las vacantes activas o prefieres buscar por ubicación?';
        }

        // Respuesta por defecto
        return 'Interesante pregunta. Para ayudarte mejor, ¿podrías especificar qué tipo de empleo buscas o en qué ubicación? También puedo mostrarte todas las vacantes disponibles.';
    }

    public function registrarInteraccion(): void
    {
        header('Content-Type: application/json');

        $vacanteId = isset($_POST['vacante_id']) ? (int)$_POST['vacante_id'] : 0;
        $tipo = $_POST['tipo'] ?? 'ver_detalle';
        $sessionId = $_POST['session_id'] ?? session_id();

        if ($vacanteId <= 0) {
            echo json_encode(['error' => 'Vacante inválida']);
            return;
        }

        $sql = "INSERT INTO interacciones_vacante 
                (vacante_id, tipo_interaccion, origen, session_id, ip, user_agent) 
                VALUES (:vid, :tipo, 'web', :sid, :ip, :ua)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'vid' => $vacanteId,
            'tipo' => $tipo,
            'sid' => $sessionId,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            'ua' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ]);

        echo json_encode(['ok' => true]);
    }
}
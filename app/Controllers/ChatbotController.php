<?php
namespace App\Controllers;

use App\Core\Controller;
use PDO;

class ChatbotController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        // Conexión a base de datos (según tu configuración actual)
        $this->db = db_connect('local');
    }

    public function chat(): void
    {
        // Si recibimos datos por POST, procesamos la respuesta
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarChat();
            return;
        }

        // Si es GET, cargamos la vista (útil si acceden directo a /chatbot)
        $this->view('chatbot/chat');
    }

    private function procesarChat(): void
    {
        header('Content-Type: application/json');

        $pregunta = trim($_POST['pregunta'] ?? '');

        // Validación básica
        if (empty($pregunta)) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Por favor escribe algo para comenzar.'
            ]);
            exit;
        }

        // 1. Generar respuesta con la nueva "Inteligencia Lógica"
        $respuesta = $this->generarRespuesta($pregunta);

        // 2. Guardar la interacción en la Base de Datos (Requisito del PDF para facturación)
        try {
            // Asegurar sesión para el ID
            if (session_status() === PHP_SESSION_NONE) session_start();
            $session_id = session_id();

            $stmt = $this->db->prepare("
                INSERT INTO chatbot_logs (session_id, pregunta, respuesta)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$session_id, $pregunta, $respuesta]);
        } catch (\Exception $e) {
            // Silenciamos error de log para no interrumpir la experiencia del usuario
            error_log("Error Log Chatbot: " . $e->getMessage());
        }

        // 3. Responder al frontend
        echo json_encode([
            'success' => true,
            'respuesta' => $respuesta,
            'timestamp' => date('H:i A')
        ]);
        exit;
    }

    /**
     * CEREBRO DEL CHATBOT
     * Implementa lógica difusa, detección de longitud y contexto.
     */
    private function generarRespuesta(string $pregunta): string
    {
        $preguntaOriginal = trim($pregunta);
        $pregunta = mb_strtolower($preguntaOriginal, 'UTF-8');
        $longitud = mb_strlen($pregunta);

        // 1. Control de input corto
        if ($longitud <= 2) return "¿Podrías ser más específico?";

        // 2. Saludos
        $saludos = ['hola', 'buenos', 'buenas', 'que tal'];
        foreach ($saludos as $saludo) {
            if (strpos($pregunta, $saludo) === 0) return "¡Hola! Soy tu asistente de empleo. ¿En qué te ayudo?";
        }

        // 3. BÚSQUEDA DINÁMICA EN BASE DE DATOS (La Magia ✨)
        // Si el usuario pregunta por vacantes, consultamos la BD real
        $clavesVacantes = ['vacante', 'trabajo', 'empleo', 'puesto', 'oportunidad', 'oferta'];
        foreach ($clavesVacantes as $clave) {
            if (strpos($pregunta, $clave) !== false) {
                try {
                    // Consultamos las últimas 3 vacantes abiertas
                    $stmt = $this->db->prepare("
                        SELECT v.titulo, e.nombre as empresa, v.ubicacion 
                        FROM vacantes v 
                        JOIN empresas e ON v.empresa_id = e.id 
                        WHERE v.estado = 'abierta' 
                        ORDER BY v.fecha_publicacion DESC LIMIT 3
                    ");
                    $stmt->execute();
                    $vacantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (empty($vacantes)) {
                        return "Actualmente no tengo vacantes registradas, pero revisa pronto.";
                    }

                    $texto = "¡Sí! He encontrado estas oportunidades recientes:\n";
                    foreach($vacantes as $v) {
                        $texto .= "🔹 " . $v['titulo'] . " en " . $v['empresa'] . " (" . $v['ubicacion'] . ")\n";
                    }
                    $texto .= "¿Te interesa alguna?";
                    return nl2br($texto); // Convertir saltos de línea para web

                } catch (\Exception $e) {
                    return "Tuve un error consultando las vacantes. Intenta más tarde.";
                }
            }
        }

        // 4. Respuestas estáticas para otros temas
        if (strpos($pregunta, 'registro') !== false) return 'Puedes registrarte haciendo clic en el botón "Acceder" arriba a la derecha.';
        if (strpos($pregunta, 'ubicacion') !== false) return 'Estamos en Plaza Las Lomas, David, Chiriquí.';

        return "No entendí bien. Intenta preguntar por 'vacantes disponibles' o 'ubicación'.";
    }
}
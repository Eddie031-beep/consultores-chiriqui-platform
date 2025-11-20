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

    public function chat(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarChat();
            return;
        }

        $this->view('chatbot/chat');
    }

    private function procesarChat(): void
    {
        header('Content-Type: application/json');

        $pregunta = trim($_POST['pregunta'] ?? '');

        if (empty($pregunta)) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Por favor ingresa una pregunta'
            ]);
            exit;
        }

        // Respuesta automática del chatbot
        $respuesta = $this->generarRespuesta($pregunta);

        // Guardar en BD
        $session_id = session_id();
        $stmt = $this->db->prepare("
            INSERT INTO chatbot_logs (session_id, pregunta, respuesta)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$session_id, $pregunta, $respuesta]);

        echo json_encode([
            'success' => true,
            'respuesta' => $respuesta,
            'timestamp' => date('H:i:s')
        ]);
        exit;
    }

    private function generarRespuesta($pregunta): string
    {
        $pregunta = strtolower($pregunta);

        // Palabras clave y respuestas
        $respuestas = [
            // Vacantes
            ['palabra' => 'vacante', 'respuesta' => '¿Buscas empleo? Tenemos muchas vacantes disponibles. ¿En qué sector te interesa trabajar?'],
            ['palabra' => 'trabajo', 'respuesta' => '¿Qué tipo de trabajo buscas? Puedo ayudarte a encontrar la vacante perfecta.'],
            ['palabra' => 'empleo', 'respuesta' => 'Estamos aquí para ayudarte a encontrar tu próximo empleo. ¿Qué área te interesa?'],
            
            // Postulación
            ['palabra' => 'postular', 'respuesta' => 'Para postularte, necesitas crear una cuenta. ¿Ya tienes cuenta registrada?'],
            ['palabra' => 'aplicar', 'respuesta' => 'Puedes aplicar a cualquier vacante desde nuestra plataforma. Solo necesitas estar registrado.'],
            
            // Ubicación
            ['palabra' => 'david', 'respuesta' => 'Tenemos varias vacantes en David, Chiriquí. ¿Qué tipo de trabajo buscas allá?'],
            ['palabra' => 'panamá', 'respuesta' => 'En la provincia de Panamá tenemos muchas oportunidades. ¿Qué sector te interesa?'],
            ['palabra' => 'ubicacion', 'respuesta' => 'Tenemos vacantes en diferentes ubicaciones. ¿Dónde te gustaría trabajar?'],
            
            // Modalidad
            ['palabra' => 'remoto', 'respuesta' => 'Tenemos varias posiciones remotas disponibles. ¿Qué area profesional te interesa?'],
            ['palabra' => 'presencial', 'respuesta' => 'Contamos con vacantes presenciales. ¿Cuál es tu área de expertise?'],
            
            // Salario
            ['palabra' => 'salario', 'respuesta' => 'Los salarios varían según el puesto y experiencia. ¿Cuál es tu rango salarial esperado?'],
            
            // Requisitos
            ['palabra' => 'requisito', 'respuesta' => 'Cada vacante tiene requisitos específicos. ¿Cuál vacante te interesa? Puedo ayudarte a revisar los detalles.'],
            ['palabra' => 'experiencia', 'respuesta' => 'La experiencia requerida varía por puesto. ¿Cuántos años de experiencia tienes?'],
            
            // Contacto
            ['palabra' => 'contacto', 'respuesta' => 'Puedes contactarnos a través de nuestro sitio web o llamar a nuestro número de atención al cliente.'],
            ['palabra' => 'telefono', 'respuesta' => 'Nuestro número de contacto está disponible en la página principal.'],
            
            // Empresa
            ['palabra' => 'empresa', 'respuesta' => '¿Buscas información sobre una empresa específica? Dime su nombre.'],
        ];

        // Buscar palabra clave en la pregunta
        foreach ($respuestas as $item) {
            if (strpos($pregunta, $item['palabra']) !== false) {
                return $item['respuesta'];
            }
        }

        // Respuesta por defecto
        return '¡Gracias por tu pregunta! Puedo ayudarte con información sobre vacantes, postulaciones, ubicaciones y más. ¿Hay algo específico que quieras saber?';
    }
}
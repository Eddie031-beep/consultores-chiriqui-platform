<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Auth;
use PDO;

class ChatbotController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        // Conecta a tu base de datos configurada en config/env.php
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
        
        $inputRaw = $_POST['pregunta'] ?? '';
        
        // 1. Normalización (Limpiar texto para que el bot entienda mejor)
        $input = $this->normalizarTexto($inputRaw);
        $user = Auth::user(); // Identificar quién escribe

        if (empty($input)) {
            echo json_encode(['success' => false, 'respuesta' => '🤔 No escribiste nada.']);
            exit;
        }

        // 2. CEREBRO: Detectar intención y consultar BD
        $respuesta = $this->cerebroBot($input, $inputRaw, $user);

        // 3. Guardar en Historial (Log)
        $this->logChat($inputRaw, $respuesta);

        echo json_encode([
            'success' => true,
            'respuesta' => $respuesta,
            'timestamp' => date('H:i A')
        ]);
        exit;
    }

    /**
     * LÓGICA PRINCIPAL DEL CHATBOT
     */
    private function cerebroBot(string $input, string $original, ?array $user): string
    {
        // Diccionario de sinónimos para detectar intención
        $intenciones = [
            'saludo'       => ['hola', 'buenas', 'que tal', 'hi', 'hello', 'alo', 'dia', 'tarde', 'noche'],
            'vacantes'     => ['vacante', 'trabajo', 'empleo', 'puesto', 'chamba', 'oportunidad', 'oferta', 'busco', 'buscar', 'disponible'],
            'estadisticas' => ['estadistica', 'reporte', 'metrica', 'grafica', 'rendimiento', 'vistas', 'interaccion'],
            'facturacion'  => ['factura', 'cobro', 'pago', 'debo', 'dinero', 'cuenta'],
            'registro'     => ['registro', 'registrarme', 'crear cuenta', 'nuevo usuario', 'sign up'],
            'login'        => ['login', 'entrar', 'acceder', 'iniciar sesion', 'loguear'],
            'ubicacion'    => ['ubicacion', 'donde', 'direccion', 'lugar', 'oficina', 'mapa', 'calle']
        ];

        $intencionDetectada = null;
        $palabrasUsuario = explode(' ', $input);

        // Algoritmo de coincidencia difusa (tolera errores ortográficos leves)
        foreach ($intenciones as $clave => $sinonimos) {
            foreach ($sinonimos as $sinonimo) {
                foreach ($palabrasUsuario as $palabra) {
                    if ($palabra === $sinonimo || levenshtein($palabra, $sinonimo) < 2) {
                        $intencionDetectada = $clave;
                        break 3;
                    }
                }
            }
        }

        // RESPUESTAS DINÁMICAS (Conectadas a la BD)
        switch ($intencionDetectada) {
            case 'saludo':
                $nombre = $user ? explode(' ', $user['nombre'])[0] : 'visitante';
                return "👋 ¡Hola $nombre! Soy tu asistente conectado en tiempo real.<br>¿Quieres ver <strong>vacantes</strong>, <strong>estadísticas</strong> o saber nuestra <strong>ubicación</strong>?";

            case 'vacantes':
                // Extraer palabra clave de búsqueda (ej: "busco programador" -> "programador")
                $stopwords = ['busco', 'necesito', 'quiero', 'trabajo', 'de', 'en', 'el', 'la', 'vacantes', 'puesto', 'empleo'];
                $terminos = array_diff($palabrasUsuario, $stopwords);
                $filtro = implode(' ', $terminos); // Lo que queda es lo que buscamos
                
                return $this->buscarVacantesEnBD($filtro); // <--- CONSULTA REAL A MYSQL

            case 'estadisticas':
                if (!$user || !in_array($user['rol'], ['empresa_admin', 'admin_consultora'])) {
                    return "🔒 <strong>Acceso Restringido:</strong><br>Inicia sesión como Empresa para ver cuántas personas han visto tus vacantes.";
                }
                return $this->obtenerEstadisticasBD($user); // <--- CONSULTA REAL

            case 'facturacion':
                if (!$user || $user['rol'] !== 'admin_consultora') {
                    return "🚫 La información de facturación es exclusiva para la administración.";
                }
                return $this->calcularFacturacionBD(); // <--- CÁLCULO REAL

            case 'registro':
                return "📝 <strong>Para registrarte:</strong><br>Haz clic en el botón 'Acceder' arriba a la derecha y elige 'Crear Cuenta'. ¡Es gratis!";

            case 'login':
                return "🔑 <strong>Entrar al sistema:</strong><br>Pulsa 'Acceder' en el menú superior para ingresar con tu email y contraseña.";

            case 'ubicacion':
                return "📍 <strong>Oficinas Centrales:</strong><br>Plaza Las Lomas, David, Chiriquí.<br>Horario: Lunes a Viernes, 8am - 5pm.";

            default:
                // Si no entiende, intenta buscar la frase completa como vacante (Plan B)
                if (strlen($input) > 3) {
                    $res = $this->buscarVacantesEnBD($input);
                    if (strpos($res, 'No encontré') === false) return $res;
                }
                return "🤔 No estoy seguro de qué necesitas. Intenta escribir: 'buscar empleo', 'ver estadísticas' o 'dónde están'.";
        }
    }

    // --- FUNCIONES DE BASE DE DATOS (REAL TIME) ---

    private function buscarVacantesEnBD(string $filtro): string
    {
        $filtro = trim($filtro);
        try {
            // Consulta SQL Real
            $sql = "SELECT v.id, v.titulo, v.slug, e.nombre as empresa, v.ubicacion 
                    FROM vacantes v 
                    JOIN empresas e ON v.empresa_id = e.id 
                    WHERE v.estado = 'abierta'";
            
            $params = [];
            // Si el usuario escribió algo específico (ej: "programador")
            if (!empty($filtro)) {
                $sql .= " AND (v.titulo LIKE ? OR v.descripcion LIKE ? OR e.nombre LIKE ?)";
                $term = "%$filtro%";
                $params = [$term, $term, $term];
            }
            $sql .= " ORDER BY v.fecha_publicacion DESC LIMIT 3";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultados)) {
                return "📭 No encontré vacantes activas que coincidan con '<strong>$filtro</strong>' en este momento.";
            }

            $html = "🔍 <strong>Encontré estas oportunidades:</strong><br><br>";
            foreach ($resultados as $v) {
                // Link directo a la vacante
                $url = ENV_APP['BASE_URL'] . "/vacantes/" . $v['slug'];
                $html .= "🔹 <a href='$url' target='_blank' style='color:#2563eb; font-weight:bold;'>{$v['titulo']}</a><br>";
                $html .= "<small>🏢 {$v['empresa']} | 📍 {$v['ubicacion']}</small><br><br>";
                
                // IMPORTANTE: Registrar "Peaje" (Cobro por interacción de Chat)
                $this->registrarPeaje($v['id'], 'chat_consulta');
            }
            $html .= "<em>(He registrado esta consulta para ayudarte mejor).</em>";
            return $html;

        } catch (\Exception $e) {
            return "Tuve un problema conectando con la base de datos.";
        }
    }

    private function obtenerEstadisticasBD($user): string
    {
        try {
            $empresaId = $user['empresa_id'] ?? 0;
            // Consultora ve todo, Empresa solo lo suyo
            $condicion = ($user['rol'] === 'admin_consultora') ? "1=1" : "v.empresa_id = $empresaId";

            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN tipo_interaccion = 'ver_detalle' THEN 1 ELSE 0 END) as vistas,
                        SUM(CASE WHEN tipo_interaccion = 'click_aplicar' THEN 1 ELSE 0 END) as aplicaciones,
                        SUM(CASE WHEN tipo_interaccion = 'chat_consulta' THEN 1 ELSE 0 END) as chats
                    FROM interacciones_vacante iv
                    JOIN vacantes v ON iv.vacante_id = v.id
                    WHERE $condicion";

            $stats = $this->db->query($sql)->fetch(PDO::FETCH_ASSOC);

            // Evitar nulos
            $total = $stats['total'] ?? 0;
            $vistas = $stats['vistas'] ?? 0;
            $apps = $stats['aplicaciones'] ?? 0;
            $chats = $stats['chats'] ?? 0;

            return "📊 <strong>Métricas en tiempo real:</strong><br>" .
                   "Total Interacciones: <strong>$total</strong><br>" .
                   "-----------------------------<br>" .
                   "👁️ Vistas de Perfil: $vistas<br>" .
                   "👆 Postulaciones: $apps<br>" .
                   "🤖 Consultas Chat: $chats";

        } catch (\Exception $e) {
            return "Error al consultar las estadísticas.";
        }
    }

    private function calcularFacturacionBD(): string
    {
        try {
            // Sumar facturas pendientes
            $sql = "SELECT COUNT(*) as cantidad, SUM(total) as deuda 
                    FROM facturas WHERE estado = 'emitida'";
            $data = $this->db->query($sql)->fetch(PDO::FETCH_ASSOC);
            
            $deuda = number_format($data['deuda'] ?? 0, 2);
            $cant = $data['cantidad'];

            return "💰 <strong>Resumen Financiero Global:</strong><br>" .
                   "Facturas por Cobrar: <strong>$cant</strong><br>" .
                   "Monto Pendiente: <strong>B/. $deuda</strong><br><br>" .
                   "Puedes ver el detalle en el panel de Facturación.";
        } catch (\Exception $e) {
            return "Error calculando facturación.";
        }
    }

    private function registrarPeaje($vacante_id, $tipo): void
    {
        // Inserta en la tabla que se usa luego para cobrar
        try {
            $ip = $_SERVER['REMOTE_ADDR'];
            $sid = session_id();
            $stmt = $this->db->prepare("INSERT INTO interacciones_vacante (vacante_id, tipo_interaccion, origen, ip, session_id) VALUES (?, ?, 'chatbot', ?, ?)");
            $stmt->execute([$vacante_id, $tipo, $ip, $sid]);
        } catch (\Exception $e) {}
    }

    private function logChat($pregunta, $respuesta): void
    {
        // Guarda la conversación
        try {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $stmt = $this->db->prepare("INSERT INTO chatbot_logs (session_id, pregunta, respuesta) VALUES (?, ?, ?)");
            $stmt->execute([session_id(), $pregunta, $respuesta]);
        } catch (\Exception $e) {}
    }

    private function normalizarTexto(string $texto): string
    {
        $texto = mb_strtolower($texto, 'UTF-8');
        // Quitar tildes para facilitar búsqueda
        $texto = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ'],
            ['a', 'e', 'i', 'o', 'u', 'n'],
            $texto
        );
        // Solo dejar letras y números
        return preg_replace('/[^a-z0-9 ]/', '', $texto);
    }
}
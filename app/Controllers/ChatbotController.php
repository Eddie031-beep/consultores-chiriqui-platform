<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Auth;
use PDO;

class ChatbotController extends Controller
{
    private PDO $db;
    private array $acciones = []; // New property for quick actions

    public function __construct()
    {
        $this->db = db_connect('local');
        if (session_status() === PHP_SESSION_NONE) session_start();
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
        $input = $this->normalizarTexto($inputRaw);
        $user = Auth::user();

        if (empty($input)) {
            echo json_encode(['success' => false, 'respuesta' => '🤔 No escribiste nada.']);
            exit;
        }

        // CEREBRO: Detectar intención y consultar BD EN TIEMPO REAL
        $respuestaHtml = $this->cerebroBot($input, $inputRaw, $user);

        // Guardar en Historial (intentamos guardar, si falla no rompe el flujo)
        $this->logChat($inputRaw, $respuestaHtml);

        echo json_encode([
            'success' => true,
            'respuesta' => $respuestaHtml, // Enviamos HTML directamente
            'acciones' => $this->acciones, // Enviamos botones dinámicos
            'timestamp' => date('H:i A')
        ]);
        exit;
    }

    /**
     * 🧠 CEREBRO DEL CHATBOT
     */
    private function cerebroBot(string $input, string $original, ?array $user): string
    {
        $intenciones = [
            'generar_factura' => ['generar factura', 'crear factura', 'facturar a', 'emitir factura', 'cobrar a', 'nueva factura'],
            'saludo'          => ['hola', 'buenas', 'que tal', 'hi', 'hello', 'alo', 'dia', 'tarde', 'noche', 'saludos'],
            'vacantes'        => ['vacante', 'trabajo', 'empleo', 'puesto', 'chamba', 'oportunidad', 'oferta', 'busco', 'buscar', 'disponible', 'plaza'],
            'estadisticas'    => ['estadistica', 'reporte', 'metrica', 'grafica', 'rendimiento', 'vistas', 'interaccion', 'datos', 'numeros', 'cuantas', 'cuantos'],
            'facturacion'     => ['factura', 'cobro', 'pago', 'debo', 'dinero', 'cuenta', 'precio', 'costo', 'tarifa', 'peaje'],
            'registro'        => ['registro', 'registrarme', 'crear cuenta', 'nuevo usuario', 'sign up', 'inscribir'],
            'login'           => ['login', 'entrar', 'acceder', 'iniciar sesion', 'loguear', 'ingresar'],
            'ubicacion'       => ['ubicacion', 'donde', 'direccion', 'lugar', 'oficina', 'mapa', 'calle', 'encuentro'],
            'ayuda'           => ['ayuda', 'guia', 'tutorial', 'como', 'explicar', 'manual', 'instrucciones'],
            'empresas'        => ['empresa', 'organizacion', 'compania', 'corporacion', 'cliente'],
            'postulaciones'   => ['postular', 'aplicar', 'solicitar', 'candidato', 'curriculum', 'cv'],
            'despedida'       => ['adios', 'chao', 'bye', 'hasta luego', 'nos vemos', 'gracias']
        ];

        $intencionDetectada = null;
        // 1. Búsqueda exacta de frases (Prioridad a frases largas)
        foreach ($intenciones as $clave => $sinonimos) {
            foreach ($sinonimos as $sinonimo) {
                // Verificar si la frase completa del sinónimo está en el input
                if (strpos($input, $sinonimo) !== false) {
                    $intencionDetectada = $clave;
                    break 2;
                }
            }
        }

        // 2. Si no hay coincidencia exacta de frase, búsqueda por palabras clave (Fuzzy)
        if (!$intencionDetectada) {
            $palabrasUsuario = explode(' ', $input);
            foreach ($intenciones as $clave => $sinonimos) {
                foreach ($sinonimos as $sinonimo) {
                    // Solo aplicar lógica de palabras sueltas si el sinónimo es una sola palabra
                    if (str_contains($sinonimo, ' ')) continue;

                    foreach ($palabrasUsuario as $palabra) {
                        if ($palabra === $sinonimo || levenshtein($palabra, $sinonimo) < 2) {
                            $intencionDetectada = $clave;
                            break 3;
                        }
                    }
                }
            }
        }

        // 🔥 RESPUESTAS DINÁMICAS CONECTADAS EN TIEMPO REAL (devuelven HTML)
        switch ($intencionDetectada) {
            
            case 'saludo':
                $nombre = ($user && isset($user['nombre'])) ? explode(' ', $user['nombre'])[0] : 'visitante';
                
                // Botones básicos
                $this->acciones = [
                    ['texto' => '🔍 Buscar empleos', 'accion' => 'buscar empleos'],
                    ['texto' => '📊 Ver estadísticas', 'accion' => 'ver estadisticas'],
                ];

                // Si es consultor, agregar botón de facturación
                if ($user && $user['rol'] === 'admin_consultora') {
                    array_push($this->acciones, ['texto' => '📄 Generar Factura', 'accion' => 'generar factura']);
                    array_push($this->acciones, ['texto' => '💰 Ver Facturación Global', 'accion' => 'facturacion']);
                }

                return "👋 ¡Hola $nombre! Soy tu asistente virtual conectado en <strong>tiempo real</strong>.<br><br>" .
                       "Puedo ayudarte con:<br>" .
                       "🔍 <strong>Buscar vacantes</strong> (ej: 'buscar programador')<br>" .
                       "📊 <strong>Ver estadísticas</strong> de interacciones<br>" .
                       "💰 <strong>Consultar facturación</strong> y peajes<br>" .
                       "📍 <strong>Ubicación</strong> de nuestras oficinas<br>" .
                       "❓ <strong>Ayuda</strong> general<br><br>" .
                       "¿Qué necesitas hoy?";

            case 'vacantes':
                $stopwords = ['busco', 'buscar', 'necesito', 'quiero', 'trabajo', 'de', 'en', 'el', 'la', 'vacantes', 'puesto', 'empleo'];
                $terminos = array_diff($palabrasUsuario, $stopwords);
                $filtro = implode(' ', $terminos);
                return $this->buscarVacantesEnBD($filtro);

            case 'estadisticas':
                if (!$user || !in_array($user['rol'], ['empresa_admin', 'admin_consultora'])) {
                    return "🔒 <strong>Acceso Restringido</strong><br><br>" .
                           "Las estadísticas detalladas son exclusivas para:<br>" .
                           "• Empresas registradas<br>" .
                           "• Administración de la consultora<br><br>" .
                           "Inicia sesión para ver cuántas personas han interactuado con tus vacantes.";
                }
                return $this->obtenerEstadisticasDetalladas($user);

            case 'facturacion':
                if (!$user) {
                    return "🚫 <strong>Información de Facturación</strong><br><br>" .
                           "Debes iniciar sesión para consultar facturación.<br>" .
                           "Si eres empresa: verás tus facturas pendientes.<br>" .
                           "Si eres consultora: verás el resumen global.";
                }
                
                if ($user['rol'] === 'admin_consultora') {
                    return $this->calcularFacturacionGlobal();
                } elseif ($user['rol'] === 'empresa_admin') {
                    return $this->calcularFacturacionEmpresa($user['empresa_id']);
                } else {
                    return "🚫 La información de facturación es exclusiva para empresas y administración.";
                }

            case 'empresas':
                return $this->listarEmpresasActivas();

            case 'postulaciones':
                if (!$user || $user['rol'] !== 'candidato') {
                    return "📝 <strong>Sistema de Postulaciones</strong><br><br>" .
                           "Para postularte a vacantes:<br>" .
                           "1. Regístrate como candidato<br>" .
                           "2. Completa tu perfil<br>" .
                           "3. Busca vacantes de tu interés<br>" .
                           "4. Haz clic en 'Aplicar'<br><br>" .
                           "<a href='" . ENV_APP['BASE_URL'] . "/auth/registro?tipo=persona'>Crear cuenta ahora</a>";
                }
                return $this->consultarPostulacionesCandidato($user['id']);

            case 'generar_factura':
                if (!$user || $user['rol'] !== 'admin_consultora') {
                    return "⛔ <strong>Acceso Denegado</strong><br>Solo los administradores de la consultora pueden generar facturas.";
                }
                // Extraer nombre de empresa (todo lo que siga a 'a' o 'factura')
                // "generar factura a sony" -> "sony"
                // Ordenar del más largo al más corto para evitar reemplazos parciales incorrectos
                $prefixes = [
                    'generar factura a ', 'crear factura a ', 'emitir factura a ', 'facturar a ', 'cobrar a ',
                    'generar factura', 'crear factura', 'emitir factura', 'facturar', 'cobrar', 'nueva factura'
                ];
                
                // Usar str_ireplace para ser insensible a mayúsculas/minúsculas
                $cleanInput = str_ireplace($prefixes, '', $original);
                
                // Limpiar caracteres extra que puedan haber quedado
                $empresaNombre = trim($cleanInput);
                // Si quedó solo " a " o caracteres no alfanúmericos sueltos
                $empresaNombre = trim(str_ireplace([' a ', ' para '], '', $empresaNombre));

                if (empty($empresaNombre) || strlen($empresaNombre) < 2) {
                    
                    // Obtener empresas para mostrar botones (Aumentado LIMIT a 20 para ver todas)
                    $stmt = $this->db->query("SELECT nombre FROM empresas WHERE estado = 'activa' ORDER BY id DESC LIMIT 20");
                    $empresas = $stmt->fetchAll(PDO::FETCH_COLUMN);

                    $this->acciones = [];
                    foreach ($empresas as $emp) {
                        $this->acciones[] = ['texto' => "🏢 $emp", 'accion' => "generar factura a $emp"];
                    }
                    $this->acciones[] = ['texto' => '❌ Cancelar', 'accion' => 'cancelar'];

                    return "🏢 <strong>Generar Factura Automática</strong><br><br>" .
                           "Por favor, selecciona una empresa de la lista o escribe el nombre.<br>" .
                           "Ejemplo: 'Generar factura a <strong>Coca Cola</strong>'<br><br>" .
                           "👇 <strong>Empresas Disponibles:</strong>";
                }

                return $this->generarFacturaEmpresa($empresaNombre);

            case 'registro':
                return "📝 <strong>¿Quieres registrarte?</strong><br><br>" .
                       "Tenemos 3 tipos de cuenta:<br>" .
                       "👤 <a href='" . ENV_APP['BASE_URL'] . "/auth/registro?tipo=persona'><strong>Candidato</strong></a> - Buscar empleo<br>" .
                       "🏢 <a href='" . ENV_APP['BASE_URL'] . "/auth/registro?tipo=empresa'><strong>Empresa</strong></a> - Publicar vacantes<br>" .
                       "⚙️ <a href='" . ENV_APP['BASE_URL'] . "/auth/registro?tipo=consultora'><strong>Consultora</strong></a> - Administración<br><br>" .
                       "¡Es gratis y toma menos de 2 minutos!";

            case 'login':
                return "🔑 <strong>Iniciar Sesión</strong><br><br>" .
                       "Pulsa el botón <strong>'Acceder'</strong> en el menú superior para ingresar con tu email y contraseña.<br><br>" .
                       "<a href='" . ENV_APP['BASE_URL'] . "/auth/login'>Ir a Login</a>";

            case 'ubicacion':
                return "📍 <strong>Oficinas Centrales</strong><br><br>" .
                       "🏢 Plaza Las Lomas, David, Chiriquí<br>" .
                       "📞 +507 775-0000<br>" .
                       "📧 admin@consultoreschiriqui.com<br>" .
                       "🕐 Lunes a Viernes, 8:00 AM - 5:00 PM<br><br>" .
                       "<strong>Centro de Datos:</strong> TYGO - Ciudad del Saber, Panamá";

            case 'ayuda':
                return "❓ <strong>¿Necesitas ayuda?</strong><br><br>" .
                       "Puedo ayudarte con:<br>" .
                       "• Buscar vacantes específicas<br>" .
                       "• Ver estadísticas de tu empresa<br>" .
                       "• Consultar estado de facturación<br>" .
                       "• Información sobre cómo postularte<br>" .
                       "• Datos de contacto y ubicación<br><br>" .
                       "Escribe tu pregunta en lenguaje natural, ¡te entenderé! 😊";

            case 'despedida':
                return "👋 ¡Hasta pronto!<br><br>" .
                       "Si necesitas algo más, estaré aquí 24/7.<br>" .
                       "Que tengas un excelente día. 😊";

            default:
                // Plan B: Buscar como vacante
                if (strlen($input) > 3) {
                    $res = $this->buscarVacantesEnBD($input);
                    if (strpos($res, 'No encontré') === false) return $res;
                }
                return "🤔 <strong>No estoy seguro de qué necesitas...</strong><br><br>" .
                       "Intenta escribir:<br>" .
                       "• 'buscar empleo en tecnología'<br>" .
                       "• 'ver estadísticas'<br>" .
                       "• 'cuánto debo de facturación'<br>" .
                       "• 'dónde están ubicados'<br>" .
                       "• 'cómo me registro'";
        }
    }

    // ========== FUNCIONES DE BASE DE DATOS (TIEMPO REAL) ==========

    private function buscarVacantesEnBD(string $filtro): string
    {
        $filtro = trim($filtro);
        try {
            $sql = "SELECT v.id, v.titulo, v.slug, e.nombre as empresa, v.ubicacion, v.modalidad, v.salario_min, v.salario_max
                    FROM vacantes v 
                    JOIN empresas e ON v.empresa_id = e.id 
                    WHERE v.estado = 'abierta'";
            
            $params = [];
            if (!empty($filtro)) {
                $sql .= " AND (v.titulo LIKE ? OR v.descripcion LIKE ? OR e.nombre LIKE ?)";
                $term = "%$filtro%";
                $params = [$term, $term, $term];
            }
            $sql .= " ORDER BY v.fecha_publicacion DESC LIMIT 5";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultados)) {
                return "📭 <strong>No encontré vacantes activas</strong> que coincidan con '<strong>$filtro</strong>' en este momento.<br><br>" .
                       "Intenta buscar con otras palabras clave o <a href='" . ENV_APP['BASE_URL'] . "/vacantes'>ver todas las vacantes disponibles</a>.";
            }

            $html = "🔍 <strong>Encontré " . count($resultados) . " oportunidades:</strong><br><br>";
            
            foreach ($resultados as $v) {
                $url = ENV_APP['BASE_URL'] . "/vacantes/" . $v['slug'];
                $salario = '';
                if ($v['salario_min'] && $v['salario_max']) {
                    $salario = " | 💵 B/. " . number_format($v['salario_min']) . " - " . number_format($v['salario_max']);
                }
                
                $html .= "🔹 <a href='$url' target='_blank' style='color:#2563eb; font-weight:bold;'>{$v['titulo']}</a><br>";
                $html .= "<small>🏢 {$v['empresa']} | 📍 {$v['ubicacion']} | {$v['modalidad']}{$salario}</small><br><br>";
                
                // ⚡ REGISTRAR PEAJE (Interacción de Chat)
                $this->registrarPeaje($v['id'], 'chat_consulta');
            }
            
            $html .= "<em style='color:#64748b;'>(✅ Interacciones registradas para facturación)</em>";
            return $html;

        } catch (\Exception $e) {
            return "❌ Error al conectar con la base de datos. Intenta de nuevo.";
        }
    }

    private function obtenerEstadisticasDetalladas($user): string
    {
        try {
            $empresaId = $user['empresa_id'] ?? 0;
            $esConsultora = ($user['rol'] === 'admin_consultora');
            
            // Filtro de empresa
            $condicion = $esConsultora ? "1=1" : "v.empresa_id = $empresaId";

            // 1. TOTALES GENERALES
            $sqlTotal = "SELECT 
                            COUNT(*) as total_interacciones,
                            COUNT(DISTINCT iv.vacante_id) as vacantes_con_actividad,
                            SUM(CASE WHEN iv.tipo_interaccion = 'ver_detalle' THEN 1 ELSE 0 END) as vistas,
                            SUM(CASE WHEN iv.tipo_interaccion = 'click_aplicar' THEN 1 ELSE 0 END) as aplicaciones,
                            SUM(CASE WHEN iv.tipo_interaccion = 'chat_consulta' THEN 1 ELSE 0 END) as chats
                        FROM interacciones_vacante iv
                        JOIN vacantes v ON iv.vacante_id = v.id
                        WHERE $condicion";

            $stats = $this->db->query($sqlTotal)->fetch(PDO::FETCH_ASSOC);

            // 2. TOP 5 VACANTES MÁS VISTAS
            $sqlTop = "SELECT v.titulo, COUNT(*) as interacciones
                       FROM interacciones_vacante iv
                       JOIN vacantes v ON iv.vacante_id = v.id
                       WHERE $condicion
                       GROUP BY v.id
                       ORDER BY interacciones DESC
                       LIMIT 5";
            $topVacantes = $this->db->query($sqlTop)->fetchAll(PDO::FETCH_ASSOC);

            // 3. INTERACCIONES POR DÍA (Últimos 7 días)
            $sqlDias = "SELECT DATE(iv.fecha_hora) as dia, COUNT(*) as cantidad
                        FROM interacciones_vacante iv
                        JOIN vacantes v ON iv.vacante_id = v.id
                        WHERE $condicion AND iv.fecha_hora >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                        GROUP BY DATE(iv.fecha_hora)
                        ORDER BY dia DESC";
            $porDia = $this->db->query($sqlDias)->fetchAll(PDO::FETCH_ASSOC);

            // 📊 CONSTRUIR RESPUESTA
            $total = $stats['total_interacciones'] ?? 0;
            $vistas = $stats['vistas'] ?? 0;
            $apps = $stats['aplicaciones'] ?? 0;
            $chats = $stats['chats'] ?? 0;
            $vacantes_activas = $stats['vacantes_con_actividad'] ?? 0;

            $resp = "📊 <strong>ESTADÍSTICAS EN TIEMPO REAL</strong><br>";
            $resp .= "━━━━━━━━━━━━━━━━━━━━<br><br>";
            
            $resp .= "<strong>📈 RESUMEN GENERAL</strong><br>";
            $resp .= "Total Interacciones: <strong style='color:#2563eb;'>{$total}</strong><br>";
            $resp .= "Vacantes con Actividad: <strong>{$vacantes_activas}</strong><br><br>";
            
            $resp .= "<strong>📋 DESGLOSE POR TIPO</strong><br>";
            $resp .= "👁️ Vistas de Detalle: <strong>{$vistas}</strong> (B/. 1.50 c/u)<br>";
            $resp .= "👆 Clicks en Aplicar: <strong>{$apps}</strong> (B/. 5.00 c/u)<br>";
            $resp .= "🤖 Consultas en Chat: <strong>{$chats}</strong> (B/. 2.50 c/u)<br><br>";

            // Cálculo de ingresos estimados
            $ingresoEstimado = ($vistas * 1.50) + ($apps * 5.00) + ($chats * 2.50);
            $resp .= "💰 <strong>Ingreso Estimado:</strong> B/. " . number_format($ingresoEstimado, 2) . "<br><br>";

            // TOP 5
            if (!empty($topVacantes)) {
                $resp .= "<strong>🔥 TOP 5 VACANTES MÁS VISTAS</strong><br>";
                foreach ($topVacantes as $idx => $tv) {
                    $resp .= ($idx + 1) . ". {$tv['titulo']} - <strong>{$tv['interacciones']}</strong> interacciones<br>";
                }
                $resp .= "<br>";
            }

            // ACTIVIDAD POR DÍA
            if (!empty($porDia)) {
                $resp .= "<strong>📅 ACTIVIDAD ÚLTIMOS 7 DÍAS</strong><br>";
                foreach ($porDia as $dia) {
                    $resp .= date('d/m/Y', strtotime($dia['dia'])) . ": <strong>{$dia['cantidad']}</strong> interacciones<br>";
                }
            }

            return $resp;

        } catch (\Exception $e) {
            return "❌ Error al consultar estadísticas: " . $e->getMessage();
        }
    }

    // … [resto de métodos sin modificaciones] …

    /**
     * Limpia una respuesta HTML para enviarla en texto plano.
     * Reemplaza <br> por saltos de línea y elimina las demás etiquetas.
     */
    private function limpiarRespuesta(string $html): string
    {
        // Sustituir <br> y variantes por saltos de línea
        $texto = preg_replace('/<br\s*\/?>/i', "\n", $html);
        // Eliminar todas las demás etiquetas (mantener anchors si se desea)
        $texto = strip_tags($texto);
        // Opcional: decodificar entidades HTML
        return html_entity_decode($texto, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    // … [métodos normalizarTexto(), registrarPeaje(), logChat() sin cambios] …
    private function listarEmpresasActivas(): string
    {
        try {
            $stmt = $this->db->query("SELECT nombre, sector, sitio_web FROM empresas WHERE estado = 'activa' ORDER BY nombre ASC LIMIT 5");
            $empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($empresas)) return "🏢 No hay empresas activas registradas por el momento.";

            $html = "🏢 <strong>Empresas Destacadas:</strong><br><br>";
            foreach ($empresas as $e) {
                $html .= "🔹 <strong>{$e['nombre']}</strong> ({$e['sector']})<br>";
                if ($e['sitio_web']) $html .= "<a href='{$e['sitio_web']}' target='_blank'>{$e['sitio_web']}</a><br>";
                $html .= "<br>";
            }
            return $html;
        } catch (\Exception $e) {
            return "❌ Error al listar empresas.";
        }
    }

    private function consultarPostulacionesCandidato($candidatoId): string
    {
        try {
            $stmt = $this->db->prepare("
                SELECT v.titulo, e.nombre as empresa, p.fecha_postulacion, p.estado
                FROM postulaciones p
                JOIN vacantes v ON p.vacante_id = v.id
                JOIN empresas e ON v.empresa_id = e.id
                WHERE p.solicitante_id = ?
                ORDER BY p.fecha_postulacion DESC LIMIT 3
            ");
            $stmt->execute([$candidatoId]);
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($posts)) return "📭 No tienes postulaciones activas.";

            $html = "📝 <strong>Tus Postulaciones Recientes:</strong><br><br>";
            foreach ($posts as $p) {
                $estado = ucfirst($p['estado']);
                $html .= "🔹 <strong>{$p['titulo']}</strong> - {$p['empresa']}<br>";
                $html .= "📅 " . date('d/m/Y', strtotime($p['fecha_postulacion'])) . " | Estado: <strong>{$estado}</strong><br><br>";
            }
            return $html;
        } catch (\Exception $e) {
            return "❌ Error al consultar postulaciones.";
        }
    }

    private function calcularFacturacionGlobal(): string
    {
        try {
            // Lógica similar a ConsultoraController dashboard
            // Suma simple de actividad para demo
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM interacciones_vacante");
            $totalInteracciones = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Estimación burda para demo
            $totalDinero = $totalInteracciones * 2.00; // Promedio ponderado aprox

            return "💰 <strong>Facturación Global (Consultora)</strong><br><br>" .
                   "Total Interacciones: <strong>{$totalInteracciones}</strong><br>" .
                   "Estimado Global: <strong style='color:green'>B/. " . number_format($totalDinero, 2) . "</strong><br><br>" .
                   "<a href='" . ENV_APP['BASE_URL'] . "/consultora/facturacion' style='color:#2563eb;'>Ver reporte detallado</a>";
        } catch (\Exception $e) {
            return "❌ Error calculando facturación global.";
        }
    }

    private function calcularFacturacionEmpresa($empresaId): string
    {
        try {
            // 1. Facturas emitidas (Deuda real)
            $stmt = $this->db->prepare("SELECT SUM(total) as deuda FROM facturas WHERE empresa_id = ? AND estado = 'emitida'");
            $stmt->execute([$empresaId]);
            $deuda = $stmt->fetch(PDO::FETCH_ASSOC)['deuda'] ?? 0;

            // 2. Consumo en curso (No facturado)
            // Reutilizamos lógica simplificada de EmpresaController
            // Por simplicidad en Chatbot, mostramos solo deuda emitida o mensaje genérico
            
            $html = "💰 <strong>Estado de Cuenta</strong><br><br>";
            
            if ($deuda > 0) {
                $html .= "⚠️ Tienes facturas pendientes por: <strong style='color:#ef4444'>B/. " . number_format($deuda, 2) . "</strong><br>";
                $html .= "<a href='" . ENV_APP['BASE_URL'] . "/empresa/facturacion'>Pagar ahora</a>";
            } else {
                $html .= "✅ ¡Estás al día! No tienes deuda pendiente.";
            }

            return $html;

        } catch (\Exception $e) {
            return "❌ Error consultando facturación.";
        }
    }

    private function generarFacturaEmpresa(string $nombreEmpresa): string
    {
        try {
            // 1. Buscar empresa
            $stmt = $this->db->prepare("SELECT id, nombre FROM empresas WHERE nombre LIKE ? LIMIT 1");
            $stmt->execute(["%$nombreEmpresa%"]);
            $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$empresa) {
                return "❌ No encontré ninguna empresa llamada '<strong>$nombreEmpresa</strong>'. Verifica el nombre.";
            }

            // 2. Calcular montos (Simulación rápida basada en FacturacionController)
            $p_vista = 1.50; 
            $p_click = 5.00; 
            $p_chat = 2.50;

            // Fechas: Mes actual
            $inicio = date('Y-m-01');
            $fin = date('Y-m-t');

            // Interacciones reales
            $sql = "SELECT tipo_interaccion, COUNT(*) as cantidad FROM interacciones_vacante iv 
                    JOIN vacantes v ON iv.vacante_id = v.id 
                    WHERE v.empresa_id = ? AND DATE(iv.fecha_hora) BETWEEN ? AND ? 
                    GROUP BY tipo_interaccion";
            $stmtInt = $this->db->prepare($sql);
            $stmtInt->execute([$empresa['id'], $inicio, $fin]);
            $interacciones = $stmtInt->fetchAll(PDO::FETCH_ASSOC);

            $subtotal = 0;
            $detallesParaInsertar = [];
            
            foreach ($interacciones as $i) {
                $precio = 0;
                if ($i['tipo_interaccion'] == 'ver_detalle') $precio = $p_vista;
                elseif ($i['tipo_interaccion'] == 'click_aplicar') $precio = $p_click;
                elseif ($i['tipo_interaccion'] == 'chat_consulta') $precio = $p_chat;
                
                $totalLinea = $i['cantidad'] * $precio;
                $subtotal += $totalLinea;
                
                $detallesParaInsertar[] = [
                    'tipo' => $i['tipo_interaccion'],
                    'cant' => $i['cantidad'],
                    'unit' => $precio,
                    'tot' => $totalLinea
                ];
            }

            if ($subtotal == 0) {
                return "⚠️ La empresa <strong>{$empresa['nombre']}</strong> no tiene actividad registrable en este mes (" . date('M Y') . "). No se puede generar factura en cero.";
            }

            $itbms = $subtotal * 0.07;
            $total = $subtotal + $itbms;

            // 3. Insertar factura
            $numero_fiscal = 'BOT-' . date('His') . '-' . $empresa['id'];
            $token = bin2hex(random_bytes(16));
            
            // Valores dummy para FE
            $cufe = strtoupper(hash('sha1', $numero_fiscal . time()));
            
            $stmtFac = $this->db->prepare("
                INSERT INTO facturas 
                (empresa_id, numero_fiscal, periodo_desde, periodo_hasta, subtotal, itbms, total, estado, token_publico, cufe, fecha_autorizacion, fecha_emision)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'emitida', ?, ?, NOW(), NOW())
            ");
            $stmtFac->execute([$empresa['id'], $numero_fiscal, $inicio, $fin, $subtotal, $itbms, $total, $token, $cufe]);
            $facturaId = $this->db->lastInsertId();

            // 4. Insertar detalles
            $stmtDet = $this->db->prepare("INSERT INTO facturas_detalle (factura_id, tipo_interaccion, cantidad_interacciones, tarifa_unitaria, total_linea) VALUES (?, ?, ?, ?, ?)");
            foreach ($detallesParaInsertar as $d) {
                $stmtDet->execute([$facturaId, $d['tipo'], $d['cant'], $d['unit'], $d['tot']]);
            }

            $link = ENV_APP['BASE_URL'] . "/consultora/facturacion/ver/" . $facturaId;

            return "✅ <strong>Factura Generada Exitosamente</strong><br><br>" .
                   "🏢 Empresa: <strong>{$empresa['nombre']}</strong><br>" .
                   "💰 Total: B/. " . number_format($total, 2) . "<br>" .
                   "📄 No. Fiscal: $numero_fiscal<br><br>" .
                   "<a href='$link' target='_blank' style='background:#2563eb; color:white; padding:5px 10px; text-decoration:none; border-radius:5px;'>Ver Factura</a>";

        } catch (\Exception $e) {
            return "❌ Error interno al generar factura: " . $e->getMessage();
        }
    }

    private function normalizarTexto(string $texto): string
    {
        // 1. Convertir a minúsculas
        $texto = mb_strtolower($texto, 'UTF-8');
        
        // 2. Reemplazar vocales con tildes por vocales simples (Normalización manual robusta)
        $tildes = ['á', 'é', 'í', 'ó', 'ú', 'ñ'];
        $simples = ['a', 'e', 'i', 'o', 'u', 'n']; // Normalizamos ñ a n para facilitar coincidencia con 'vacantes', 'ubicacion'
        $texto = str_replace($tildes, $simples, $texto);

        // 3. Eliminar todo lo que no sea letra o número básico
        return trim(preg_replace('/[^a-z0-9\s]/', '', $texto));
    }

    private function logChat(string $pregunta, string $respuesta): void
    {
        try {
            // Intentar insertar en tabla de logs si existe
            $sql = "INSERT INTO chat_logs (session_id, pregunta, respuesta, fecha) VALUES (?, ?, ?, NOW())";
            // Si la tabla no existe o tiene otro nombre, esto fallará y caerá en el catch
            // Asumimos 'chat_logs' por defecto o 'historial_chat'
            // Para evitar errores fatales, verificamos existencia o usamos try/catch
            
            $session_id = session_id();
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$session_id, $pregunta, strip_tags($respuesta)]);
        } catch (\Exception $e) {
            // Silencio: no interrumpir el chat por error de log
        }
    }

    private function registrarPeaje(int $vacanteId, string $tipo): void
    {
        try {
            $sql = "INSERT INTO interacciones_vacante (vacante_id, tipo_interaccion, fecha_hora, ip_usuario) VALUES (?, ?, NOW(), ?)";
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$vacanteId, $tipo, $ip]);
        } catch (\Exception $e) {
            // Silencio
        }
    }
}

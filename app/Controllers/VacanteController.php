<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Auth;
use PDO;

class VacanteController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db_connect('local');
    }

    // Listar vacantes
    public function listar(): void
    {
        $busqueda = $_GET['busqueda'] ?? '';
        $modalidad = $_GET['modalidad'] ?? '';
        $ubicacion = $_GET['ubicacion'] ?? '';
        $empresa = $_GET['empresa'] ?? '';

        $sql = "SELECT v.*, e.nombre as empresa_nombre FROM vacantes v 
                JOIN empresas e ON v.empresa_id = e.id 
                WHERE v.estado = 'abierta'";

        $params = [];

        if (!empty($busqueda)) {
            $sql .= " AND (v.titulo LIKE ? OR v.descripcion LIKE ?)";
            $params[] = "%$busqueda%";
            $params[] = "%$busqueda%";
        }

        if (!empty($modalidad)) {
            $sql .= " AND v.modalidad = ?";
            $params[] = $modalidad;
        }

        if (!empty($ubicacion)) {
            $sql .= " AND v.ubicacion LIKE ?";
            $params[] = "%$ubicacion%";
        }

        if (!empty($empresa)) {
            $sql .= " AND v.empresa_id = ?";
            $params[] = $empresa;
        }

        $sql .= " ORDER BY v.fecha_publicacion DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $vacantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener empresas para filtro
        $empresasStmt = $this->db->query("SELECT id, nombre FROM empresas WHERE estado = 'activa' ORDER BY nombre");
        $empresas = $empresasStmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('vacantes/listar', compact('vacantes', 'empresas'));
    }

    // Ver detalle de vacante
    public function detalle($slug): void
    {
        // 1. Obtener datos de la vacante y empresa
        $stmt = $this->db->prepare("
            SELECT v.*, 
                   e.nombre as empresa_nombre, e.telefono, e.email_contacto, e.sitio_web, e.sector,
                   e.tipo, e.direccion, e.provincia, e.fecha_registro as empresa_registro
            FROM vacantes v
            JOIN empresas e ON v.empresa_id = e.id
            WHERE v.slug = ? AND v.estado = 'abierta'
        ");
        $stmt->execute([$slug]);
        $vacante = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$vacante) {
            header("HTTP/1.1 404 Not Found");
            $this->view('404');
            return;
        }

        // Datos simulados (como acordamos antes)
        $vacante['empresa_tamano'] = "51 - 200 empleados"; 
        $vacante['empresa_descripcion'] = "Líderes en transformación digital y soluciones tecnológicas.";

        // --- NUEVO: VERIFICAR SI YA SE POSTULÓ ---
        $haPostulado = false;
        if (Auth::check() && Auth::user()['rol'] === 'candidato') {
            $user = Auth::user();
            $checkPost = $this->db->prepare("SELECT id FROM postulaciones WHERE vacante_id = ? AND solicitante_id = ?");
            $checkPost->execute([$vacante['id'], $user['id']]);
            if ($checkPost->fetch()) {
                $haPostulado = true;
            }
        }

        // --- VERIFICAR CUPO (VACANTE LLENA) ---
        $stmtCount = $this->db->prepare("SELECT COUNT(*) FROM postulaciones WHERE vacante_id = ? AND estado = 'aceptado'");
        $stmtCount->execute([$vacante['id']]);
        $aceptados = $stmtCount->fetchColumn();
        
        $isFull = ($aceptados >= $vacante['cantidad_plazas']);

        // --- Lógica del Contador de Vistas (PEAJE) ---
        // Se cobra CADA VEZ que entra, si ha aceptado el modal.
        if (isset($_GET['accepted_view']) && $_GET['accepted_view'] == '1') {
            $session_id = session_id() ?: $_COOKIE['PHPSESSID'] ?? '';
            $ip_address = $_SERVER['REMOTE_ADDR'];

            // Insertar directamente sin verificar duplicados (Requerimiento: "cada vez que entre... se le cobra")
            $solicitante_id = null;
            if (Auth::check() && Auth::user()['rol'] === 'candidato') {
                $solicitante_id = Auth::user()['id'];
            }

            $this->db->prepare("
                INSERT INTO interacciones_vacante (vacante_id, tipo_interaccion, origen, ip, session_id, solicitante_id, fecha_hora)
                VALUES (?, 'ver_detalle', 'web', ?, ?, ?, NOW())
            ")->execute([$vacante['id'], $ip_address, $session_id, $solicitante_id]);
        }

        // Pasamos $haPostulado a la vista

        // --- OBTENER RESEÑAS ---
        $stmtResenas = $this->db->prepare("
            SELECT r.*, s.nombre, s.apellido, s.id as autor_id
            FROM resenas_vacante r
            JOIN solicitantes s ON r.solicitante_id = s.id
            WHERE r.vacante_id = ?
            ORDER BY r.fecha_creacion DESC
        ");
        $stmtResenas->execute([$vacante['id']]);
        $resenas = $stmtResenas->fetchAll(\PDO::FETCH_ASSOC);

        // Calcular promedio
        $promedio = 0;
        if (count($resenas) > 0) {
            $suma = array_sum(array_column($resenas, 'calificacion'));
            $promedio = round($suma / count($resenas), 1);
        }

        // Verificar si el usuario actual ya dejó reseña (para mostrar form editar o crear)
        $miResena = null;
        if (Auth::check() && Auth::user()['rol'] === 'candidato') {
            foreach ($resenas as $r) {
                if ($r['solicitante_id'] == Auth::user()['id']) {
                    $miResena = $r;
                    break;
                }
            }
        }

        // --- REPUTACIÓN DE PAGOS ---
        $stmtRep = $this->db->prepare("SELECT estado FROM facturas WHERE empresa_id = ?");
        $stmtRep->execute([$vacante['empresa_id']]);
        $facturasEmpresa = $stmtRep->fetchAll(\PDO::FETCH_ASSOC);

        $reputacion = 'nueva';
        $pagadas = 0;
        $problemas = 0;
        
        foreach ($facturasEmpresa as $f) {
            if ($f['estado'] === 'pagada') $pagadas++;
            if ($f['estado'] === 'anulada') $problemas++;
            // Pendientes vencidas también podrían ser problemas, pero simplifiquemos por ahora
        }

        if ($pagadas > 0 && $problemas === 0) {
            $reputacion = 'confiable';
        } elseif ($problemas > 0) {
            $reputacion = 'riesgo';
        } elseif ($pagadas > 0) {
            // Tiene pagadas pero también otros estados (pendientes/emitidas)
            $reputacion = 'normal';
        }

        // Pasa las nuevas variables a la vista
        $this->view('vacantes/detalle', compact('vacante', 'haPostulado', 'resenas', 'promedio', 'miResena', 'reputacion', 'isFull'));
    }

    // Redirigir a postulación (validar si está autenticado)
    public function prePostular($vacante_id): void
    {
        // Verificar que la vacante existe
        $stmt = $this->db->prepare("SELECT id FROM vacantes WHERE id = ? AND estado = 'abierta'");
        $stmt->execute([$vacante_id]);
        if (!$stmt->fetch()) {
            header('Location: ' . ENV_APP['BASE_URL'] . '/vacantes');
            exit;
        }

        // 1. VALIDACIÓN ESTRICTA: Redirigir si no hay sesión
        if (!Auth::check() || Auth::user()['rol'] !== 'candidato') {
            // Guardar mensaje opcionalmente o pasar por GET
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/registro?tipo=candidato&vacante_id=' . $vacante_id);
            exit;
        }

        // 2. Obtener solicitante_id (Ahora garantizado)
        $solicitante_id = Auth::user()['id'];

        // 3. Registrar interacción (Solo usuarios autenticados)
        $interaccionStmt = $this->db->prepare("
            INSERT INTO interacciones_vacante (vacante_id, tipo_interaccion, origen, ip, session_id, solicitante_id, fecha_hora)
            VALUES (?, 'click_aplicar', 'web', ?, ?, ?, NOW())
        ");
        $interaccionStmt->execute([$vacante_id, $_SERVER['REMOTE_ADDR'], session_id(), $solicitante_id]);

        // 4. Redirigir al flujo de postulación
        header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/postular/' . $vacante_id);
        exit;
    }
}
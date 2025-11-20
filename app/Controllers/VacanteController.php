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
        $stmt = $this->db->prepare("
            SELECT v.*, e.nombre as empresa_nombre, e.telefono, e.email_contacto, e.sitio_web, e.sector
            FROM vacantes v
            JOIN empresas e ON v.empresa_id = e.id
            WHERE v.slug = ? AND v.estado = 'abierta'
        ");
        $stmt->execute([$slug]);
        $vacante = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$vacante) {
            header("HTTP/1.1 404 Not Found");
            $this->view('404');
            return;
        }

        // Registrar interacción
        $interaccionStmt = $this->db->prepare("
            INSERT INTO interacciones_vacante (vacante_id, tipo_interaccion, origen, ip, session_id)
            VALUES (?, 'ver_detalle', 'web', ?, ?)
        ");
        $interaccionStmt->execute([$vacante['id'], $_SERVER['REMOTE_ADDR'], session_id()]);

        $this->view('vacantes/detalle', compact('vacante'));
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

        // Registrar interacción
        $interaccionStmt = $this->db->prepare("
            INSERT INTO interacciones_vacante (vacante_id, tipo_interaccion, origen, ip, session_id)
            VALUES (?, 'click_aplicar', 'web', ?, ?)
        ");
        $interaccionStmt->execute([$vacante_id, $_SERVER['REMOTE_ADDR'], session_id()]);

        // Si ya está autenticado como candidato
        if (Auth::check() && Auth::user()['rol'] === 'candidato') {
            header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/postular/' . $vacante_id);
            exit;
        }

        // Sino, redirigir a registro/login
        header('Location: ' . ENV_APP['BASE_URL'] . '/auth/registro-candidato?vacante_id=' . $vacante_id);
        exit;
    }
}
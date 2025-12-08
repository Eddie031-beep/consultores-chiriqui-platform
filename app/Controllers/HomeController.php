<?php
namespace App\Controllers;

use App\Core\Controller;
use PDO;

class HomeController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db_connect('local');
    }

    public function index(): void
    {
        // Obtener vacantes disponibles
        $vacantes = [];
        $busqueda = $_GET['busqueda'] ?? '';
        $modalidad = $_GET['modalidad'] ?? '';
        $ubicacion = $_GET['ubicacion'] ?? '';

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

        $sql .= " ORDER BY v.fecha_publicacion DESC LIMIT 12";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $vacantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('home', compact('vacantes'));
    }
    public function guia(): void
    {
        $this->view('home/guia');
    }
}
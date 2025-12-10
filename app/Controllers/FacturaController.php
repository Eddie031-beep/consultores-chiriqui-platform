<?php
namespace App\Controllers;

use App\Core\Controller;
use PDO;

class FacturaController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db_connect('local');
    }

    // Ruta: /factura/ver/{token}
    public function ver($token): void
    {
        // 1. Buscar Factura por Token
        $stmt = $this->db->prepare("
            SELECT f.*, e.nombre as empresa_nombre, e.ruc, e.direccion, e.email_contacto
            FROM facturas f
            JOIN empresas e ON f.empresa_id = e.id
            WHERE f.token_publico = ?
        ");
        $stmt->execute([$token]);
        $factura = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$factura) {
            die("Factura no encontrada o enlace inválido.");
        }

        // 2. Buscar Detalles
        $stmtDet = $this->db->prepare("SELECT * FROM facturas_detalle WHERE factura_id = ?");
        $stmtDet->execute([$factura['id']]);
        $detalles = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

        // 3. Renderizar Vista Pública (Sin navbar de admin, solo la factura)
        $this->view('facturacion/publica', compact('factura', 'detalles'));
    }
}

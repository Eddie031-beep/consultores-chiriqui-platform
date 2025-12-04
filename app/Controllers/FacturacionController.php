<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Auth;
use PDO;

class FacturacionController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db_connect('local');
        
        // Solo consultora puede acceder
        if (!Auth::check() || Auth::user()['rol'] !== 'admin_consultora') {
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/login-consultora');
            exit;
        }
    }

    // ============ LISTAR FACTURAS ============
    public function listar(): void
    {
        $mes = $_GET['mes'] ?? date('m');
        $anio = $_GET['anio'] ?? date('Y');

        $sql = "SELECT f.*, e.nombre as empresa_nombre 
                FROM facturas f
                JOIN empresas e ON f.empresa_id = e.id
                WHERE MONTH(f.fecha_emision) = ? AND YEAR(f.fecha_emision) = ?
                ORDER BY f.fecha_emision DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$mes, $anio]);
        $facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('facturacion/listar', compact('facturas', 'mes', 'anio'));
    }

    // ============ GENERAR FACTURA ============
    public function generar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->procesarFactura();
            return;
        }

        // Obtener empresas
        $stmt = $this->db->query("SELECT id, nombre FROM empresas WHERE estado = 'activa' ORDER BY nombre");
        $empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $error = '';
        $this->view('facturacion/generar', compact('empresas', 'error'));
    }

    private function procesarFactura(): void
    {
        $empresa_id = (int)($_POST['empresa_id'] ?? 0);
        $periodo_desde = $_POST['periodo_desde'] ?? '';
        $periodo_hasta = $_POST['periodo_hasta'] ?? '';

        if ($empresa_id <= 0 || empty($periodo_desde) || empty($periodo_hasta)) {
            $error = 'Todos los campos son obligatorios';
            $stmt = $this->db->query("SELECT id, nombre FROM empresas WHERE estado = 'activa' ORDER BY nombre");
            $empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->view('facturacion/generar', compact('empresas', 'error'));
            return;
        }

        try {
            // 1. Obtener tarifas vigentes de la BD (DINÁMICO)
            $stmtT = $this->db->query("SELECT * FROM peajes_tarifas WHERE activo = 1");
            $tarifas = $stmtT->fetchAll(PDO::FETCH_ASSOC);
            
            $p_vista = 0.10; $p_click = 0.15; $p_chat = 0.05; // defaults
            foreach($tarifas as $t) {
                if (stripos($t['nombre_plan'], 'Vista') !== false) $p_vista = $t['precio_unitario'];
                if (stripos($t['nombre_plan'], 'Click') !== false) $p_click = $t['precio_unitario'];
                if (stripos($t['nombre_plan'], 'Chat') !== false) $p_chat = $t['precio_unitario'];
            }

            // 2. Calcular interacciones usando precios dinámicos
            $sql = "
                SELECT 
                    iv.tipo_interaccion,
                    COUNT(*) as cantidad,
                    CASE 
                        WHEN iv.tipo_interaccion = 'ver_detalle' THEN $p_vista
                        WHEN iv.tipo_interaccion = 'click_aplicar' THEN $p_click
                        WHEN iv.tipo_interaccion = 'chat_consulta' THEN $p_chat
                    END as precio_unitario
                FROM interacciones_vacante iv
                JOIN vacantes v ON iv.vacante_id = v.id
                WHERE v.empresa_id = ? AND DATE(iv.fecha_hora) BETWEEN ? AND ?
                GROUP BY iv.tipo_interaccion
            ";
            
            $stmtInt = $this->db->prepare($sql);
            $stmtInt->execute([$empresa_id, $periodo_desde, $periodo_hasta]);
            $interacciones = $stmtInt->fetchAll(PDO::FETCH_ASSOC);

            // 3. Calcular totales
            $subtotal = 0;
            foreach ($interacciones as $int) {
                $subtotal += ($int['cantidad'] * $int['precio_unitario']);
            }

            // Obtener ITBMS de configuraciones (si existe) o usar 0.07
            $stmtConf = $this->db->prepare("SELECT valor FROM configuraciones WHERE codigo = 'ITBMS'");
            $stmtConf->execute();
            $itbms_pct = $stmtConf->fetchColumn() ?: 0.07;

            $itbms = $subtotal * $itbms_pct;
            $total = $subtotal + $itbms;

            // Generar número de factura único
            $numero_fiscal = 'FAC-' . date('YmdHis') . '-' . $empresa_id;
            $token_publico = bin2hex(random_bytes(32));

            // Insertar factura
            $stmtFac = $this->db->prepare("
                INSERT INTO facturas 
                (empresa_id, numero_fiscal, periodo_desde, periodo_hasta, subtotal, itbms, total, estado, token_publico)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'emitida', ?)
            ");
            $stmtFac->execute([$empresa_id, $numero_fiscal, $periodo_desde, $periodo_hasta, $subtotal, $itbms, $total, $token_publico]);

            $factura_id = $this->db->lastInsertId();

            // Insertar detalles
            foreach ($interacciones as $int) {
                $total_linea = $int['cantidad'] * $int['precio_unitario'];
                $stmtDet = $this->db->prepare("
                    INSERT INTO facturas_detalle 
                    (factura_id, cantidad_interacciones, tarifa_unitaria, total_linea)
                    VALUES (?, ?, ?, ?)
                ");
                $stmtDet->execute([$factura_id, $int['cantidad'], $int['precio_unitario'], $total_linea]);
            }

            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Factura generada exitosamente: ' . $numero_fiscal];
            header('Location: ' . ENV_APP['BASE_URL'] . '/facturacion/ver/' . $factura_id);
            exit;

        } catch (\PDOException $e) {
            $error = 'Error al generar factura: ' . $e->getMessage();
            $stmt = $this->db->query("SELECT id, nombre FROM empresas WHERE estado = 'activa' ORDER BY nombre");
            $empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->view('facturacion/generar', compact('empresas', 'error'));
        }
    }

    // ============ VER FACTURA ============
    public function ver($id): void
    {
        $id = (int)$id;

        $stmt = $this->db->prepare("
            SELECT f.*, e.nombre as empresa_nombre, e.ruc, e.direccion
            FROM facturas f
            JOIN empresas e ON f.empresa_id = e.id
            WHERE f.id = ?
        ");
        $stmt->execute([$id]);
        $factura = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$factura) {
            header('Location: ' . ENV_APP['BASE_URL'] . '/facturacion');
            exit;
        }

        // Obtener detalles
        $stmtDet = $this->db->prepare("
            SELECT * FROM facturas_detalle WHERE factura_id = ?
        ");
        $stmtDet->execute([$id]);
        $detalles = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

        $this->view('facturacion/ver', compact('factura', 'detalles'));
    }

    // ============ DESCARGAR PDF ============
    public function descargarPDF($id): void
    {
        $id = (int)$id;

        $stmt = $this->db->prepare("
            SELECT f.*, e.nombre as empresa_nombre, e.ruc, e.direccion
            FROM facturas f
            JOIN empresas e ON f.empresa_id = e.id
            WHERE f.id = ?
        ");
        $stmt->execute([$id]);
        $factura = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$factura) {
            die('Factura no encontrada');
        }

        // Generar contenido PDF (básico)
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Factura_' . $factura['numero_fiscal'] . '.pdf"');

        echo "FACTURA DIGITAL\n\n";
        echo "Número: " . $factura['numero_fiscal'] . "\n";
        echo "Fecha: " . date('d/m/Y H:i', strtotime($factura['fecha_emision'])) . "\n";
        echo "Empresa: " . $factura['empresa_nombre'] . "\n";
        echo "RUC: " . $factura['ruc'] . "\n\n";
        echo "Período: " . $factura['periodo_desde'] . " - " . $factura['periodo_hasta'] . "\n\n";
        echo "Subtotal: B/. " . number_format($factura['subtotal'], 2) . "\n";
        echo "ITBMS (7%): B/. " . number_format($factura['itbms'], 2) . "\n";
        echo "TOTAL: B/. " . number_format($factura['total'], 2) . "\n\n";
        echo "Estado: " . ucfirst($factura['estado']) . "\n";
        exit;
    }
}
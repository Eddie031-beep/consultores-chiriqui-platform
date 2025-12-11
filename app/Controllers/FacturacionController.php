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

    // ============ GENERAR FACTURA (POR VACANTE CERRADA) ============
    public function generar(): void
    {
        // 1. Obtener empresas que tienen vacantes CERRADAS y NO FACTURADAS (Simplificación: Vacantes cerradas)
        // En un sistema real, excluiríamos las que ya tienen `facturas` asociada.
        $stmt = $this->db->query("
            SELECT e.id, e.nombre, v.id as vacante_id, v.titulo as vacante_titulo, v.fecha_cierre
            FROM empresas e
            JOIN vacantes v ON v.empresa_id = e.id
            WHERE v.estado = 'cerrada'
            ORDER BY e.nombre, v.fecha_cierre DESC
        ");
        $vacantesCerradas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agrupar por empresa para el select
        $empresasConVacantes = [];
        foreach ($vacantesCerradas as $row) {
            $empresasConVacantes[$row['id']]['nombre'] = $row['nombre'];
            $empresasConVacantes[$row['id']]['vacantes'][] = $row;
        }

        // Variables iniciales
        $error = '';
        $preview = null;
        
        // Input: vacante_id
        $selectedVacanteId = (int)($_GET['vacante_id'] ?? ($_POST['vacante_id'] ?? 0));
        // Fecha Vencimiento
        $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? date('Y-m-d', strtotime('+15 days'));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $accion = $_POST['accion'] ?? 'preview';
            
            if ($selectedVacanteId > 0) {
                
                // 1. Calcular Detalles (Por Vacante Completa)
                $calculo = $this->calcularDetallesPorVacante($selectedVacanteId);

                if ($accion === 'preview') {
                    $preview = $calculo;
                } elseif ($accion === 'generar') {
                    if ($calculo['total'] > 0) {
                        $this->guardarFacturaVacante($selectedVacanteId, $fecha_vencimiento, $calculo);
                        return;
                    } else {
                        $error = "La vacante seleccionada no tiene consumo para facturar.";
                    }
                }

            } else {
                $error = 'Por favor seleccione una vacante cerrada.';
            }
        }

        $this->view('facturacion/generar', compact('empresasConVacantes', 'error', 'selectedVacanteId', 'preview', 'fecha_vencimiento'));
    }

    private function calcularDetallesPorVacante($vacante_id): array
    {
        $p_vista = 1.50; $p_click = 5.00; $p_chat = 2.50;

        // Calcular costo total histórico de la vacante
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
            WHERE iv.vacante_id = ?
            GROUP BY iv.tipo_interaccion
        ";
        
        $stmtInt = $this->db->prepare($sql);
        $stmtInt->execute([$vacante_id]);
        $detalles = $stmtInt->fetchAll(PDO::FETCH_ASSOC);

        $subtotal = 0;
        foreach ($detalles as &$det) {
            $det['total_linea'] = $det['cantidad'] * $det['precio_unitario'];
            $subtotal += $det['total_linea'];
        }

        // Información de la vacante para fechas
        $stmtVac = $this->db->prepare("SELECT fecha_publicacion, fecha_cierre FROM vacantes WHERE id = ?");
        $stmtVac->execute([$vacante_id]);
        $vacanteInfo = $stmtVac->fetch(PDO::FETCH_ASSOC);

        // ITBMS
        $stmtConf = $this->db->prepare("SELECT valor FROM configuraciones WHERE codigo = 'ITBMS'");
        $stmtConf->execute();
        $itbms_pct = $stmtConf->fetchColumn() ?: 0.07;

        return [
            'detalles' => $detalles,
            'subtotal' => $subtotal,
            'itbms' => $subtotal * $itbms_pct,
            'total' => ($subtotal + ($subtotal * $itbms_pct)),
            'periodo_desde' => $vacanteInfo['fecha_publicacion'],
            'periodo_hasta' => $vacanteInfo['fecha_cierre'] ?? date('Y-m-d H:i:s')
        ];
    }

    private function guardarFacturaVacante($vacante_id, $vencimiento, $calculo): void
    {
        try {
            $this->db->beginTransaction();

            // Obtener empresa_id de la vacante
            $stmtEmp = $this->db->prepare("SELECT empresa_id FROM vacantes WHERE id = ?");
            $stmtEmp->execute([$vacante_id]);
            $empresa_id = $stmtEmp->fetchColumn();

            $numero_fiscal = 'FAC-VAC-' . $vacante_id . '-' . date('Ymd');
            $token_publico = bin2hex(random_bytes(32));
            $cufe = strtoupper(hash('sha1', $numero_fiscal . $token_publico . time()));
            $protocolo = 'AUT-' . date('Y') . '-' . mt_rand(100000, 999999);
            $clave = mt_rand(10000000, 99999999);

            $stmtFac = $this->db->prepare("
                INSERT INTO facturas 
                (empresa_id, numero_fiscal, periodo_desde, periodo_hasta, subtotal, itbms, total, estado, token_publico, cufe, protocolo_autorizacion, clave_acceso, fecha_autorizacion, fecha_vencimiento)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'emitida', ?, ?, ?, ?, NOW(), ?)
            ");
            
            $stmtFac->execute([
                $empresa_id, $numero_fiscal, 
                $calculo['periodo_desde'], $calculo['periodo_hasta'], // Usamos fechas reales de la vacante
                $calculo['subtotal'], $calculo['itbms'], $calculo['total'], 
                $token_publico, $cufe, $protocolo, $clave, $vencimiento
            ]);

            $factura_id = $this->db->lastInsertId();

            foreach ($calculo['detalles'] as $det) {
                $stmtDet = $this->db->prepare("
                    INSERT INTO facturas_detalle 
                    (factura_id, tipo_interaccion, cantidad_interacciones, tarifa_unitaria, total_linea)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmtDet->execute([$factura_id, $det['tipo_interaccion'], $det['cantidad'], $det['precio_unitario'], $det['total_linea']]);
            }

            $this->db->commit();

            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Factura (Cierre Vacante) generada exitosamente: ' . $numero_fiscal];
            header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/facturacion/ver/' . $factura_id);
            exit;

        } catch (\PDOException $e) {
            $this->db->rollBack();
            echo "Error DB: " . $e->getMessage(); exit; 
        }
    }

    // ============ VER FACTURA ============
    public function ver($id): void
    {
        $id = (int)$id;

        $stmt = $this->db->prepare("
            SELECT f.*, e.nombre as empresa_nombre, e.ruc, e.direccion, e.email_contacto, e.telefono
            FROM facturas f
            JOIN empresas e ON f.empresa_id = e.id
            WHERE f.id = ?
        ");
        $stmt->execute([$id]);
        $factura = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$factura) {
            header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/facturacion');
            exit;
        }

        // Obtener detalles
        $stmtDet = $this->db->prepare("
            SELECT * FROM facturas_detalle WHERE factura_id = ?
        ");
        $stmtDet->execute([$id]);
        $detalles = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

        // Generar URL para código QR (usando API pública para compatibilidad)
        // Datos reales para el QR según DGI
        $qrData = "CUFE:{$factura['cufe']}|FECHA:{$factura['fecha_autorizacion']}|TOTAL:{$factura['total']}|RUC:{$factura['ruc']}";
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrData);

        $this->view('facturacion/ver', compact('factura', 'detalles', 'qrUrl'));
    }

    // ============ DESCARGAR RESUMEN (TXT) ============
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

        // CORRECCIÓN: Cambiamos a text/plain porque estamos generando texto, no un binario PDF real.
        // Si quieres un PDF con diseño, usa la opción "Imprimir -> Guardar como PDF" del navegador.
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="Factura_' . $factura['numero_fiscal'] . '.txt"');

        echo "=================================================\n";
        echo "          FACTURA ELECTRÓNICA - RESUMEN          \n";
        echo "=================================================\n\n";
        
        echo "EMISOR: Consultores Chiriquí S.A.\n";
        echo "RUC: 155694852-2-2025 DV 55\n";
        echo "-------------------------------------------------\n\n";
        
        echo "CLIENTE: " . $factura['empresa_nombre'] . "\n";
        echo "RUC/CIP: " . $factura['ruc'] . "\n";
        echo "DIRECCIÓN: " . $factura['direccion'] . "\n\n";
        
        echo "DETALLES FISCALES:\n";
        echo "No. Fiscal: " . $factura['numero_fiscal'] . "\n";
        echo "Fecha: " . date('d/m/Y H:i', strtotime($factura['fecha_emision'])) . "\n";
        echo "CUFE: " . ($factura['cufe'] ?? 'N/A') . "\n";
        echo "-------------------------------------------------\n\n";
        
        echo "DESGLOSE:\n";
        echo "Período: " . date('d/m/Y', strtotime($factura['periodo_desde'])) . " al " . date('d/m/Y', strtotime($factura['periodo_hasta'])) . "\n\n";
        
        echo "Subtotal:      B/. " . number_format($factura['subtotal'], 2) . "\n";
        echo "ITBMS (7%):    B/. " . number_format($factura['itbms'], 2) . "\n";
        echo "=================================================\n";
        echo "TOTAL A PAGAR: B/. " . number_format($factura['total'], 2) . "\n";
        echo "=================================================\n";
        
        echo "\nEstado: " . strtoupper($factura['estado']) . "\n";
        exit;
    }
    // ============ ACTUALIZAR ESTADO ============
    public function actualizarEstado($id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/facturacion');
            exit;
        }

        $nuevoEstado = $_POST['estado_factura'] ?? '';
        $id = (int)$id;

        if (in_array($nuevoEstado, ['emitida', 'pagada', 'anulada', 'en_revision'])) {
            $stmt = $this->db->prepare("UPDATE facturas SET estado = ? WHERE id = ?");
            $stmt->execute([$nuevoEstado, $id]);
        }

        header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/facturacion/ver/' . $id);
        exit;
    }
}

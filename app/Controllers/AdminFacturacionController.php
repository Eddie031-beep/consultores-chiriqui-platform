<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Auth;
use PDO;

class AdminFacturacionController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db_connect('local');
        // Solo Admin Consultora puede entrar aquí
        if (!Auth::check() || Auth::user()['rol'] !== 'admin_consultora') {
            header('Location: ' . ENV_APP['BASE_URL']);
            exit;
        }
    }

    // 1. VISTA: Listar empresas y cuánto deben (Pre-factura)
    public function panel(): void
    {
        // Obtener empresas con interacciones pendientes de facturar
        // (Aquellas que están en interacciones_vacante pero NO en facturas_detalle)
        /* NOTA: Para simplificar este ejemplo, asumiremos un corte mensual. 
           En un sistema real, marcarías cada interacción con un flag 'facturado'.
        */
        
        $sql = "
            SELECT e.id, e.nombre, e.ruc,
                COUNT(iv.id) as total_interacciones,
                SUM(CASE 
                    WHEN iv.tipo_interaccion = 'ver_detalle' THEN 1.50
                    WHEN iv.tipo_interaccion = 'click_aplicar' THEN 5.00
                    WHEN iv.tipo_interaccion = 'chat_consulta' THEN 2.50
                    ELSE 0 END
                ) as monto_pendiente
            FROM empresas e
            JOIN vacantes v ON v.empresa_id = e.id
            JOIN interacciones_vacante iv ON iv.vacante_id = v.id
            -- Aquí filtramos interacciones del mes actual para el ejemplo
            WHERE MONTH(iv.fecha_hora) = MONTH(CURRENT_DATE())
            AND YEAR(iv.fecha_hora) = YEAR(CURRENT_DATE())
            GROUP BY e.id
        ";
        
        $pendientes = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $this->view('consultora/facturacion/panel', compact('pendientes'));
    }

    // 2. ACCIÓN: Generar la factura real (INSERT en BD)
    public function generar($empresa_id): void
    {
        try {
            $this->db->beginTransaction();

            // A. Calcular detalles
            $sqlDetalles = "
                SELECT iv.tipo_interaccion, COUNT(*) as cantidad,
                CASE 
                    WHEN iv.tipo_interaccion = 'ver_detalle' THEN 1.50
                    WHEN iv.tipo_interaccion = 'click_aplicar' THEN 5.00
                    WHEN iv.tipo_interaccion = 'chat_consulta' THEN 2.50
                END as precio_unitario
                FROM interacciones_vacante iv
                JOIN vacantes v ON iv.vacante_id = v.id
                WHERE v.empresa_id = ?
                AND MONTH(iv.fecha_hora) = MONTH(CURRENT_DATE())
                GROUP BY iv.tipo_interaccion
            ";
            $stmt = $this->db->prepare($sqlDetalles);
            $stmt->execute([$empresa_id]);
            $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($detalles)) {
                throw new \Exception("No hay interacciones para facturar este mes.");
            }

            // B. Calcular Totales
            $subtotal = 0;
            foreach ($detalles as $d) {
                $subtotal += $d['cantidad'] * $d['precio_unitario'];
            }
            $itbms = $subtotal * 0.07;
            $total = $subtotal + $itbms;

            // C. Crear Cabecera de Factura
            $numeroFiscal = 'FAC-' . date('Ymd') . '-' . rand(1000, 9999);
            $token = bin2hex(random_bytes(16)); // Token para ver PDF público

            $sqlFactura = "INSERT INTO facturas 
                (empresa_id, numero_fiscal, fecha_emision, periodo_desde, periodo_hasta, subtotal, itbms, total, estado, token_publico)
                VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, 'emitida', ?)";
            
            // Fechas del mes actual
            $desde = date('Y-m-01');
            $hasta = date('Y-m-t');

            $stmtFact = $this->db->prepare($sqlFactura);
            $stmtFact->execute([$empresa_id, $numeroFiscal, $desde, $hasta, $subtotal, $itbms, $total, $token]);
            $facturaId = $this->db->lastInsertId();

            // D. Insertar Detalles
            $sqlInsertDetalle = "INSERT INTO facturas_detalle 
                (factura_id, tipo_interaccion, cantidad_interacciones, tarifa_unitaria, total_linea)
                VALUES (?, ?, ?, ?, ?)";
            $stmtDetalle = $this->db->prepare($sqlInsertDetalle);

            foreach ($detalles as $d) {
                $totalLinea = $d['cantidad'] * $d['precio_unitario'];
                $stmtDetalle->execute([$facturaId, $d['tipo_interaccion'], $d['cantidad'], $d['precio_unitario'], $totalLinea]);
            }

            $this->db->commit();
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => "Factura $numeroFiscal generada correctamente."];

        } catch (\Exception $e) {
            $this->db->rollBack();
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error: ' . $e->getMessage()];
        }

        header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/facturacion');
        exit;
    }
}

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
    }

    private function requireConsultora(): array
    {
        $user = Auth::user();
        if (!$user || ($user['rol'] ?? '') !== 'admin_consultora') {
            $this->redirect('/');
        }
        return $user;
    }

    public function index(): void
    {
        $this->requireConsultora();

        $sql = "SELECT f.*, e.nombre AS empresa_nombre 
                FROM facturas f 
                JOIN empresas e ON e.id = f.empresa_id 
                ORDER BY f.fecha_emision DESC";
        $stmt = $this->db->query($sql);
        $facturas = $stmt->fetchAll();

        $this->view('facturacion/index', compact('facturas'));
    }

    public function estadisticas(): void
    {
        $this->requireConsultora();

        // Estadísticas por empresa
        $sql = "SELECT 
                    e.id,
                    e.nombre,
                    COUNT(DISTINCT v.id) AS total_vacantes,
                    COUNT(i.id) AS total_interacciones,
                    SUM(CASE WHEN i.tipo_interaccion = 'ver_detalle' THEN 1 ELSE 0 END) AS vistas,
                    SUM(CASE WHEN i.tipo_interaccion = 'click_aplicar' THEN 1 ELSE 0 END) AS clicks,
                    SUM(CASE WHEN i.tipo_interaccion = 'chat_consulta' THEN 1 ELSE 0 END) AS consultas_chat
                FROM empresas e
                LEFT JOIN vacantes v ON v.empresa_id = e.id
                LEFT JOIN interacciones_vacante i ON i.vacante_id = v.id
                WHERE e.estado = 'activa'
                GROUP BY e.id
                ORDER BY total_interacciones DESC";
        $stmt = $this->db->query($sql);
        $estadisticas = $stmt->fetchAll();

        // Calcular costos estimados
        foreach ($estadisticas as &$est) {
            $costo = ($est['vistas'] * 0.10) + ($est['clicks'] * 0.15) + ($est['consultas_chat'] * 0.05);
            $est['costo_estimado'] = $costo;
        }

        $this->view('facturacion/estadisticas', compact('estadisticas'));
    }

    public function generar(): void
    {
        $this->requireConsultora();

        $empresaId = isset($_GET['empresa_id']) ? (int)$_GET['empresa_id'] : 0;
        if ($empresaId <= 0) {
            $this->redirect('/consultora/facturacion');
        }

        // Obtener empresa
        $sql = "SELECT * FROM empresas WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $empresaId]);
        $empresa = $stmt->fetch();

        if (!$empresa) {
            $this->redirect('/consultora/facturacion');
        }

        // Obtener interacciones del mes actual
        $periodoDesde = date('Y-m-01');
        $periodoHasta = date('Y-m-t');

        $sql = "SELECT 
                    v.id,
                    v.titulo,
                    COUNT(i.id) AS total_interacciones,
                    SUM(CASE WHEN i.tipo_interaccion = 'ver_detalle' THEN 1 ELSE 0 END) AS vistas,
                    SUM(CASE WHEN i.tipo_interaccion = 'click_aplicar' THEN 1 ELSE 0 END) AS clicks,
                    SUM(CASE WHEN i.tipo_interaccion = 'chat_consulta' THEN 1 ELSE 0 END) AS consultas
                FROM vacantes v
                LEFT JOIN interacciones_vacante i ON i.vacante_id = v.id 
                    AND DATE(i.fecha_hora) BETWEEN :desde AND :hasta
                WHERE v.empresa_id = :eid
                GROUP BY v.id
                HAVING total_interacciones > 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'eid' => $empresaId,
            'desde' => $periodoDesde,
            'hasta' => $periodoHasta,
        ]);
        $vacantes = $stmt->fetchAll();

        $this->view('facturacion/generar', compact('empresa', 'vacantes', 'periodoDesde', 'periodoHasta'));
    }

    public function crear(): void
    {
        $this->requireConsultora();

        $empresaId = isset($_POST['empresa_id']) ? (int)$_POST['empresa_id'] : 0;
        $periodoDesde = $_POST['periodo_desde'] ?? '';
        $periodoHasta = $_POST['periodo_hasta'] ?? '';

        if ($empresaId <= 0) {
            $this->redirect('/consultora/facturacion');
        }

        // Calcular totales
        $sql = "SELECT 
                    v.id AS vacante_id,
                    COUNT(i.id) AS total_interacciones,
                    SUM(CASE WHEN i.tipo_interaccion = 'ver_detalle' THEN 1 ELSE 0 END) AS vistas,
                    SUM(CASE WHEN i.tipo_interaccion = 'click_aplicar' THEN 1 ELSE 0 END) AS clicks,
                    SUM(CASE WHEN i.tipo_interaccion = 'chat_consulta' THEN 1 ELSE 0 END) AS consultas
                FROM vacantes v
                LEFT JOIN interacciones_vacante i ON i.vacante_id = v.id 
                    AND DATE(i.fecha_hora) BETWEEN :desde AND :hasta
                WHERE v.empresa_id = :eid
                GROUP BY v.id
                HAVING total_interacciones > 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'eid' => $empresaId,
            'desde' => $periodoDesde,
            'hasta' => $periodoHasta,
        ]);
        $vacantes = $stmt->fetchAll();

        $subtotal = 0;
        foreach ($vacantes as $v) {
            $subtotal += ($v['vistas'] * 0.10) + ($v['clicks'] * 0.15) + ($v['consultas'] * 0.05);
        }

        $itbms = $subtotal * 0.07;
        $total = $subtotal + $itbms;

        // Generar número fiscal
        $numeroFiscal = sprintf('F-%04d-%s', $empresaId, date('YmdHis'));
        $tokenPublico = bin2hex(random_bytes(16));

        // Insertar factura
        $this->db->beginTransaction();

        try {
            $sql = "INSERT INTO facturas 
                    (empresa_id, numero_fiscal, periodo_desde, periodo_hasta, subtotal, itbms, total, token_publico)
                    VALUES (:eid, :num, :desde, :hasta, :sub, :itbms, :total, :token)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'eid' => $empresaId,
                'num' => $numeroFiscal,
                'desde' => $periodoDesde,
                'hasta' => $periodoHasta,
                'sub' => $subtotal,
                'itbms' => $itbms,
                'total' => $total,
                'token' => $tokenPublico,
            ]);

            $facturaId = $this->db->lastInsertId();

            // Insertar detalles
            foreach ($vacantes as $v) {
                $totalInt = $v['total_interacciones'];
                $tarifa = (($v['vistas'] * 0.10) + ($v['clicks'] * 0.15) + ($v['consultas'] * 0.05)) / $totalInt;
                $totalLinea = $totalInt * $tarifa;

                $sql = "INSERT INTO facturas_detalle 
                        (factura_id, vacante_id, cantidad_interacciones, tarifa_unitaria, total_linea)
                        VALUES (:fid, :vid, :cant, :tarifa, :total)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    'fid' => $facturaId,
                    'vid' => $v['vacante_id'],
                    'cant' => $totalInt,
                    'tarifa' => $tarifa,
                    'total' => $totalLinea,
                ]);
            }

            $this->db->commit();

            $this->redirect('/consultora/facturacion/ver/' . $tokenPublico);
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function ver(): void
    {
        // Vista pública de factura (accesible con token)
        $token = $_GET['token'] ?? '';
        if ($token === '') {
            http_response_code(404);
            echo 'Factura no encontrada';
            return;
        }

        $sql = "SELECT f.*, e.nombre AS empresa_nombre, e.ruc, e.dv, e.direccion 
                FROM facturas f 
                JOIN empresas e ON e.id = f.empresa_id 
                WHERE f.token_publico = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $token]);
        $factura = $stmt->fetch();

        if (!$factura) {
            http_response_code(404);
            echo 'Factura no encontrada';
            return;
        }

        // Obtener detalles
        $sql = "SELECT fd.*, v.titulo AS vacante_titulo 
                FROM facturas_detalle fd 
                LEFT JOIN vacantes v ON v.id = fd.vacante_id 
                WHERE fd.factura_id = :fid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['fid' => $factura['id']]);
        $detalles = $stmt->fetchAll();

        $this->view('facturacion/ver', compact('factura', 'detalles'));
    }
}
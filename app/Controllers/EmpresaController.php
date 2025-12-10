<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Auth;
use PDO;

class EmpresaController extends Controller
{
    private PDO $dbWrite;
    private PDO $dbRead;

    public function __construct()
    {
        $this->dbWrite = db_connect('write');
        $this->dbRead  = db_connect('read');
        
        if (!Auth::check() || Auth::user()['rol'] !== 'empresa_admin') {
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/login?tipo=empresa');
            exit;
        }
    }

    // ============ DASHBOARD ============
    public function dashboard(): void
    {
        $user = Auth::user();
        $empresaId = $user['empresa_id'];

        // 1. Estadísticas Básicas (Vacantes)
        $stmtVac = $this->dbRead->prepare("SELECT COUNT(*) as total FROM vacantes WHERE empresa_id = ? AND estado = 'abierta'");
        $stmtVac->execute([$empresaId]);
        $vacantesActivas = $stmtVac->fetch(PDO::FETCH_ASSOC)['total'];

        // 2. Candidatos
        $stmtCand = $this->dbRead->prepare("
            SELECT COUNT(DISTINCT solicitante_id) as total 
            FROM postulaciones p 
            JOIN vacantes v ON p.vacante_id = v.id 
            WHERE v.empresa_id = ?
        ");
        $stmtCand->execute([$empresaId]);
        $totalCandidatos = $stmtCand->fetch(PDO::FETCH_ASSOC)['total'];

        // 3. Notificaciones de Facturas Pendientes
        $stmtFac = $this->dbRead->prepare("
            SELECT COUNT(*) as pendientes, SUM(total) as deuda 
            FROM facturas 
            WHERE empresa_id = ? AND estado = 'emitida'
        ");
        $stmtFac->execute([$empresaId]);
        $facturacionInfo = $stmtFac->fetch(PDO::FETCH_ASSOC);

        // 4. Actividad Reciente (Postulaciones)
        $stmtAct = $this->dbRead->prepare("
            SELECT p.fecha_postulacion, s.nombre, s.apellido, v.titulo, s.cv_ruta
            FROM postulaciones p 
            JOIN solicitantes s ON p.solicitante_id = s.id 
            JOIN vacantes v ON p.vacante_id = v.id 
            WHERE v.empresa_id = ? 
            ORDER BY p.fecha_postulacion DESC 
            LIMIT 5
        ");
        $stmtAct->execute([$empresaId]);
        $actividadReciente = $stmtAct->fetchAll(PDO::FETCH_ASSOC);

        // 5. CONSUMO DINÁMICO (Optimizado)
        $consumoActual = 0.0;
        try {
            // Obtener tarifas (Cacheable idealmente, por ahora directo)
            $stmtTarifas = $this->dbRead->query("SELECT nombre_plan, precio_unitario FROM peajes_tarifas WHERE activo = 1");
            $tarifas = [];
            while ($row = $stmtTarifas->fetch(PDO::FETCH_ASSOC)) {
                if (stripos($row['nombre_plan'], 'Vista') !== false) $tarifas['ver_detalle'] = $row['precio_unitario'];
                if (stripos($row['nombre_plan'], 'Click') !== false) $tarifas['click_aplicar'] = $row['precio_unitario'];
                if (stripos($row['nombre_plan'], 'Chat') !== false) $tarifas['chat_consulta'] = $row['precio_unitario'];
            }
            
            $p_vista = (float)($tarifas['ver_detalle'] ?? 0.10);
            $p_click = (float)($tarifas['click_aplicar'] ?? 0.15);
            $p_chat  = (float)($tarifas['chat_consulta'] ?? 0.05);

            // Rango de fechas para optimizar índice (SARGABLE)
            $inicioMes = date('Y-m-01 00:00:00');
            $finMes = date('Y-m-t 23:59:59');

            $stmtPeaje = $this->dbRead->prepare("
                SELECT SUM(
                    CASE 
                        WHEN iv.tipo_interaccion = 'ver_detalle' THEN ? 
                        WHEN iv.tipo_interaccion = 'click_aplicar' THEN ? 
                        WHEN iv.tipo_interaccion = 'chat_consulta' THEN ? 
                        ELSE 0 
                    END
                ) as total_consumo
                FROM interacciones_vacante iv
                JOIN vacantes v ON iv.vacante_id = v.id
                WHERE v.empresa_id = ? 
                AND iv.fecha_hora >= ? AND iv.fecha_hora <= ?
            ");
            $stmtPeaje->execute([$p_vista, $p_click, $p_chat, $empresaId, $inicioMes, $finMes]);
            $resultado = $stmtPeaje->fetch(PDO::FETCH_ASSOC);

            $consumoActual = (float) ($resultado['total_consumo'] ?? 0);
        } catch (\Exception $e) {
            // Silencioso para no romper dashboard, loguear error
            error_log("Error calculando consumo: " . $e->getMessage());
            $consumoActual = 0.00;
        }

        $this->view('dashboard/empresa', compact(
            'user', 'vacantesActivas', 'totalCandidatos', 
            'facturacionInfo', 'actividadReciente', 'consumoActual'
        ));
    }

    // ============ GESTIÓN DE PERFIL (CON NOTIFICACIÓN) ============
    public function perfil(): void
    {
        $user = Auth::user();
        
        $stmt = $this->dbRead->prepare("
            SELECT e.*, u.nombre as user_nombre, u.apellido as user_apellido, u.email as user_email
            FROM empresas e
            JOIN usuarios u ON u.empresa_id = e.id
            WHERE e.id = ? AND u.id = ?
        ");
        $stmt->execute([$user['empresa_id'], $user['id']]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updatePerfil($user['empresa_id'], $user['id']);
            return;
        }

        $this->view('empresa/perfil', compact('data'));
    }

    private function updatePerfil($empresaId, $userId): void
    {
        try {
            $this->dbWrite->beginTransaction();

            // Actualizar Empresa
            $stmtEmp = $this->dbWrite->prepare("
                UPDATE empresas SET 
                direccion = ?, telefono = ?, email_contacto = ?, sitio_web = ?
                WHERE id = ?
            ");
            $stmtEmp->execute([
                $_POST['direccion'], $_POST['telefono'], 
                $_POST['email_contacto'], $_POST['sitio_web'], 
                $empresaId
            ]);

            // Actualizar Usuario
            $stmtUser = $this->dbWrite->prepare("
                UPDATE usuarios SET nombre = ?, apellido = ? WHERE id = ?
            ");
            $stmtUser->execute([$_POST['user_nombre'], $_POST['user_apellido'], $userId]);

            $this->dbWrite->commit();
            
            // Actualizar sesión del usuario
            $_SESSION['user']['nombre'] = $_POST['user_nombre'];
            $_SESSION['user']['apellido'] = $_POST['user_apellido'];

            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => '✅ Información de la empresa actualizada correctamente.'];

        } catch (\Exception $e) {
            $this->dbWrite->rollBack();
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => '❌ Error al guardar: ' . $e->getMessage()];
        }

        header('Location: ' . ENV_APP['BASE_URL'] . '/empresa/perfil');
        exit;
    }

    // ============ GESTIÓN DE VACANTES (CON NOTIFICACIÓN) ============
    public function vacantes(): void {
        $user = Auth::user();
        $stmt = $this->dbRead->prepare("SELECT * FROM vacantes WHERE empresa_id = ? ORDER BY fecha_publicacion DESC");
        $stmt->execute([$user['empresa_id']]);
        $vacantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->view('vacantes/index', compact('vacantes'));
    }

    public function crearVacante(): void {
        $this->view('vacantes/form', ['modo' => 'crear']);
    }

    public function storeVacante(): void {
        $user = Auth::user();
        $titulo = $_POST['titulo'] ?? ''; 
        $desc = $_POST['descripcion'] ?? ''; 
        
        if(empty($titulo)) { 
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => '❌ El título es obligatorio.'];
            header('Location: '.ENV_APP['BASE_URL'].'/empresa/vacantes/crear'); 
            exit; 
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $titulo))) . '-' . time();
        
        try {

            $sql = "INSERT INTO vacantes (empresa_id, titulo, slug, descripcion, tipo_contrato, ubicacion, modalidad, salario_min, salario_max, estado, fecha_publicacion, cantidad_plazas, fecha_cierre, costo_por_vista) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'abierta', NOW(), ?, ?, ?)";
            $stmt = $this->dbWrite->prepare($sql);
            $stmt->execute([
                $user['empresa_id'], $titulo, $slug, $desc, 
                $_POST['tipo_contrato']??'', $_POST['ubicacion']??'', $_POST['modalidad']??'presencial', 
                !empty($_POST['salario_min'])?$_POST['salario_min']:null, 
                !empty($_POST['salario_max'])?$_POST['salario_max']:null,
                $_POST['cantidad_plazas'] ?? 1,
                $_POST['fecha_cierre'] ?? null,
                $_POST['costo_por_vista'] ?? 1.00
            ]);

            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => '✅ Nueva vacante publicada con éxito.'];

        } catch (\Exception $e) {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => '❌ Error al publicar: ' . $e->getMessage()];
        }
        
        header('Location: ' . ENV_APP['BASE_URL'] . '/empresa/vacantes');
        exit;
    }

    public function editarVacante($id): void {
        $user = Auth::user();
        $stmt = $this->dbRead->prepare("SELECT * FROM vacantes WHERE id = ? AND empresa_id = ?");
        $stmt->execute([(int)$id, $user['empresa_id']]);
        $vacante = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$vacante) { header('Location: '.ENV_APP['BASE_URL'].'/empresa/vacantes'); exit; }
        $this->view('vacantes/form', ['modo' => 'editar', 'vacante' => $vacante]);
    }

    public function updateVacante($id): void {
        try {

            $sql = "UPDATE vacantes SET titulo=?, descripcion=?, tipo_contrato=?, ubicacion=?, modalidad=?, salario_min=?, salario_max=?, cantidad_plazas=?, fecha_cierre=?, costo_por_vista=? WHERE id=?";
            $stmt = $this->dbWrite->prepare($sql);
            $stmt->execute([
                $_POST['titulo'], $_POST['descripcion'], $_POST['tipo_contrato'], 
                $_POST['ubicacion'], $_POST['modalidad'], 
                !empty($_POST['salario_min'])?$_POST['salario_min']:null, 
                !empty($_POST['salario_max'])?$_POST['salario_max']:null, 
                $_POST['cantidad_plazas'] ?? 1,
                $_POST['fecha_cierre'] ?? null,
                $_POST['costo_por_vista'] ?? 1.00,
                (int)$id
            ]);

            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => '✅ Vacante actualizada correctamente.'];

        } catch (\Exception $e) {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => '❌ Error al actualizar: ' . $e->getMessage()];
        }

        header('Location: ' . ENV_APP['BASE_URL'] . '/empresa/vacantes');
        exit;
    }

    // ============ CANDIDATOS Y POSTULANTES ============
    public function candidatos(): void {
        header('Location: ' . ENV_APP['BASE_URL'] . '/empresa/postulantes');
        exit;
    }

    public function postulantes(): void {
        $user = Auth::user();
        // Agrupar por vacante
        $stmt = $this->dbRead->prepare("
            SELECT v.id, v.titulo, COUNT(p.id) as total_postulantes
            FROM vacantes v
            LEFT JOIN postulaciones p ON v.id = p.vacante_id
            WHERE v.empresa_id = ?
            GROUP BY v.id, v.titulo
            ORDER BY v.fecha_publicacion DESC
        ");
        $stmt->execute([$user['empresa_id']]);
        $vacantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $selectedVacante = $_GET['vacante_id'] ?? ($vacantes[0]['id'] ?? null);
        $candidatos = [];

        if ($selectedVacante) {
            $stmtCand = $this->dbRead->prepare("
                SELECT p.*, s.nombre, s.apellido, s.email, s.telefono, s.cv_ruta, s.habilidades, s.ciudad
                FROM postulaciones p
                JOIN solicitantes s ON p.solicitante_id = s.id
                WHERE p.vacante_id = ?
                ORDER BY p.fecha_postulacion DESC
            ");
            $stmtCand->execute([$selectedVacante]);
            $candidatos = $stmtCand->fetchAll(PDO::FETCH_ASSOC);
        }

        $this->view('empresa/postulantes', compact('vacantes', 'candidatos', 'selectedVacante'));
    }

    // ============ FACTURACIÓN Y TÉRMINOS ============
    public function facturacion(): void {
        $user = Auth::user();

        // Consultar facturas de ESTA empresa
        $stmt = $this->dbRead->prepare("
            SELECT * FROM facturas 
            WHERE empresa_id = ? 
            ORDER BY fecha_emision DESC
        ");
        $stmt->execute([$user['empresa_id']]);
        $facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calcular deuda pendiente
        $deuda = 0;
        foreach($facturas as $f) {
            if($f['estado'] === 'emitida') $deuda += $f['total'];
        }

        $this->view('empresa/facturacion', compact('facturas', 'deuda'));
    }

    public function aceptarContrato(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = Auth::user();
            $ip = $_SERVER['REMOTE_ADDR'];
            $texto = "Aceptación Digital de Términos v1.0. IP: $ip. Fecha: " . date('Y-m-d H:i:s');

            $stmt = $this->dbWrite->prepare("INSERT INTO contratos_empresas (empresa_id, version_contrato, ip_aceptacion, texto_resumen, estado) VALUES (?, 'v1.0', ?, ?, 'vigente')");
            $stmt->execute([$user['empresa_id'], $ip, $texto]);
            
            header('Location: ' . ENV_APP['BASE_URL'] . '/empresa/dashboard');
            exit;
        }
        header('Location: ' . ENV_APP['BASE_URL'] . '/empresa/dashboard');
        exit;
    }
    // ============ CAMBIAR ESTADO DE POSTULACIÓN ============
    public function cambiarEstadoPostulacion(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ENV_APP['BASE_URL'] . '/empresa/dashboard');
            exit;
        }

        $postulacion_id = $_POST['postulacion_id'] ?? 0;
        $vacante_id = $_POST['vacante_id'] ?? 0;
        $nuevo_estado = $_POST['nuevo_estado'] ?? '';

        if (!$postulacion_id || !in_array($nuevo_estado, ['aceptado', 'rechazado'])) {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Datos inválidos.'];
            header("Location: " . ENV_APP['BASE_URL'] . "/empresa/postulantes?vacante_id=$vacante_id");
            exit;
        }

        try {
            $stmt = $this->dbWrite->prepare("UPDATE postulaciones SET estado = ? WHERE id = ?");
            $stmt->execute([$nuevo_estado, $postulacion_id]);

            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => "Candidato marcado como " . strtoupper($nuevo_estado)];
        } catch (\Exception $e) {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al actualizar: ' . $e->getMessage()];
        }

        header("Location: " . ENV_APP['BASE_URL'] . "/empresa/postulantes?vacante_id=$vacante_id");
        exit;
    }   

}
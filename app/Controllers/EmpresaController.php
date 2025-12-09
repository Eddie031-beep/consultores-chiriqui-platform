<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Auth;
use PDO;

class EmpresaController extends Controller
{
    private PDO $dbWrite; // Conexión Maestro (INSERT/UPDATE)
    private PDO $dbRead;  // Conexión Réplica (SELECT)

    public function __construct()
    {
        // 1. INICIALIZAR DOBLE CONEXIÓN (Requisito de Infraestructura)
        $this->dbWrite = db_connect('write');
        $this->dbRead  = db_connect('read');
        
        if (!Auth::check() || Auth::user()['rol'] !== 'empresa_admin') {
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/login?tipo=empresa');
            exit;
        }
    }

    // ============ DASHBOARD CON PRECIOS DINÁMICOS ============
    public function dashboard(): void
    {
        $user = Auth::user();
        $empresaId = $user['empresa_id'];

        // Usamos dbRead para consultas (Balanceo de carga)
        
        // 1. Vacantes
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

        // 3. CONSUMO DINÁMICO (Requisito de BD)
        $consumoActual = 0.0;
        try {
            // Traemos precios de la tabla, no fijos
            $stmtTarifas = $this->dbRead->query("SELECT nombre_plan, precio_unitario FROM peajes_tarifas WHERE activo = 1");
            $tarifas = [];
            if ($stmtTarifas) {
                while ($row = $stmtTarifas->fetch(PDO::FETCH_ASSOC)) {
                    if (stripos($row['nombre_plan'], 'Vista') !== false) $tarifas['ver_detalle'] = $row['precio_unitario'];
                    if (stripos($row['nombre_plan'], 'Click') !== false) $tarifas['click_aplicar'] = $row['precio_unitario'];
                    if (stripos($row['nombre_plan'], 'Chat') !== false) $tarifas['chat_consulta'] = $row['precio_unitario'];
                }
            }
            
            $p_vista = (float)($tarifas['ver_detalle'] ?? 0.10);
            $p_click = (float)($tarifas['click_aplicar'] ?? 0.15);
            $p_chat  = (float)($tarifas['chat_consulta'] ?? 0.05);

            $stmtPeaje = $this->dbRead->prepare("
                SELECT SUM(CASE 
                    WHEN tipo_interaccion = 'ver_detalle' THEN ? 
                    WHEN tipo_interaccion = 'click_aplicar' THEN ? 
                    WHEN tipo_interaccion = 'chat_consulta' THEN ? 
                    ELSE 0 END) as total_consumo
                FROM interacciones_vacante iv
                JOIN vacantes v ON iv.vacante_id = v.id
                WHERE v.empresa_id = ? AND MONTH(iv.fecha_hora) = MONTH(CURRENT_DATE())
            ");
            $stmtPeaje->execute([$p_vista, $p_click, $p_chat, $empresaId]);
            $resultado = $stmtPeaje->fetch(PDO::FETCH_ASSOC);

            // Forzamos a float para evitar errores en number_format
            $consumoActual = (float) ($resultado['total_consumo'] ?? 0);
        } catch (\Exception $e) {
            // Fail silently or log error, but don't break dashboard
            error_log("Error calculando consumo: " . $e->getMessage());
            $consumoActual = 0.00;
        }

        // 4. Actividad
        $stmtAct = $this->dbRead->prepare("
            SELECT p.fecha_postulacion, s.nombre, s.apellido, v.titulo 
            FROM postulaciones p 
            JOIN solicitantes s ON p.solicitante_id = s.id 
            JOIN vacantes v ON p.vacante_id = v.id 
            WHERE v.empresa_id = ? 
            ORDER BY p.fecha_postulacion DESC 
            LIMIT 5
        ");
        $stmtAct->execute([$empresaId]);
        $actividadReciente = $stmtAct->fetchAll(PDO::FETCH_ASSOC);

        // 5. Verificar Contrato
        $stmtContrato = $this->dbRead->prepare("SELECT id FROM contratos_empresas WHERE empresa_id = ? AND estado = 'vigente'");
        $stmtContrato->execute([$empresaId]);
        $contratoAceptado = $stmtContrato->fetch() ? true : false;

        // 6. DATOS MOCK PARA REDISEÑO (Solicitud Frontend)
        $reputacion = 4.5;
        $valoraciones = [
            [
                'autor' => 'Candidato Anónimo',
                'fecha' => 'Hace 2 días',
                'estrellas' => 5,
                'comentario' => 'Excelente proceso de selección, muy rápidos en contestar.'
            ],
            [
                'autor' => 'Juan Pérez',
                'fecha' => 'Hace 1 semana',
                'estrellas' => 3,
                'comentario' => 'La entrevista fue buena pero el salario no era el publicado.'
            ]
        ];

        $this->view('dashboard/empresa', compact(
            'user', 'vacantesActivas', 'totalCandidatos', 'consumoActual', 
            'actividadReciente', 'contratoAceptado', 'reputacion', 'valoraciones'
        ));
    }

    // ============ ACEPTAR CONTRATO ============
    public function aceptarContrato(): void
    {
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

    // ============ OTROS MÉTODOS ============
    public function vacantes(): void {
        $user = Auth::user();
        $stmt = $this->dbRead->prepare("SELECT * FROM vacantes WHERE empresa_id = ? ORDER BY fecha_publicacion DESC");
        $stmt->execute([$user['empresa_id']]);
        $vacantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->view('vacantes/index', compact('vacantes', 'user'));
    }

    public function crearVacante(): void {
        $this->view('vacantes/form', ['modo' => 'crear']);
    }

    public function storeVacante(): void {
        $user = Auth::user();
        $titulo = $_POST['titulo'] ?? ''; 
        $desc = $_POST['descripcion'] ?? ''; 
        
        if(empty($titulo)) { header('Location: '.ENV_APP['BASE_URL'].'/empresa/vacantes/crear'); exit; }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $titulo))) . '-' . time();
        
        $sql = "INSERT INTO vacantes (empresa_id, titulo, slug, descripcion, tipo_contrato, ubicacion, modalidad, salario_min, salario_max, estado, fecha_publicacion, cantidad_plazas) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'abierta', NOW(), 0)";
        $stmt = $this->dbWrite->prepare($sql);
        $stmt->execute([
            $user['empresa_id'], $titulo, $slug, $desc, 
            $_POST['tipo_contrato']??'', $_POST['ubicacion']??'', $_POST['modalidad']??'presencial', 
            !empty($_POST['salario_min'])?$_POST['salario_min']:null, 
            !empty($_POST['salario_max'])?$_POST['salario_max']:null
        ]);
        
        header('Location: ' . ENV_APP['BASE_URL'] . '/empresa/vacantes');
        exit;
    }

    public function editarVacante($id): void {
        $user = Auth::user();
        $stmt = $this->dbRead->prepare("SELECT * FROM vacantes WHERE id = ? AND empresa_id = ?");
        $stmt->execute([(int)$id, $user['empresa_id']]);
        $vacante = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$vacante) { header('Location: '.ENV_APP['BASE_URL'].'/empresa/vacantes'); exit; }
        $this->view('vacantes/form', ['modo' => 'editar', 'vacante' => $vacante]);
    }

    public function updateVacante($id): void {
        $sql = "UPDATE vacantes SET titulo=?, descripcion=?, tipo_contrato=?, ubicacion=?, modalidad=?, salario_min=?, salario_max=? WHERE id=?";
        $stmt = $this->dbWrite->prepare($sql);
        $stmt->execute([
            $_POST['titulo'], $_POST['descripcion'], $_POST['tipo_contrato'], 
            $_POST['ubicacion'], $_POST['modalidad'], 
            !empty($_POST['salario_min'])?$_POST['salario_min']:null, 
            !empty($_POST['salario_max'])?$_POST['salario_max']:null, 
            (int)$id
        ]);
        header('Location: ' . ENV_APP['BASE_URL'] . '/empresa/vacantes');
        exit;
    }
    
    public function candidatos(): void {
        $user = Auth::user();
        $stmt = $this->dbRead->prepare("SELECT p.*, v.titulo as vacante_titulo, s.nombre, s.apellido, s.email, s.telefono FROM postulaciones p JOIN vacantes v ON p.vacante_id = v.id JOIN solicitantes s ON p.solicitante_id = s.id WHERE v.empresa_id = ? ORDER BY p.fecha_postulacion DESC");
        $stmt->execute([$user['empresa_id']]);
        $candidatos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->view('empresa/candidatos', compact('candidatos', 'user'));
    }
    
    public function postulantes(): void {
        $user = Auth::user();
        // Agrupar por vacante
        $stmt = $this->dbRead->prepare("
            SELECT v.id as vacante_id, v.titulo, COUNT(p.id) as cantidad_postulantes 
            FROM vacantes v 
            LEFT JOIN postulaciones p ON v.id = p.vacante_id 
            WHERE v.empresa_id = ? 
            GROUP BY v.id, v.titulo 
            ORDER BY cantidad_postulantes DESC
        ");
        $stmt->execute([$user['empresa_id']]);
        $vacantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Si se selecciona una vacante, traer sus postulantes
        $selectedVacante = $_GET['vacante_id'] ?? null;
        $detalles = [];
        
        if ($selectedVacante) {
            $stmtDet = $this->dbRead->prepare("
                SELECT p.*, s.nombre, s.apellido, s.email, s.telefono, s.habilidades,cv_path
                FROM postulaciones p 
                JOIN solicitantes s ON p.solicitante_id = s.id 
                WHERE p.vacante_id = ? 
                ORDER BY p.fecha_postulacion DESC
            ");
            $stmtDet->execute([$selectedVacante]);
            $detalles = $stmtDet->fetchAll(PDO::FETCH_ASSOC);
        }

        $this->view('empresa/postulantes', compact('vacantes', 'detalles', 'selectedVacante'));
    }

    public function facturacion(): void {
        $user = Auth::user();
        $stmt = $this->dbRead->prepare("
            SELECT f.*, 
            CASE WHEN f.estado = 'emitida' THEN 1 ELSE 2 END as orden_estado
            FROM facturas f 
            WHERE f.empresa_id = ? 
            ORDER BY orden_estado ASC, f.fecha_emision DESC
        ");
        $stmt->execute([$user['empresa_id']]);
        $facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('empresa/facturacion', compact('facturas'));
    }
}
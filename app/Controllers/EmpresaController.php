<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Auth;
use PDO;

class EmpresaController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db_connect('local');
        
        if (!Auth::check() || Auth::user()['rol'] !== 'empresa_admin') {
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/login?tipo=empresa');
            exit;
        }
    }

    // ============ DASHBOARD PRINCIPAL ============
    public function dashboard(): void
    {
        $user = Auth::user();
        $empresaId = $user['empresa_id'];

        // 1. Total Vacantes Activas
        $stmtVac = $this->db->prepare("SELECT COUNT(*) as total FROM vacantes WHERE empresa_id = ? AND estado = 'abierta'");
        $stmtVac->execute([$empresaId]);
        $vacantesActivas = $stmtVac->fetch(PDO::FETCH_ASSOC)['total'];

        // 2. Total Candidatos (Postulaciones únicas)
        $stmtCand = $this->db->prepare("
            SELECT COUNT(DISTINCT solicitante_id) as total 
            FROM postulaciones p 
            JOIN vacantes v ON p.vacante_id = v.id 
            WHERE v.empresa_id = ?
        ");
        $stmtCand->execute([$empresaId]);
        $totalCandidatos = $stmtCand->fetch(PDO::FETCH_ASSOC)['total'];

        // 3. Cálculo de Consumo (Peaje por interacción)
        // Según el PDF, se cobra por interacción. Asumimos precios base si no están en DB.
        $stmtPeaje = $this->db->prepare("
            SELECT 
                SUM(CASE 
                    WHEN tipo_interaccion = 'ver_detalle' THEN 0.10 
                    WHEN tipo_interaccion = 'click_aplicar' THEN 0.15 
                    WHEN tipo_interaccion = 'chat_consulta' THEN 0.05 
                    ELSE 0 
                END) as total_consumo
            FROM interacciones_vacante iv
            JOIN vacantes v ON iv.vacante_id = v.id
            WHERE v.empresa_id = ? AND MONTH(iv.fecha_hora) = MONTH(CURRENT_DATE())
        ");
        $stmtPeaje->execute([$empresaId]);
        $consumoActual = $stmtPeaje->fetch(PDO::FETCH_ASSOC)['total_consumo'] ?? 0.00;

        // 4. Actividad Reciente (Últimas 5 postulaciones)
        $stmtAct = $this->db->prepare("
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

        $this->view('dashboard/empresa', compact('user', 'vacantesActivas', 'totalCandidatos', 'consumoActual', 'actividadReciente'));
    }

    // ============ LISTAR VACANTES ============
    public function vacantes(): void
    {
        $user = Auth::user();
        
        // Obtener vacantes de la empresa actual
        $stmt = $this->db->prepare("
            SELECT * FROM vacantes 
            WHERE empresa_id = ? 
            ORDER BY fecha_publicacion DESC
        ");
        $stmt->execute([$user['empresa_id']]);
        $vacantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('vacantes/index', compact('vacantes', 'user'));
    }

    // ============ CREAR VACANTE (VISTA) ============
    public function crearVacante(): void
    {
        $this->view('vacantes/form', ['modo' => 'crear']);
    }

    // ============ GUARDAR VACANTE (POST) ============
    public function storeVacante(): void
    {
        $user = Auth::user();
        
        $titulo = trim($_POST['titulo'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $tipo_contrato = trim($_POST['tipo_contrato'] ?? '');
        $ubicacion = trim($_POST['ubicacion'] ?? '');
        $modalidad = $_POST['modalidad'] ?? 'presencial';
        $salario_min = !empty($_POST['salario_min']) ? $_POST['salario_min'] : null;
        $salario_max = !empty($_POST['salario_max']) ? $_POST['salario_max'] : null;

        // Validaciones básicas
        $errores = [];
        if (empty($titulo)) $errores['titulo'] = 'El título es obligatorio';
        if (empty($descripcion)) $errores['descripcion'] = 'La descripción es obligatoria';
        if (empty($ubicacion)) $errores['ubicacion'] = 'La ubicación es obligatoria';

        if (!empty($errores)) {
            $this->view('vacantes/form', [
                'modo' => 'crear',
                'errores' => $errores,
                'old' => $_POST
            ]);
            return;
        }

        // Generar slug único
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $titulo)));
        $slug .= '-' . time(); 

        try {
            // Insertar vacante con cantidad_plazas (si aplicaste el parche anterior) o sin él
            // Nota: Asumo que ya aplicaste el parche de cantidad_plazas, si no, quítalo del query
            $sql = "INSERT INTO vacantes 
                    (empresa_id, titulo, slug, descripcion, tipo_contrato, ubicacion, modalidad, salario_min, salario_max, estado, fecha_publicacion, cantidad_plazas)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'abierta', NOW(), 0)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $user['empresa_id'], $titulo, $slug, $descripcion, $tipo_contrato, 
                $ubicacion, $modalidad, $salario_min, $salario_max
            ]);

            header('Location: ' . ENV_APP['BASE_URL'] . '/empresa/vacantes');
            exit;

        } catch (\PDOException $e) {
            // Manejo de error (ej. slug duplicado o error de BD)
            $errores['general'] = 'Error al guardar: ' . $e->getMessage();
            $this->view('vacantes/form', [
                'modo' => 'crear',
                'errores' => $errores,
                'old' => $_POST
            ]);
        }
    }

    // ============ EDITAR VACANTE (VISTA) ============
    public function editarVacante($id): void
    {
        $user = Auth::user();
        $id = (int)$id;

        // Verificar que la vacante pertenezca a la empresa
        $stmt = $this->db->prepare("SELECT * FROM vacantes WHERE id = ? AND empresa_id = ?");
        $stmt->execute([$id, $user['empresa_id']]);
        $vacante = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$vacante) {
            header('Location: ' . ENV_APP['BASE_URL'] . '/empresa/vacantes');
            exit;
        }

        $this->view('vacantes/form', ['modo' => 'editar', 'vacante' => $vacante]);
    }

    // ============ ACTUALIZAR VACANTE (POST) ============
    public function updateVacante($id): void
    {
        $user = Auth::user();
        $id = (int)$id;

        // Verificar propiedad
        $stmtCheck = $this->db->prepare("SELECT id FROM vacantes WHERE id = ? AND empresa_id = ?");
        $stmtCheck->execute([$id, $user['empresa_id']]);
        if (!$stmtCheck->fetch()) {
            header('Location: ' . ENV_APP['BASE_URL'] . '/empresa/vacantes');
            exit;
        }

        $titulo = trim($_POST['titulo'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $tipo_contrato = trim($_POST['tipo_contrato'] ?? '');
        $ubicacion = trim($_POST['ubicacion'] ?? '');
        $modalidad = $_POST['modalidad'] ?? 'presencial';
        $salario_min = !empty($_POST['salario_min']) ? $_POST['salario_min'] : null;
        $salario_max = !empty($_POST['salario_max']) ? $_POST['salario_max'] : null;

        $sql = "UPDATE vacantes SET 
                titulo = ?, descripcion = ?, tipo_contrato = ?, ubicacion = ?, 
                modalidad = ?, salario_min = ?, salario_max = ?
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $titulo, $descripcion, $tipo_contrato, $ubicacion, 
            $modalidad, $salario_min, $salario_max, $id
        ]);

        header('Location: ' . ENV_APP['BASE_URL'] . '/empresa/vacantes');
        exit;
    }

    // ============ VER CANDIDATOS (Placeholder) ============
    // Ruta definida en routes: '/empresa/candidatos'
    public function candidatos(): void
    {
        $user = Auth::user();
        
        // Obtener postulaciones a vacantes de esta empresa
        $sql = "SELECT p.*, v.titulo as vacante_titulo, s.nombre, s.apellido, s.email, s.cv_ruta
                FROM postulaciones p
                JOIN vacantes v ON p.vacante_id = v.id
                JOIN solicitantes s ON p.solicitante_id = s.id
                WHERE v.empresa_id = ?
                ORDER BY p.fecha_postulacion DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user['empresa_id']]);
        $candidatos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Puedes crear una vista 'empresas/candidatos.php' o reutilizar alguna existente
        // Por ahora, si no tienes la vista, podrías mostrar un dump o redirigir
        // $this->view('empresas/candidatos', compact('candidatos')); 
        
        // Si no existe la vista, evita el error 500 mostrando algo básico:
        echo "<h1>Candidatos Postulados</h1>";
        echo "<pre>"; print_r($candidatos); echo "</pre>";
        echo "<a href='".ENV_APP['BASE_URL']."/empresa/dashboard'>Volver</a>";
    }
    
    // Método adicional para cerrar vacante (usado en tu vista index.php)
    public function cerrarVacante(): void 
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = Auth::user();
            $id = (int)($_POST['id'] ?? 0);
            
            $stmt = $this->db->prepare("UPDATE vacantes SET estado = 'cerrada' WHERE id = ? AND empresa_id = ?");
            $stmt->execute([$id, $user['empresa_id']]);
            
            header('Location: ' . ENV_APP['BASE_URL'] . '/empresa/vacantes');
            exit;
        }
    }
    // Agregar método placeholder para facturación si no existe
    public function facturacion(): void {
        $user = Auth::user();
        // Aquí iría la lógica para mostrar el historial de facturas
        echo "<h1>Historial de Facturación</h1><p>Módulo en construcción según requerimientos DGI.</p>";
        echo "<a href='".ENV_APP['BASE_URL']."/empresa/dashboard'>Volver</a>";
    }
}
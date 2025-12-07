<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Auth;
use PDO;

class CandidatoController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db_connect('local');
        
        // Proteger rutas
        if (!Auth::check() || Auth::user()['rol'] !== 'candidato') {
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/login?tipo=persona');
            exit;
        }
    }

    // ============ DASHBOARD EXISTENTE ============
    public function dashboard(): void
    {
        $user = Auth::user();
        
        $stmt = $this->db->prepare("
            SELECT p.*, v.titulo, v.empresa_id, e.nombre as empresa_nombre
            FROM postulaciones p
            JOIN vacantes v ON p.vacante_id = v.id
            JOIN empresas e ON v.empresa_id = e.id
            WHERE p.solicitante_id = ?
            ORDER BY p.fecha_postulacion DESC
        ");
        $stmt->execute([$user['id']]);
        $postulaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('dashboard/candidato', compact('postulaciones'));
    }

    // ============ NUEVA LÓGICA: OPCIONES DE PERFIL ============
    public function opcionesPerfil(): void
    {
        // Vista de selección: "¿Subir CV o Llenar Manual?"
        $this->view('candidato/opciones-perfil');
    }

    // PROCESAR SUBIDA DE CV (Opción 1)
    public function subirCV(): void
    {
        $user = Auth::user();

        if (isset($_FILES['cv_archivo']) && $_FILES['cv_archivo']['error'] === UPLOAD_ERR_OK) {
            // Crear carpeta si no existe
            $uploadDir = __DIR__ . '/../../public/uploads/cvs/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            // Generar nombre único
            $ext = strtolower(pathinfo($_FILES['cv_archivo']['name'], PATHINFO_EXTENSION));
            $fileName = 'cv_' . $user['id'] . '_' . time() . '.' . $ext;
            $destPath = $uploadDir . $fileName;

            // Validaciones básicas
            if (!in_array($ext, ['pdf', 'doc', 'docx'])) {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Solo archivos PDF o Word'];
                header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/opciones-perfil');
                exit;
            }

            if (move_uploaded_file($_FILES['cv_archivo']['tmp_name'], $destPath)) {
                // Guardar en BD
                $stmt = $this->db->prepare("UPDATE solicitantes SET cv_ruta = ?, perfil_completado = 1 WHERE id = ?");
                $stmt->execute(['uploads/cvs/' . $fileName, $user['id']]);

                $_SESSION['message'] = ['type' => 'success', 'text' => '¡Hoja de vida cargada exitosamente!'];
                header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/dashboard');
                exit;
            }
        }
        
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Error al subir el archivo'];
        header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/opciones-perfil');
    }

    // FORMULARIO MANUAL (Opción 2 - Estilo Konzerta)
    public function perfilManual(): void
    {
        $user = Auth::user();
        
        // Obtener datos actuales del perfil
        $stmt = $this->db->prepare("SELECT * FROM solicitantes WHERE id = ?");
        $stmt->execute([$user['id']]);
        $perfil = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->view('candidato/formulario-manual', compact('perfil'));
    }

    // GUARDAR FORMULARIO MANUAL
    public function guardarManual(): void
    {
        $user = Auth::user();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recopilar datos
            $nombre = $_POST['nombre'] ?? '';
            $apellido = $_POST['apellido'] ?? '';
            $nacionalidad = $_POST['nacionalidad'] ?? '';
            $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
            $estado_civil = $_POST['estado_civil'] ?? '';
            $genero = $_POST['genero'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $telefono_sec = $_POST['telefono_secundario'] ?? '';
            $pais = $_POST['pais'] ?? '';
            $provincia = $_POST['provincia'] ?? '';
            $ciudad = $_POST['ciudad'] ?? '';
            $direccion = $_POST['direccion'] ?? '';

            // Actualizar BD
            $sql = "UPDATE solicitantes SET 
                    nombre = ?, apellido = ?, nacionalidad = ?, 
                    fecha_nacimiento = ?, estado_civil = ?, genero = ?,
                    telefono = ?, telefono_secundario = ?,
                    pais = ?, provincia = ?, ciudad = ?, direccion = ?,
                    perfil_completado = 1
                    WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $nombre, $apellido, $nacionalidad, 
                $fecha_nacimiento ?: null, $estado_civil, $genero,
                $telefono, $telefono_sec,
                $pais, $provincia, $ciudad, $direccion,
                $user['id']
            ]);

            $_SESSION['message'] = ['type' => 'success', 'text' => 'Perfil actualizado correctamente'];
            header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/dashboard');
            exit;
        }
    }

    // ============ FUNCIONES EXISTENTES (Mantener) ============
    public function postulaciones(): void {
        $user = Auth::user();
        $stmt = $this->db->prepare("
            SELECT p.*, v.titulo, v.slug, v.empresa_id, e.nombre as empresa_nombre, v.modalidad, v.ubicacion
            FROM postulaciones p
            JOIN vacantes v ON p.vacante_id = v.id
            JOIN empresas e ON v.empresa_id = e.id
            WHERE p.solicitante_id = ?
            ORDER BY p.fecha_postulacion DESC
        ");
        $stmt->execute([$user['id']]);
        $postulaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->view('dashboard/postulaciones', compact('postulaciones'));
    }

    public function perfil(): void {
        $this->perfilManual(); // Reutilizamos la vista manual para editar perfil
    }

    public function postular($vacante_id): void {
        $user = Auth::user();
        
        // 1. Verificar si ya se postuló
        $stmtCheck = $this->db->prepare("SELECT id FROM postulaciones WHERE vacante_id = ? AND solicitante_id = ?");
        $stmtCheck->execute([$vacante_id, $user['id']]);
        
        if ($stmtCheck->fetch()) {
            $_SESSION['message'] = ['type' => 'warning', 'text' => 'Ya te has postulado a esta vacante anteriormente.'];
            header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/postulaciones');
            exit;
        }

        // 2. Insertar postulación
        $stmt = $this->db->prepare("
            INSERT INTO postulaciones (vacante_id, solicitante_id, fecha_postulacion, estado) 
            VALUES (?, ?, NOW(), 'pendiente')
        ");
        
        try {
            $stmt->execute([$vacante_id, $user['id']]);
            
            // 3. Registrar interacción
            $ip = $_SERVER['REMOTE_ADDR'];
            $sid = session_id();
            $log = $this->db->prepare("INSERT INTO interacciones_vacante (vacante_id, tipo_interaccion, origen, ip, session_id) VALUES (?, 'aplicacion_exitosa', 'web', ?, ?)");
            $log->execute([$vacante_id, $ip, $sid]);

            $_SESSION['message'] = ['type' => 'success', 'text' => '¡Te has postulado exitosamente! La empresa revisará tu perfil.'];
            header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/postulaciones');
            exit;
        } catch (\PDOException $e) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Error al procesar la postulación. Intenta nuevamente.'];
            header('Location: ' . ENV_APP['BASE_URL'] . '/vacantes/' . $vacante_id); // Fallback redirection
            exit;
        }
    }
}
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
        
        if (!Auth::check() || Auth::user()['rol'] !== 'candidato') {
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/login?tipo=persona');
            exit;
        }
    }

    public function dashboard(): void
    {
        $user = Auth::user();
        
        $stmtCheck = $this->db->prepare("SELECT perfil_completado FROM solicitantes WHERE id = ?");
        $stmtCheck->execute([$user['id']]);
        $completo = $stmtCheck->fetchColumn();

        if (!$completo) {
            $_SESSION['message'] = ['type' => 'warning', 'text' => 'Por favor completa tu perfil o sube tu CV para continuar.'];
            header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/opciones-perfil');
            exit;
        }
        
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

    public function editarPerfil(): void
    {
        $user = Auth::user();
        $stmt = $this->db->prepare("SELECT * FROM solicitantes WHERE id = ?");
        $stmt->execute([$user['id']]);
        $perfil = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch Experience and Education
        $expStmt = $this->db->prepare("SELECT * FROM candidato_experiencia WHERE solicitante_id = ? ORDER BY fecha_inicio DESC");
        $expStmt->execute([$user['id']]);
        $experiencia = $expStmt->fetchAll(PDO::FETCH_ASSOC);

        $eduStmt = $this->db->prepare("SELECT * FROM candidato_educacion WHERE solicitante_id = ? ORDER BY fecha_graduacion DESC");
        $stmt->execute([$user['id']]);
        $perfil = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Fetch Experience and Education
        $expStmt = $this->db->prepare("SELECT * FROM candidato_experiencia WHERE solicitante_id = ? ORDER BY fecha_inicio DESC");
        $expStmt->execute([$user['id']]);
        $experiencia = $expStmt->fetchAll(PDO::FETCH_ASSOC);

        $eduStmt = $this->db->prepare("SELECT * FROM candidato_educacion WHERE solicitante_id = ? ORDER BY fecha_graduacion DESC");
        $eduStmt->execute([$user['id']]);
        $educacion = $eduStmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('candidato/formulario-manual', compact('perfil', 'experiencia', 'educacion'));
    }

    public function guardarManual(): void
    {
        $user = Auth::user();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->db->beginTransaction();

                $nombre = $_POST['nombre'] ?? '';
                $apellido = $_POST['apellido'] ?? '';
                $nacionalidad = $_POST['nacionalidad'] ?? '';
                $fecha_nacimiento = $_POST['fecha_nacimiento'] ?: null;
                $genero = $_POST['genero'] ?: null;
                $estado_civil = $_POST['estado_civil'] ?: null;
                $telefono = $_POST['telefono'] ?? '';
                $direccion = $_POST['direccion'] ?? '';
                $habilidades = $_POST['habilidades'] ?? '';

                $sql = "UPDATE solicitantes SET 
                        nombre = ?, apellido = ?, nacionalidad = ?, 
                        fecha_nacimiento = ?, genero = ?, estado_civil = ?,
                        telefono = ?, direccion = ?, habilidades = ?, perfil_completado = 1
                        WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $nombre, $apellido, $nacionalidad, 
                    $fecha_nacimiento, $genero, $estado_civil,
                    $telefono, $direccion, $habilidades, $user['id']
                ]);

                // Experiencia
                $this->db->prepare("DELETE FROM candidato_experiencia WHERE solicitante_id = ?")->execute([$user['id']]);
                if (!empty($_POST['exp_empresa'])) {
                    $stmtExp = $this->db->prepare("INSERT INTO candidato_experiencia (solicitante_id, empresa, puesto, descripcion, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?, ?, ?)");
                    foreach ($_POST['exp_empresa'] as $key => $val) {
                        if (empty($val)) continue;
                        $stmtExp->execute([
                            $user['id'], $val,
                            $_POST['exp_puesto'][$key] ?? '',
                            $_POST['exp_descripcion'][$key] ?? '',
                            $_POST['exp_inicio'][$key] ?: null,
                            $_POST['exp_fin'][$key] ?: null
                        ]);
                    }
                }

                // Educación
                $this->db->prepare("DELETE FROM candidato_educacion WHERE solicitante_id = ?")->execute([$user['id']]);
                if (!empty($_POST['edu_institucion'])) {
                    $stmtEdu = $this->db->prepare("INSERT INTO candidato_educacion (solicitante_id, institucion, titulo, nivel, fecha_graduacion) VALUES (?, ?, ?, ?, ?)");
                    foreach ($_POST['edu_institucion'] as $key => $val) {
                        if (empty($val)) continue;
                        $stmtEdu->execute([
                            $user['id'], $val,
                            $_POST['edu_titulo'][$key] ?? '',
                            $_POST['edu_nivel'][$key] ?? '',
                            $_POST['edu_fecha'][$key] ?: null
                        ]);
                    }
                }

                $this->db->commit();
                
                $_SESSION['user']['nombre'] = $nombre;
                $_SESSION['user']['apellido'] = $apellido;
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Perfil completo actualizado correctamente'];
                header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/dashboard');
                exit;

            } catch (\Exception $e) {
                if ($this->db->inTransaction()) $this->db->rollBack();
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Error al guardar perfil: ' . $e->getMessage()];
                header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/perfil-manual');
                exit;
            }
        }
    }

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

    public function postular($vacante_id): void {
        $user = Auth::user();
        
        $stmtCheck = $this->db->prepare("SELECT id FROM postulaciones WHERE vacante_id = ? AND solicitante_id = ?");
        $stmtCheck->execute([$vacante_id, $user['id']]);
        
        if ($stmtCheck->fetch()) {
            $_SESSION['message'] = ['type' => 'warning', 'text' => 'Ya te has postulado a esta vacante anteriormente.'];
            header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/postulaciones');
            exit;
        }

        $stmt = $this->db->prepare("
            INSERT INTO postulaciones (vacante_id, solicitante_id, fecha_postulacion, estado) 
            VALUES (?, ?, NOW(), 'pendiente')
        ");
        
        try {
            $stmt->execute([$vacante_id, $user['id']]);
            
            $ip = $_SERVER['REMOTE_ADDR'];
            $sid = session_id();
            $log = $this->db->prepare("INSERT INTO interacciones_vacante (vacante_id, tipo_interaccion, origen, ip, session_id) VALUES (?, 'aplicacion_exitosa', 'web', ?, ?)");
            $log->execute([$vacante_id, $ip, $sid]);

            $_SESSION['message'] = ['type' => 'success', 'text' => '¡Te has postulado exitosamente! La empresa revisará tu perfil.'];
            header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/postulaciones');
            exit;
        } catch (\PDOException $e) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Error al procesar la postulación. ' . $e->getMessage()];
            header('Location: ' . ENV_APP['BASE_URL'] . '/vacantes');
            exit;
        }
    }

    public function cancelarPostulacion(): void
    {
        $user = Auth::user();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $vacante_id = $_POST['vacante_id'] ?? null;
            
            if (!$vacante_id) {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'ID de vacante inválido.'];
                header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/postulaciones');
                exit;
            }

            $stmtCheck = $this->db->prepare("SELECT id, estado FROM postulaciones WHERE vacante_id = ? AND solicitante_id = ?");
            $stmtCheck->execute([$vacante_id, $user['id']]);
            $postulacion = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if (!$postulacion) {
                 $_SESSION['message'] = ['type' => 'error', 'text' => 'Postulación no encontrada.'];
            } elseif ($postulacion['estado'] !== 'pendiente') {
                 $_SESSION['message'] = ['type' => 'warning', 'text' => 'No puedes cancelar una postulación que ya ha sido revisada o procesada.'];
            } else {
                $stmtDel = $this->db->prepare("DELETE FROM postulaciones WHERE id = ?");
                if ($stmtDel->execute([$postulacion['id']])) {
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Postulación cancelada correctamente.'];
                } else {
                    $_SESSION['message'] = ['type' => 'error', 'text' => 'Error al eliminar la postulación.'];
                }
            }
            
            $redirect = $_POST['redirect'] ?? '/candidato/postulaciones';
            header('Location: ' . ENV_APP['BASE_URL'] . $redirect);
            exit;
        }
    }
}
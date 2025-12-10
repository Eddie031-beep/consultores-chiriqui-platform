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
        
        // Verificar autenticación
        if (!Auth::check() || Auth::user()['rol'] !== 'candidato') {
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/login?tipo=persona');
            exit;
        }
    }

    public function dashboard(): void
    {
        $user = Auth::user();
        
        // 1. OBTENER PERFIL (Esto faltaba y causaba el error)
        $stmtProfile = $this->db->prepare("SELECT * FROM solicitantes WHERE id = ?");
        $stmtProfile->execute([$user['id']]);
        $perfil = $stmtProfile->fetch(PDO::FETCH_ASSOC);

        // 2. Verificar si completó perfil (Opcional: Si quieres forzarlo, descomenta esto)
        /*
        if (!$perfil['perfil_completado']) {
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'Completa tu perfil para continuar.'];
            header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/opciones-perfil');
            exit;
        }
        */
        
        // 3. Obtener Postulaciones
        $stmt = $this->db->prepare("
            SELECT p.*, v.titulo, v.empresa_id, e.nombre as empresa_nombre, v.modalidad, v.ubicacion
            FROM postulaciones p
            JOIN vacantes v ON p.vacante_id = v.id
            JOIN empresas e ON v.empresa_id = e.id
            WHERE p.solicitante_id = ?
            ORDER BY p.fecha_postulacion DESC
        ");
        $stmt->execute([$user['id']]);
        $postulaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 4. Enviar datos a la vista (Ahora incluimos 'perfil')
        $this->view('dashboard/candidato', compact('postulaciones', 'perfil'));
    }

    // ============ MÉTODOS QUE FALTABAN (SOLUCIÓN AL ERROR 500) ============

    public function opcionesPerfil(): void
    {
        $this->view('candidato/opciones-perfil');
    }

    public function perfilManual(): void
    {
        // Reutilizamos la lógica de editar perfil para cargar el formulario vacío o con datos previos
        $this->editarPerfil();
    }

    // ======================================================================

    public function editarPerfil(): void
    {
        $user = Auth::user();
        $stmt = $this->db->prepare("SELECT * FROM solicitantes WHERE id = ?");
        $stmt->execute([$user['id']]);
        $perfil = $stmt->fetch(PDO::FETCH_ASSOC);

        // Experiencia y Educación
        $expStmt = $this->db->prepare("SELECT * FROM candidato_experiencia WHERE solicitante_id = ? ORDER BY fecha_inicio DESC");
        $expStmt->execute([$user['id']]);
        $experiencia = $expStmt->fetchAll(PDO::FETCH_ASSOC);

        $eduStmt = $this->db->prepare("SELECT * FROM candidato_educacion WHERE solicitante_id = ? ORDER BY fecha_graduacion DESC");
        $eduStmt->execute([$user['id']]);
        $educacion = $eduStmt->fetchAll(PDO::FETCH_ASSOC);

        // Si viene de perfilManual, usamos la vista formulario-manual
        // Si viene de editar, usamos la misma vista (son compatibles)
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

                // VALIDACIÓN SIMPLE: Si no pone nombre, no guardar
                if (empty($nombre) || empty($apellido)) {
                    $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Nombre y Apellido son obligatorios.'];
                    header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/perfil-manual');
                    exit;
                }

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

                // Actualizar Experiencia
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

                // Actualizar Educación
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
                $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Perfil completo actualizado correctamente'];
                header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/dashboard');
                exit;

            } catch (\Exception $e) {
                if ($this->db->inTransaction()) $this->db->rollBack();
                $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al guardar perfil: ' . $e->getMessage()];
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
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'Ya te has postulado a esta vacante anteriormente.'];
            header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/postulaciones');
            exit;
        }

        // Obtener título para mensaje personalizado
        $stmtT = $this->db->prepare("SELECT titulo FROM vacantes WHERE id = ?");
        $stmtT->execute([$vacante_id]);
        $titulo = $stmtT->fetchColumn() ?: 'la vacante';

        $stmt = $this->db->prepare("
            INSERT INTO postulaciones (vacante_id, solicitante_id, fecha_postulacion, estado) 
            VALUES (?, ?, NOW(), 'pendiente')
        ");
        
        try {
            $stmt->execute([$vacante_id, $user['id']]);
            
            // Log interaction
            $ip = $_SERVER['REMOTE_ADDR'];
            $sid = session_id();
            $this->db->prepare("INSERT INTO interacciones_vacante (vacante_id, tipo_interaccion, origen, ip, session_id) VALUES (?, 'click_aplicar', 'web', ?, ?)")
                     ->execute([$vacante_id, $ip, $sid]);

            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => "¡Te has postulado exitosamente a <strong>$titulo</strong>!"];
            header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/postulaciones');
            exit;
        } catch (\PDOException $e) {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al procesar: ' . $e->getMessage()];
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
                $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'ID de vacante inválido.'];
                header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/postulaciones');
                exit;
            }

            $stmtCheck = $this->db->prepare("SELECT id, estado FROM postulaciones WHERE vacante_id = ? AND solicitante_id = ?");
            $stmtCheck->execute([$vacante_id, $user['id']]);
            $postulacion = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if (!$postulacion) {
                 $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Postulación no encontrada.'];
            } elseif ($postulacion['estado'] !== 'pendiente') {
                 $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'No puedes cancelar una postulación que ya ha sido revisada.'];
            } else {
                $stmtDel = $this->db->prepare("DELETE FROM postulaciones WHERE id = ?");
                if ($stmtDel->execute([$postulacion['id']])) {
                    $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Postulación cancelada correctamente.'];
                } else {
                    $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al eliminar la postulación.'];
                }
            }
            
            $redirect = $_POST['redirect'] ?? '/candidato/postulaciones';
            header('Location: ' . ENV_APP['BASE_URL'] . $redirect);
            exit;
        }
    }
    
    public function actualizarPerfil(): void
    {
        $user = Auth::user();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $apellido = $_POST['apellido'] ?? '';
            $email = $_POST['email'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            
            $stmt = $this->db->prepare("UPDATE solicitantes SET nombre = ?, apellido = ?, email = ?, telefono = ? WHERE id = ?");
            try {
                $stmt->execute([$nombre, $apellido, $email, $telefono, $user['id']]);
                $_SESSION['user']['nombre'] = $nombre;
                $_SESSION['user']['apellido'] = $apellido;
                
                $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Información personal actualizada.'];
            } catch (\Exception $e) {
                $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al actualizar: ' . $e->getMessage()];
            }
            header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/perfil');
            exit;
        }
    }

    public function subirCV(): void
    {
        $user = Auth::user();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cv_file'])) {
            $file = $_FILES['cv_file'];
            
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al subir archivo.'];
                header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/perfil');
                exit;
            }

            $mime = mime_content_type($file['tmp_name']);
            if ($mime !== 'application/pdf') {
                $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Solo se permiten archivos PDF.'];
                header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/perfil');
                exit;
            }

            $ext = 'pdf';
            $filename = 'cv_' . $user['id'] . '_' . time() . '.' . $ext;
            $uploadDir = __DIR__ . '/../../public/uploads/cvs/';
            
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                $webPath = '/uploads/cvs/' . $filename;
                
                // Usamos 'cv_ruta' que es el nombre correcto en tu BD
                $stmt = $this->db->prepare("UPDATE solicitantes SET cv_ruta = ?, perfil_completado = 1 WHERE id = ?");
                $stmt->execute([$webPath, $user['id']]);
                
                $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'CV subido correctamente.'];
            } else {
                $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al guardar el archivo.'];
            }
            
            header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/perfil');
            exit;
        }

        // Fallback para evitar pantalla blanca
        $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'No se recibió ningún archivo.'];
        header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/opciones-perfil');
        exit;
    }

    public function perfil(): void
    {
        $user = Auth::user();
        $stmt = $this->db->prepare("SELECT * FROM solicitantes WHERE id = ?");
        $stmt->execute([$user['id']]);
        $perfil = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->view('candidato/perfil', compact('perfil'));
    }

    public function generarPDF(): void
    {
        $user = Auth::user();
        
        $stmt = $this->db->prepare("SELECT * FROM solicitantes WHERE id = ?");
        $stmt->execute([$user['id']]);
        $perfil = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmtExp = $this->db->prepare("SELECT * FROM candidato_experiencia WHERE solicitante_id = ? ORDER BY fecha_inicio DESC");
        $stmtExp->execute([$user['id']]);
        $experiencia = $stmtExp->fetchAll(PDO::FETCH_ASSOC);

        $stmtEdu = $this->db->prepare("SELECT * FROM candidato_educacion WHERE solicitante_id = ? ORDER BY fecha_graduacion DESC");
        $stmtEdu->execute([$user['id']]);
        $educacion = $stmtEdu->fetchAll(PDO::FETCH_ASSOC);

        $this->view('candidato/cv_pdf', compact('perfil', 'experiencia', 'educacion'));
    }
}
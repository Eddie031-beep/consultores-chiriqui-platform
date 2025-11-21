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
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/login-candidato');
            exit;
        }
    }

    // Dashboard
    public function dashboard(): void
    {
        $user = Auth::user();
        
        // Obtener postulaciones del candidato
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

    // Mis postulaciones
    public function postulaciones(): void
    {
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

    // Postular a vacante
    public function postular($vacante_id): void
    {
        $user = Auth::user();
        $vacante_id = (int)$vacante_id;

        // 1. Verificar que la vacante existe y está abierta
        $stmt = $this->db->prepare("SELECT id, titulo, cantidad_plazas FROM vacantes WHERE id = ? AND estado = 'abierta'");
        $stmt->execute([$vacante_id]);
        $vacante = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$vacante) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Esta vacante ya no está disponible o ha sido cerrada.'];
            header('Location: ' . ENV_APP['BASE_URL'] . '/vacantes');
            exit;
        }

        // 2. Verificar límite de cupos (si existe la columna cantidad_plazas)
        if (isset($vacante['cantidad_plazas']) && $vacante['cantidad_plazas'] > 0) {
            $countStmt = $this->db->prepare("SELECT COUNT(*) as total FROM postulaciones WHERE vacante_id = ? AND estado = 'aceptado'");
            $countStmt->execute([$vacante_id]);
            $aceptados = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

            if ($aceptados >= $vacante['cantidad_plazas']) {
                // Cerrar vacante automáticamente si se llenó
                $closeStmt = $this->db->prepare("UPDATE vacantes SET estado = 'cerrada' WHERE id = ?");
                $closeStmt->execute([$vacante_id]);

                $_SESSION['message'] = ['type' => 'error', 'text' => 'Lo sentimos, las vacantes para este puesto ya han sido cubiertas.'];
                header('Location: ' . ENV_APP['BASE_URL'] . '/vacantes/' . $vacante_id);
                exit;
            }
        }

        // 3. Verificar si ya se postuló
        $checkStmt = $this->db->prepare("
            SELECT id FROM postulaciones WHERE solicitante_id = ? AND vacante_id = ?
        ");
        $checkStmt->execute([$user['id'], $vacante_id]);
        if ($checkStmt->fetch()) {
            $_SESSION['message'] = ['type' => 'warning', 'text' => 'Ya te has postulado a esta vacante anteriormente.'];
            header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/postulaciones');
            exit;
        }

        // 4. Crear postulación
        $postStmt = $this->db->prepare("
            INSERT INTO postulaciones (solicitante_id, vacante_id, estado, fecha_postulacion)
            VALUES (?, ?, 'pendiente', NOW())
        ");
        $postStmt->execute([$user['id'], $vacante_id]);

        $_SESSION['message'] = ['type' => 'success', 'text' => '¡Postulación enviada exitosamente! La empresa revisará tu perfil.'];
        header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/postulaciones');
        exit;
    }

    // Perfil
    public function perfil(): void
    {
        $user = Auth::user();

        $stmt = $this->db->prepare("SELECT * FROM solicitantes WHERE id = ?");
        $stmt->execute([$user['id']]);
        $solicitante = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $cedula = trim($_POST['cedula'] ?? '');

            $updateStmt = $this->db->prepare("
                UPDATE solicitantes 
                SET nombre = ?, apellido = ?, telefono = ?, cedula = ?
                WHERE id = ?
            ");
            $updateStmt->execute([$nombre, $apellido, $telefono, $cedula, $user['id']]);

            $_SESSION['message'] = ['type' => 'success', 'text' => 'Perfil actualizado'];
            header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/perfil');
            exit;
        }

        $this->view('dashboard/perfil-candidato', compact('solicitante'));
    }
}
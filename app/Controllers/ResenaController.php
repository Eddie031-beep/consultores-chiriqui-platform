<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Auth;
use PDO;

class ResenaController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db_connect('local');
    }

    // Guardar o Editar (Upsert)
    public function guardar(): void
    {
        if (!Auth::check() || Auth::user()['rol'] !== 'candidato') {
            $this->redirectBack('error', 'Debes ser candidato para comentar.');
        }

        $user = Auth::user();
        $vacante_id = (int)($_POST['vacante_id'] ?? 0);
        $calificacion = (int)($_POST['calificacion'] ?? 5);
        $comentario = trim($_POST['comentario'] ?? '');

        if ($calificacion < 1 || $calificacion > 5 || empty($comentario)) {
            $this->redirectBack('error', 'La calificación y el comentario son obligatorios.');
        }

        // Verificar si ya existe para editar
        $check = $this->db->prepare("SELECT id FROM resenas_vacante WHERE vacante_id = ? AND solicitante_id = ?");
        $check->execute([$vacante_id, $user['id']]);
        $existe = $check->fetchColumn();

        try {
            if ($existe) {
                // Editar
                $stmt = $this->db->prepare("UPDATE resenas_vacante SET calificacion = ?, comentario = ?, fecha_actualizacion = NOW() WHERE id = ?");
                $stmt->execute([$calificacion, $comentario, $existe]);
                $msg = 'Reseña actualizada correctamente.';
            } else {
                // Crear
                $stmt = $this->db->prepare("INSERT INTO resenas_vacante (vacante_id, solicitante_id, calificacion, comentario) VALUES (?, ?, ?, ?)");
                $stmt->execute([$vacante_id, $user['id'], $calificacion, $comentario]);
                $msg = 'Reseña publicada exitosamente.';
            }
            $this->redirectBack('success', $msg);

        } catch (\Exception $e) {
            $this->redirectBack('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    // Eliminar Reseña (Candidato dueño o Empresa dueña de la vacante)
    public function eliminar($id): void
    {
        if (!Auth::check()) $this->redirectBack('error', 'Acceso denegado.');
        
        $user = Auth::user();
        $id = (int)$id;

        // Verificar permisos
        $stmt = $this->db->prepare("
            SELECT r.*, v.empresa_id 
            FROM resenas_vacante r 
            JOIN vacantes v ON r.vacante_id = v.id 
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        $resena = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$resena) $this->redirectBack('error', 'Reseña no encontrada.');

        $esAutor = ($user['rol'] === 'candidato' && $resena['solicitante_id'] == $user['id']);
        $esEmpresa = ($user['rol'] === 'empresa_admin' && $resena['empresa_id'] == $user['empresa_id']);

        if ($esAutor || $esEmpresa) {
            $this->db->prepare("DELETE FROM resenas_vacante WHERE id = ?")->execute([$id]);
            $this->redirectBack('success', 'Reseña eliminada.');
        } else {
            $this->redirectBack('error', 'No tienes permiso para eliminar esta reseña.');
        }
    }

    // Reportar Reseña
    public function reportar($id): void
    {
        if (!Auth::check()) $this->redirectBack('error', 'Inicia sesión para reportar.');
        
        $razon = $_POST['razon'] ?? 'Contenido inapropiado';
        $stmt = $this->db->prepare("UPDATE resenas_vacante SET reportado = 1, razon_reporte = ? WHERE id = ?");
        $stmt->execute([$razon, $id]);

        $this->redirectBack('success', 'La reseña ha sido reportada a la administración.');
    }

    private function redirectBack($type, $text) {
        $_SESSION['mensaje'] = ['tipo' => $type, 'texto' => $text];
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}

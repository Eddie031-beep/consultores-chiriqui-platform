<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Auth;
use PDO;

class AuthController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db_connect('local'); // maestro
    }

    public function showLoginConsultora(): void
    {
        $this->view('auth/login-consultora');
    }

    public function showLoginEmpresa(): void
    {
        $this->view('auth/login-empresa');
    }

    public function loginConsultora(): void
    {
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $sql = "
            SELECT u.*, r.nombre AS rol_nombre
            FROM usuarios u
            JOIN roles r ON r.id = u.rol_id
            WHERE u.email = :email
              AND r.nombre = 'admin_consultora'
              AND u.estado = 'activo'
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || $user['password_hash'] !== $password) {
            $error = 'Credenciales inválidas';
            $this->view('auth/login-consultora', compact('error', 'email'));
            return;
        }

        Auth::login($user);

        $base = ENV_APP['BASE_URL'];
        header("Location: {$base}/consultora/dashboard");
        exit;
    }

    public function loginEmpresa(): void
    {
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $sql = "
            SELECT u.*, r.nombre AS rol_nombre
            FROM usuarios u
            JOIN roles r ON r.id = u.rol_id
            WHERE u.email = :email
              AND r.nombre = 'empresa_admin'
              AND u.estado = 'activo'
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || $user['password_hash'] !== $password) {
            $error = 'Credenciales inválidas';
            $this->view('auth/login-empresa', compact('error', 'email'));
            return;
        }

        Auth::login($user);

        $base = ENV_APP['BASE_URL'];
        header("Location: {$base}/empresa/dashboard");
        exit;
    }

    public function logout(): void
    {
        Auth::logout();
        $base = ENV_APP['BASE_URL'];
        header("Location: {$base}/");
        exit;
    }
}

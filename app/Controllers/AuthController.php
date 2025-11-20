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
        $this->db = db_connect('local');
    }

    // ============ PÁGINA INICIAL DE SELECCIÓN ============
    public function index(): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user['rol'] === 'admin_consultora') {
                header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/dashboard');
            } elseif ($user['rol'] === 'empresa_admin') {
                header('Location: ' . ENV_APP['BASE_URL'] . '/empresa/dashboard');
            } elseif ($user['rol'] === 'candidato') {
                header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/dashboard');
            }
            exit;
        }

        $this->view('auth/index');
    }

    // ============ LOGIN CANDIDATO ============
    public function showLoginCandidato(): void
    {
        if (Auth::check()) {
            header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/dashboard');
            exit;
        }

        $vacante_id = $_GET['vacante_id'] ?? '';
        $this->view('auth/login-candidato', compact('vacante_id'));
    }

    public function loginCandidato(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $error = '';

        if (empty($email) || empty($password)) {
            $error = 'Por favor completa todos los campos';
            $this->view('auth/login-candidato', compact('error', 'email'));
            return;
        }

        $sql = "SELECT * FROM solicitantes WHERE email = ? AND estado = 'activo'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $solicitante = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$solicitante || !password_verify($password, $solicitante['password_hash'])) {
            $error = 'Email o contraseña incorrectos';
            $this->view('auth/login-candidato', compact('error', 'email'));
            return;
        }

        // Actualizar último login
        $updateStmt = $this->db->prepare('UPDATE solicitantes SET ultimo_login = NOW() WHERE id = ?');
        $updateStmt->execute([$solicitante['id']]);

        // Crear sesión personalizada para candidatos
        $_SESSION['user'] = [
            'id' => $solicitante['id'],
            'nombre' => $solicitante['nombre'],
            'apellido' => $solicitante['apellido'],
            'email' => $solicitante['email'],
            'rol' => 'candidato',
            'rol_nombre' => 'candidato'
        ];

        // Redirigir a vacante si viene de postulación
        if (isset($_GET['vacante_id']) && !empty($_GET['vacante_id'])) {
            header('Location: ' . ENV_APP['BASE_URL'] . '/postular/' . intval($_GET['vacante_id']));
        } else {
            header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/dashboard');
        }
        exit;
    }

    // ============ REGISTRO CANDIDATO ============
    public function showRegistroCandidato(): void
    {
        if (Auth::check()) {
            header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/dashboard');
            exit;
        }

        $vacante_id = $_GET['vacante_id'] ?? '';
        $this->view('auth/registro-candidato', compact('vacante_id'));
    }

    public function registroCandidato(): void
    {
        $form_data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'cedula' => trim($_POST['cedula'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? ''
        ];

        $error = '';

        // Validaciones
        if (empty($form_data['nombre'])) {
            $error = 'El nombre es requerido';
        } elseif (empty($form_data['apellido'])) {
            $error = 'El apellido es requerido';
        } elseif (empty($form_data['email'])) {
            $error = 'El email es requerido';
        } elseif (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
            $error = 'Email inválido';
        } elseif (empty($form_data['password'])) {
            $error = 'La contraseña es requerida';
        } elseif (strlen($form_data['password']) < 6) {
            $error = 'La contraseña debe tener mínimo 6 caracteres';
        } elseif ($form_data['password'] !== $form_data['password_confirm']) {
            $error = 'Las contraseñas no coinciden';
        }

        if (!empty($error)) {
            $this->view('auth/registro-candidato', compact('error', 'form_data'));
            return;
        }

        // Verificar si email ya existe
        $checkStmt = $this->db->prepare('SELECT id FROM solicitantes WHERE email = ?');
        $checkStmt->execute([$form_data['email']]);
        if ($checkStmt->fetch()) {
            $error = 'Este email ya está registrado';
            $this->view('auth/registro-candidato', compact('error', 'form_data'));
            return;
        }

        // Insertar nuevo solicitante
        $password_hash = password_hash($form_data['password'], PASSWORD_BCRYPT);
        $sql = "INSERT INTO solicitantes (nombre, apellido, email, password_hash, telefono, cedula, estado) 
                VALUES (?, ?, ?, ?, ?, ?, 'activo')";
        $stmt = $this->db->prepare($sql);

        try {
            $stmt->execute([
                $form_data['nombre'],
                $form_data['apellido'],
                $form_data['email'],
                $password_hash,
                $form_data['telefono'] ?: null,
                $form_data['cedula'] ?: null
            ]);

            $solicitante_id = $this->db->lastInsertId();

            // Login automático
            $_SESSION['user'] = [
                'id' => $solicitante_id,
                'nombre' => $form_data['nombre'],
                'apellido' => $form_data['apellido'],
                'email' => $form_data['email'],
                'rol' => 'candidato',
                'rol_nombre' => 'candidato'
            ];

            // Redirigir a vacante si viene desde postulación
            if (isset($_GET['vacante_id']) && !empty($_GET['vacante_id'])) {
                header('Location: ' . ENV_APP['BASE_URL'] . '/postular/' . intval($_GET['vacante_id']));
            } else {
                header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/dashboard');
            }
            exit;
        } catch (\PDOException $e) {
            $error = 'Error al registrar: ' . $e->getMessage();
            $this->view('auth/registro-candidato', compact('error', 'form_data'));
        }
    }

    // ============ LOGIN EMPRESA ============
    public function showLoginEmpresa(): void
    {
        if (Auth::check()) {
            header('Location: ' . ENV_APP['BASE_URL'] . '/empresa/dashboard');
            exit;
        }

        $this->view('auth/login-empresa');
    }

    public function loginEmpresa(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $error = '';

        if (empty($email) || empty($password)) {
            $error = 'Por favor completa todos los campos';
            $this->view('auth/login-empresa', compact('error', 'email'));
            return;
        }

        $sql = "
            SELECT u.*, r.nombre AS rol_nombre, e.nombre as empresa_nombre
            FROM usuarios u
            JOIN roles r ON r.id = u.rol_id
            JOIN empresas e ON e.id = u.empresa_id
            WHERE u.email = ? AND r.nombre = 'empresa_admin' AND u.estado = 'activo'
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $error = 'Email o contraseña incorrectos';
            $this->view('auth/login-empresa', compact('error', 'email'));
            return;
        }

        // Actualizar último login
        $updateStmt = $this->db->prepare('UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?');
        $updateStmt->execute([$user['id']]);

        // Crear sesión
        Auth::login($user);

        header('Location: ' . ENV_APP['BASE_URL'] . '/empresa/dashboard');
        exit;
    }

    // ============ REGISTRO EMPRESA ============
    public function showRegistroEmpresa(): void
    {
        if (Auth::check()) {
            header('Location: ' . ENV_APP['BASE_URL'] . '/empresa/dashboard');
            exit;
        }

        $this->view('auth/registro-empresa');
    }

    public function registroEmpresa(): void
    {
        $form_data = [
            'nombre_empresa' => trim($_POST['nombre_empresa'] ?? ''),
            'ruc' => trim($_POST['ruc'] ?? ''),
            'dv' => trim($_POST['dv'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? ''),
            'provincia' => trim($_POST['provincia'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'email_contacto' => trim($_POST['email_contacto'] ?? ''),
            'sitio_web' => trim($_POST['sitio_web'] ?? ''),
            'sector' => trim($_POST['sector'] ?? ''),
            'nombre_usuario' => trim($_POST['nombre_usuario'] ?? ''),
            'apellido_usuario' => trim($_POST['apellido_usuario'] ?? ''),
            'email_usuario' => trim($_POST['email_usuario'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'tipo' => $_POST['tipo'] ?? 'privada'
        ];

        $error = '';

        // Validaciones
        if (empty($form_data['nombre_empresa'])) {
            $error = 'El nombre de la empresa es requerido';
        } elseif (empty($form_data['ruc'])) {
            $error = 'El RUC es requerido';
        } elseif (empty($form_data['direccion'])) {
            $error = 'La dirección es requerida';
        } elseif (empty($form_data['provincia'])) {
            $error = 'La provincia es requerida';
        } elseif (empty($form_data['nombre_usuario'])) {
            $error = 'El nombre del usuario es requerido';
        } elseif (empty($form_data['apellido_usuario'])) {
            $error = 'El apellido del usuario es requerido';
        } elseif (empty($form_data['email_usuario'])) {
            $error = 'El email es requerido';
        } elseif (!filter_var($form_data['email_usuario'], FILTER_VALIDATE_EMAIL)) {
            $error = 'Email inválido';
        } elseif (empty($form_data['password'])) {
            $error = 'La contraseña es requerida';
        } elseif (strlen($form_data['password']) < 6) {
            $error = 'La contraseña debe tener mínimo 6 caracteres';
        } elseif ($form_data['password'] !== $form_data['password_confirm']) {
            $error = 'Las contraseñas no coinciden';
        }

        if (!empty($error)) {
            $this->view('auth/registro-empresa', compact('error', 'form_data'));
            return;
        }

        // Verificar si RUC existe
        $checkRuc = $this->db->prepare('SELECT id FROM empresas WHERE ruc = ?');
        $checkRuc->execute([$form_data['ruc']]);
        if ($checkRuc->fetch()) {
            $error = 'Este RUC ya está registrado';
            $this->view('auth/registro-empresa', compact('error', 'form_data'));
            return;
        }

        // Verificar si email existe
        $checkEmail = $this->db->prepare('SELECT id FROM usuarios WHERE email = ?');
        $checkEmail->execute([$form_data['email_usuario']]);
        if ($checkEmail->fetch()) {
            $error = 'Este email ya está registrado';
            $this->view('auth/registro-empresa', compact('error', 'form_data'));
            return;
        }

        // Iniciar transacción
        try {
            $this->db->beginTransaction();

            // Insertar empresa
            $empresaSql = "INSERT INTO empresas (tipo, nombre, ruc, dv, direccion, provincia, telefono, email_contacto, sitio_web, sector, estado) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'activa')";
            $empresaStmt = $this->db->prepare($empresaSql);
            $empresaStmt->execute([
                $form_data['tipo'],
                $form_data['nombre_empresa'],
                $form_data['ruc'],
                $form_data['dv'],
                $form_data['direccion'],
                $form_data['provincia'],
                $form_data['telefono'] ?: null,
                $form_data['email_contacto'] ?: null,
                $form_data['sitio_web'] ?: null,
                $form_data['sector'] ?: null
            ]);

            $empresa_id = $this->db->lastInsertId();

            // Insertar usuario admin
            $password_hash = password_hash($form_data['password'], PASSWORD_BCRYPT);
            $usuarioSql = "INSERT INTO usuarios (empresa_id, nombre, apellido, email, password_hash, rol_id, estado) 
                          VALUES (?, ?, ?, ?, ?, 2, 'activo')";
            $usuarioStmt = $this->db->prepare($usuarioSql);
            $usuarioStmt->execute([
                $empresa_id,
                $form_data['nombre_usuario'],
                $form_data['apellido_usuario'],
                $form_data['email_usuario'],
                $password_hash
            ]);

            $usuario_id = $this->db->lastInsertId();

            // Confirmar transacción
            $this->db->commit();

            // Obtener datos para sesión
            $userStmt = $this->db->prepare("SELECT u.*, e.nombre as empresa_nombre FROM usuarios u JOIN empresas e ON e.id = u.empresa_id WHERE u.id = ?");
            $userStmt->execute([$usuario_id]);
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);

            // Login automático
            Auth::login(array_merge($user, ['rol_nombre' => 'empresa_admin']));

            header('Location: ' . ENV_APP['BASE_URL'] . '/empresa/dashboard');
            exit;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            $error = 'Error al registrar la empresa: ' . $e->getMessage();
            $this->view('auth/registro-empresa', compact('error', 'form_data'));
        }
    }

    // ============ LOGIN CONSULTORA ============
    public function showLoginConsultora(): void
    {
        if (Auth::check()) {
            header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/dashboard');
            exit;
        }

        $this->view('auth/login-consultora');
    }

    public function loginConsultora(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $error = '';

        if (empty($email) || empty($password)) {
            $error = 'Por favor completa todos los campos';
            $this->view('auth/login-consultora', compact('error', 'email'));
            return;
        }

        $sql = "
            SELECT u.*, r.nombre AS rol_nombre
            FROM usuarios u
            JOIN roles r ON r.id = u.rol_id
            WHERE u.email = ? AND r.nombre = 'admin_consultora' AND u.estado = 'activo'
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $error = 'Email o contraseña incorrectos';
            $this->view('auth/login-consultora', compact('error', 'email'));
            return;
        }

        // Actualizar último login
        $updateStmt = $this->db->prepare('UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?');
        $updateStmt->execute([$user['id']]);

        Auth::login($user);

        header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/dashboard');
        exit;
    }

    // ============ LOGOUT ============
    public function logout(): void
    {
        Auth::logout();
        header('Location: ' . ENV_APP['BASE_URL'] . '/');
        exit;
    }
}
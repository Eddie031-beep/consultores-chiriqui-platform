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

    // ============ PÁGINA INICIAL - MODAL DE SELECCIÓN ============
    public function index(): void
    {
        // Si ya está autenticado, redirigir al dashboard correspondiente
        if (Auth::check()) {
            $this->redirectToDashboard();
            return;
        }

        // Mostrar el modal de selección
        include __DIR__ . '/../Views/auth/index.php';
    }

    // ============ MOSTRAR LOGIN UNIFICADO ============
    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirectToDashboard();
            return;
        }

        $tipo = $_GET['tipo'] ?? 'persona';
        $error = $_SESSION['error'] ?? '';
        $email = $_SESSION['email'] ?? '';
        
        unset($_SESSION['error'], $_SESSION['email']);

        include __DIR__ . '/../Views/auth/login-unificado.php';
    }

    // ============ PROCESAR LOGIN UNIFICADO ============
    public function processLogin(): void
    {
        $tipo = $_POST['tipo'] ?? $_GET['tipo'] ?? 'persona';
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validaciones
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Por favor completa todos los campos';
            $_SESSION['email'] = $email;
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/login?tipo=' . $tipo);
            exit;
        }

        // Login según tipo
        if ($tipo === 'persona' || $tipo === 'candidato') {
            $this->loginPersona($email, $password);
        } elseif ($tipo === 'empresa') {
            $this->loginEmpresa($email, $password);
        } elseif ($tipo === 'consultora') {
            $this->loginConsultora($email, $password);
        } else {
            $_SESSION['error'] = 'Tipo de usuario inválido';
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth');
            exit;
        }
    }

    private function loginPersona($email, $password): void
    {
        $sql = "SELECT * FROM solicitantes WHERE email = ? AND estado = 'activo'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $solicitante = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$solicitante || !password_verify($password, $solicitante['password_hash'])) {
            $_SESSION['error'] = 'Email o contraseña incorrectos';
            $_SESSION['email'] = $email;
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/login?tipo=persona');
            exit;
        }

        // Actualizar último login
        $updateStmt = $this->db->prepare('UPDATE solicitantes SET ultimo_login = NOW() WHERE id = ?');
        $updateStmt->execute([$solicitante['id']]);

        // Crear sesión
        $_SESSION['user'] = [
            'id' => $solicitante['id'],
            'nombre' => $solicitante['nombre'],
            'apellido' => $solicitante['apellido'],
            'email' => $solicitante['email'],
            'rol' => 'candidato',
            'rol_nombre' => 'candidato'
        ];

        header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/dashboard');
        exit;
    }

    private function loginEmpresa($email, $password): void
    {
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
            $_SESSION['error'] = 'Email o contraseña incorrectos';
            $_SESSION['email'] = $email;
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/login?tipo=empresa');
            exit;
        }

        // Actualizar último login
        $updateStmt = $this->db->prepare('UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?');
        $updateStmt->execute([$user['id']]);

        // Crear sesión
        Auth::login($user);

        header('Location: ' . ENV_APP['BASE_URL'] . '/empresa/dashboard');
        exit;
    }

    private function loginConsultora($email, $password): void
    {
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
            $_SESSION['error'] = 'Email o contraseña incorrectos';
            $_SESSION['email'] = $email;
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/login?tipo=consultora');
            exit;
        }

        // Actualizar último login
        $updateStmt = $this->db->prepare('UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?');
        $updateStmt->execute([$user['id']]);

        Auth::login($user);

        header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/dashboard');
        exit;
    }

    // ============ MOSTRAR REGISTRO UNIFICADO ============
    public function showRegistro(): void
    {
        if (Auth::check()) {
            $this->redirectToDashboard();
            return;
        }

        $tipo = $_GET['tipo'] ?? 'persona';
        $error = $_SESSION['error'] ?? '';
        $form_data = $_SESSION['form_data'] ?? [];
        
        unset($_SESSION['error'], $_SESSION['form_data']);

        include __DIR__ . '/../Views/auth/registro-unificado.php';
    }

    // ============ PROCESAR REGISTRO UNIFICADO ============
    public function processRegistro(): void
    {
        $tipo = $_POST['tipo'] ?? $_GET['tipo'] ?? 'persona';

        if ($tipo === 'persona' || $tipo === 'candidato') {
            $this->registroPersona();
        } elseif ($tipo === 'empresa') {
            $this->registroEmpresa();
        } elseif ($tipo === 'consultora') {
            $this->registroConsultora();
        } else {
            $_SESSION['error'] = 'Tipo de registro inválido';
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth');
            exit;
        }
    }

    private function registroConsultora(): void
    {
        $form_data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'codigo_acceso' => trim($_POST['codigo_acceso'] ?? '')
        ];

        // Validaciones
        $error = $this->validateConsultoraData($form_data);
        if ($error) {
            $_SESSION['error'] = $error;
            $_SESSION['form_data'] = $form_data;
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/registro?tipo=consultora');
            exit;
        }

        // Verificar duplicados
        $checkStmt = $this->db->prepare('SELECT id FROM usuarios WHERE email = ?');
        $checkStmt->execute([$form_data['email']]);
        if ($checkStmt->fetch()) {
            $_SESSION['error'] = 'Este email ya está registrado';
            $_SESSION['form_data'] = $form_data;
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/registro?tipo=consultora');
            exit;
        }

        // Obtener ID del rol admin_consultora
        // Asumimos que existe. Si no, habría que crearlo o manejar el error.
        // Buscamos dinámicamente el ID para evitar hardcoding incorrecto.
        $rolStmt = $this->db->prepare("SELECT id FROM roles WHERE nombre = 'admin_consultora'");
        $rolStmt->execute();
        $rol = $rolStmt->fetch(PDO::FETCH_ASSOC);
        $rol_id = $rol ? $rol['id'] : 1; // Fallback to 1 (usually admin) but risky. Better strictly check.

        if (!$rol) {
             // Si no existe el rol, podríamos crearlo o fallar. 
             // Por seguridad, asignaremos un rol predeterminado o fallaremos.
             $_SESSION['error'] = 'Error interno: Rol de consultora no configurado.';
             header('Location: ' . ENV_APP['BASE_URL'] . '/auth/registro?tipo=consultora');
             exit;
        }

        // Insertar usuario
        $password_hash = password_hash($form_data['password'], PASSWORD_BCRYPT);
        // Nota: empresa_id es NULL para consultores independientes o de la misma consultora
        $sql = "INSERT INTO usuarios (empresa_id, nombre, apellido, email, password_hash, rol_id, estado) 
                VALUES (NULL, ?, ?, ?, ?, ?, 'activo')";
        $stmt = $this->db->prepare($sql);

        try {
            $stmt->execute([
                $form_data['nombre'],
                $form_data['apellido'],
                $form_data['email'],
                $password_hash,
                $rol_id
            ]);

            $usuario_id = $this->db->lastInsertId();

            // Login automático
            $_SESSION['user'] = [
                'id' => $usuario_id,
                'nombre' => $form_data['nombre'],
                'apellido' => $form_data['apellido'],
                'email' => $form_data['email'],
                'rol' => 'admin_consultora',
                'rol_nombre' => 'admin_consultora'
            ];

            header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/dashboard');
            exit;

        } catch (\PDOException $e) {
            $_SESSION['error'] = 'Error al registrar: ' . $e->getMessage();
            $_SESSION['form_data'] = $form_data;
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/registro?tipo=consultora');
            exit;
        }
    }

    private function registroPersona(): void
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

        // Validaciones
        $error = $this->validatePersonaData($form_data);
        if ($error) {
            $_SESSION['error'] = $error;
            $_SESSION['form_data'] = $form_data;
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/registro?tipo=persona');
            exit;
        }

        // Verificar si email ya existe
        $checkStmt = $this->db->prepare('SELECT id FROM solicitantes WHERE email = ?');
        $checkStmt->execute([$form_data['email']]);
        if ($checkStmt->fetch()) {
            $_SESSION['error'] = 'Este email ya está registrado';
            $_SESSION['form_data'] = $form_data;
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/registro?tipo=persona');
            exit;
        }

        // Insertar nuevo solicitante
        $password_hash = password_hash($form_data['password'], PASSWORD_BCRYPT);
        $sql = "INSERT INTO solicitantes (nombre, apellido, email, password_hash, telefono, cedula, nacionalidad, fecha_nacimiento, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'activo')";
        $stmt = $this->db->prepare($sql);

        try {
            $stmt->execute([
                $form_data['nombre'],
                $form_data['apellido'],
                $form_data['email'],
                $password_hash,
                $form_data['telefono'] ?: null,
                $form_data['cedula'] ?: null,
                $_POST['nacionalidad'] ?? null,  // New
                $_POST['fecha_nacimiento'] ?: null // New
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

            header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/opciones-perfil');
            exit;
        } catch (\PDOException $e) {
            $_SESSION['error'] = 'Error al registrar: ' . $e->getMessage();
            $_SESSION['form_data'] = $form_data;
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/registro?tipo=persona');
            exit;
        }
    }

    private function registroEmpresa(): void
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
            'tipo' => $_POST['tipo_empresa'] ?? 'privada'
        ];

        // Validaciones
        $error = $this->validateEmpresaData($form_data);
        if ($error) {
            $_SESSION['error'] = $error;
            $_SESSION['form_data'] = $form_data;
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/registro?tipo=empresa');
            exit;
        }

        // Verificar duplicados
        $checkRuc = $this->db->prepare('SELECT id FROM empresas WHERE ruc = ?');
        $checkRuc->execute([$form_data['ruc']]);
        if ($checkRuc->fetch()) {
            $_SESSION['error'] = 'Este RUC ya está registrado';
            $_SESSION['form_data'] = $form_data;
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/registro?tipo=empresa');
            exit;
        }

        $checkEmail = $this->db->prepare('SELECT id FROM usuarios WHERE email = ?');
        $checkEmail->execute([$form_data['email_usuario']]);
        if ($checkEmail->fetch()) {
            $_SESSION['error'] = 'Este email ya está registrado';
            $_SESSION['form_data'] = $form_data;
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/registro?tipo=empresa');
            exit;
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
            $_SESSION['error'] = 'Error al registrar la empresa: ' . $e->getMessage();
            $_SESSION['form_data'] = $form_data;
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/registro?tipo=empresa');
            exit;
        }
    }

    // ============ VALIDACIONES ============
    private function validatePersonaData($data): ?string
    {
        if (empty($data['nombre'])) return 'El nombre es requerido';
        if (empty($data['apellido'])) return 'El apellido es requerido';
        if (empty($data['email'])) return 'El email es requerido';
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) return 'Email inválido';
        if (empty($data['password'])) return 'La contraseña es requerida';
        if (strlen($data['password']) < 6) return 'La contraseña debe tener mínimo 6 caracteres';
        if ($data['password'] !== $data['password_confirm']) return 'Las contraseñas no coinciden';
        return null;
    }

    private function validateEmpresaData($data): ?string
    {
        if (empty($data['nombre_empresa'])) return 'El nombre de la empresa es requerido';
        if (empty($data['ruc'])) return 'El RUC es requerido';
        if (empty($data['direccion'])) return 'La dirección es requerida';
        if (empty($data['provincia'])) return 'La provincia es requerida';
        if (empty($data['nombre_usuario'])) return 'El nombre del usuario es requerido';
        if (empty($data['apellido_usuario'])) return 'El apellido del usuario es requerido';
        if (empty($data['email_usuario'])) return 'El email es requerido';
        if (!filter_var($data['email_usuario'], FILTER_VALIDATE_EMAIL)) return 'Email inválido';
        if (empty($data['password'])) return 'La contraseña es requerida';
        if (strlen($data['password']) < 6) return 'La contraseña debe tener mínimo 6 caracteres';
        if ($data['password'] !== $data['password_confirm']) return 'Las contraseñas no coinciden';
        return null;
    }

    // ============ LOGOUT ============
    public function logout(): void
    {
        Auth::logout();
        header('Location: ' . ENV_APP['BASE_URL'] . '/');
        exit;
    }

    // ============ HELPER ============
    private function redirectToDashboard(): void
    {
        $user = Auth::user();
        $rol = $user['rol'] ?? $user['rol_nombre'] ?? '';

        if ($rol === 'admin_consultora') {
            header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/dashboard');
        } elseif ($rol === 'empresa_admin') {
            header('Location: ' . ENV_APP['BASE_URL'] . '/empresa/dashboard');
        } elseif ($rol === 'candidato') {
            header('Location: ' . ENV_APP['BASE_URL'] . '/candidato/dashboard');
        } else {
            header('Location: ' . ENV_APP['BASE_URL'] . '/');
        }
        exit;
    }
}
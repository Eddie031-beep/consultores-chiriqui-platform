<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Auth;
use PDO;

class ConsultoraController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db_connect('local');
        
        // Proteger rutas - solo consultora
        if (!Auth::check() || Auth::user()['rol'] !== 'admin_consultora') {
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/login-consultora');
            exit;
        }
    }

    // ============ DASHBOARD CONSULTORA ============
    public function dashboard(): void
    {
        $user = Auth::user();

        // Estadísticas generales
        $empresasStmt = $this->db->query("SELECT COUNT(*) as total FROM empresas WHERE estado = 'activa'");
        $totalEmpresas = $empresasStmt->fetch(PDO::FETCH_ASSOC)['total'];

        $vacantesStmt = $this->db->query("SELECT COUNT(*) as total FROM vacantes WHERE estado = 'abierta'");
        $totalVacantes = $vacantesStmt->fetch(PDO::FETCH_ASSOC)['total'];

        $postulacionesStmt = $this->db->query("SELECT COUNT(*) as total FROM postulaciones WHERE estado = 'pendiente'");
        $totalPostulaciones = $postulacionesStmt->fetch(PDO::FETCH_ASSOC)['total'];

        $interaccionesStmt = $this->db->query("SELECT COUNT(*) as total FROM interacciones_vacante");
        $totalInteracciones = $interaccionesStmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Últimas empresas registradas
        $ultimasStmt = $this->db->query("
            SELECT * FROM empresas 
            ORDER BY fecha_registro DESC 
            LIMIT 5
        ");
        $ultimasEmpresas = $ultimasStmt->fetchAll(PDO::FETCH_ASSOC);

        // --- NUEVO: ESTADÍSTICAS FINANCIERAS POR EMPRESA (TOP 5) ---
        $sqlStats = "
            SELECT 
                e.nombre,
                COUNT(CASE WHEN iv.tipo_interaccion = 'ver_detalle' THEN 1 END) as vistas,
                COUNT(CASE WHEN iv.tipo_interaccion = 'click_aplicar' THEN 1 END) as aplicaciones,
                -- Cálculo aproximado de facturación (Vista=0.10, Click=0.15, Chat=0.05)
                FORMAT(
                    (COUNT(CASE WHEN iv.tipo_interaccion = 'ver_detalle' THEN 1 END) * 0.10) +
                    (COUNT(CASE WHEN iv.tipo_interaccion = 'click_aplicar' THEN 1 END) * 0.15) +
                    (COUNT(CASE WHEN iv.tipo_interaccion = 'chat_consulta' THEN 1 END) * 0.05), 
                2) as facturacion_estimada
            FROM empresas e
            LEFT JOIN vacantes v ON e.id = v.empresa_id
            LEFT JOIN interacciones_vacante iv ON v.id = iv.vacante_id
            WHERE e.estado = 'activa'
            GROUP BY e.id
            ORDER BY facturacion_estimada DESC
            LIMIT 5
        ";
        $topEmpresasStmt = $this->db->query($sqlStats);
        $topEmpresas = $topEmpresasStmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('dashboard/consultora', compact(
            'totalEmpresas',
            'totalVacantes',
            'totalPostulaciones',
            'totalInteracciones',
            'ultimasEmpresas',
            'topEmpresas',
            'user'
        ));
    }

    // ============ LISTAR EMPRESAS ============
    public function empresas(): void
    {
        $sql = "SELECT e.*, COUNT(v.id) as total_vacantes, COUNT(DISTINCT p.id) as total_postulaciones,
                       (SELECT id FROM contratos_empresas WHERE empresa_id = e.id LIMIT 1) as contrato_id
                FROM empresas e
                LEFT JOIN vacantes v ON e.id = v.empresa_id
                LEFT JOIN postulaciones p ON v.id = p.vacante_id
                GROUP BY e.id
                ORDER BY e.fecha_registro DESC";
        $stmt = $this->db->query($sql);
        $empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('dashboard/empresas-consultora', compact('empresas'));
    }

    // ============ CREAR EMPRESA ============
    public function crearEmpresa(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->storeEmpresa();
            return;
        }

        $error = '';
        $this->view('dashboard/crear-empresa', compact('error'));
    }

    // ============ GUARDAR EMPRESA ============
    public function storeEmpresa(): void
    {
        $tipo = $_POST['tipo'] ?? 'privada';
        $nombre = trim($_POST['nombre'] ?? '');
        $ruc = trim($_POST['ruc'] ?? '');
        $dv = trim($_POST['dv'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $provincia = trim($_POST['provincia'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $email_contacto = trim($_POST['email_contacto'] ?? '');
        $sitio_web = trim($_POST['sitio_web'] ?? '');
        $sector = trim($_POST['sector'] ?? '');

        $errores = [];

        if (empty($nombre)) $errores['nombre'] = 'El nombre es obligatorio';
        if (empty($ruc)) $errores['ruc'] = 'El RUC es obligatorio';
        if (empty($dv)) $errores['dv'] = 'El DV es obligatorio';
        if (empty($direccion)) $errores['direccion'] = 'La dirección es obligatoria';
        if (empty($provincia)) $errores['provincia'] = 'La provincia es obligatoria';

        if (!empty($errores)) {
            $this->view('dashboard/crear-empresa', compact('errores'));
            return;
        }

        try {
            $sql = "INSERT INTO empresas 
                    (tipo, nombre, ruc, dv, direccion, provincia, telefono, email_contacto, sitio_web, sector, estado)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'activa')";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $tipo, $nombre, $ruc, $dv, $direccion, $provincia, 
                $telefono ?: null, $email_contacto ?: null, $sitio_web ?: null, $sector ?: null
            ]);

            $empresa_id = $this->db->lastInsertId();
            
            // Verificar si se solicitó contrato
            if (!empty($_POST['generar_contrato'])) {
                $this->crearContratoInterno($empresa_id, $nombre, $ruc, $dv);
            }

            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Empresa creada exitosamente'];
            header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/empresas');
            exit;

            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Empresa creada exitosamente'];
            header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/empresas');
            exit;

        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                $errores['ruc'] = 'Este RUC ya está registrado';
                $this->view('dashboard/crear-empresa', compact('errores'));
            } else {
                throw $e;
            }
        }
    }

    // ============ EDITAR EMPRESA ============
    public function editarEmpresa($id): void
    {
        $id = (int)$id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateEmpresa($id);
            return;
        }

        $stmt = $this->db->prepare("SELECT * FROM empresas WHERE id = ?");
        $stmt->execute([$id]);
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$empresa) {
            header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/empresas');
            exit;
        }

        $error = '';
        $this->view('dashboard/editar-empresa', compact('empresa', 'error'));
    }

    // ============ ACTUALIZAR EMPRESA ============
    // CAMBIO IMPORTANTE: Debe ser 'public' para que el Router pueda acceder a ella
    public function updateEmpresa($id): void
    {
        // 1. Recibir datos básicos
        $tipo = $_POST['tipo'] ?? 'privada';
        $nombre = trim($_POST['nombre'] ?? '');
        $estado = $_POST['estado'] ?? 'activa';
        $sector = trim($_POST['sector'] ?? '');
        
        // 2. Recibir datos fiscales
        $razon_social = trim($_POST['razon_social'] ?? '');
        $ruc = trim($_POST['ruc'] ?? '');
        $dv = trim($_POST['dv'] ?? '');
        $datos_completos = isset($_POST['datos_facturacion_completos']) ? 1 : 0;

        // 3. Recibir datos de contacto
        $direccion = trim($_POST['direccion'] ?? '');
        $provincia = trim($_POST['provincia'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $email_contacto = trim($_POST['email_contacto'] ?? '');
        $sitio_web = trim($_POST['sitio_web'] ?? '');

        // Validaciones básicas
        $errores = [];
        if (empty($nombre)) $errores['nombre'] = 'El nombre es obligatorio';
        if (empty($ruc)) $errores['ruc'] = 'El RUC es obligatorio';
        if (empty($direccion)) $errores['direccion'] = 'La dirección es obligatoria';

        // Si hay errores, recargar vista
        if (!empty($errores)) {
            $stmt = $this->db->prepare("SELECT * FROM empresas WHERE id = ?");
            $stmt->execute([$id]);
            $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->view('dashboard/editar-empresa', compact('empresa', 'errores'));
            return;
        }

        try {
            $sql = "UPDATE empresas 
                    SET tipo = ?, nombre = ?, razon_social = ?, ruc = ?, dv = ?, datos_facturacion_completos = ?,
                        direccion = ?, provincia = ?, telefono = ?, email_contacto = ?, sitio_web = ?, sector = ?, estado = ?
                    WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $tipo, $nombre, $razon_social ?: null, $ruc, $dv, $datos_completos,
                $direccion, $provincia, $telefono ?: null, $email_contacto ?: null, 
                $sitio_web ?: null, $sector ?: null, $estado, $id
            ]);

            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Empresa actualizada correctamente'];
            
        } catch (\PDOException $e) {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al guardar: ' . $e->getMessage()];
        }

        header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/empresas');
        exit;
    }

    // ============ VER CONTRATO ============
    public function verContrato($id): void
    {
        $id = (int)$id;

        $stmt = $this->db->prepare("
            SELECT c.*, e.nombre as empresa_nombre 
            FROM contratos_empresas c
            JOIN empresas e ON c.empresa_id = e.id
            WHERE c.empresa_id = ?
        ");
        $stmt->execute([$id]);
        $contrato = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$contrato) {
            header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/empresas');
            exit;
        }

        $this->view('dashboard/contrato-empresa', compact('contrato'));
    }

    // ============ GENERAR CONTRATO MANUAL ============
    public function generarContrato($id): void
    {
        $id = (int)$id;
        
        // Obtener datos de empresa
        $stmt = $this->db->prepare("SELECT * FROM empresas WHERE id = ?");
        $stmt->execute([$id]);
        $emp = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$emp) {
            header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/empresas');
            exit;
        }

        try {
            // Verificar si ya existe
            $stmtCheck = $this->db->prepare("SELECT id FROM contratos_empresas WHERE empresa_id = ?");
            $stmtCheck->execute([$id]);
            if ($stmtCheck->fetch()) {
                $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'Esta empresa ya tiene un contrato activo.'];
                header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/contratos/' . $id);
                exit;
            }

            $this->crearContratoInterno($id, $emp['nombre'], $emp['ruc'], $emp['dv']);
            
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Contrato Comercial generado exitosamente'];
            header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/contratos/' . $id);
            exit;

        } catch (\Exception $e) {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al generar: ' . $e->getMessage()];
            header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/empresas');
            exit;
        }
    }

    private function crearContratoInterno($empresaId, $nombre, $ruc, $dv): void
    {
        $contrato = "ACUERDO DE SERVICIO COMERCIAL (Términos y Condiciones)\n\n";
        $contrato .= "ENTRE: Consultores Chiriquí S.A. (La Plataforma)\n";
        $contrato .= "Y: " . $nombre . " (El Cliente)\n";
        $contrato .= "RUC: " . $ruc . "-" . $dv . "\n\n";
        $contrato .= "OBJETO: Prestación de servicios de intermediación laboral y publicidad de vacantes.\n\n";
        $contrato .= "TARIFAS VIGENTES (MODELO DE PEAJE):\n";
        $contrato .= "1. Visualización de Vacante: B/. 1.50\n";
        $contrato .= "2. Postulación (Click Apply): B/. 5.00\n";
        $contrato .= "3. Consulta IA (Chatbot): B/. 2.50\n\n";
        $contrato .= "CONDICIONES DE PAGO:\n";
        $contrato .= "Las facturas se emitirán mensualmente según el consumo real registrado por el sistema.\n";
        $contrato .= "El cliente acepta los registros electrónicos como prueba de servicio.\n\n";
        $contrato .= "ACEPTACIÓN:\n";
        $contrato .= "Fecha de Registro/Generación: " . date('Y-m-d H:i:s') . "\n";
        $contrato .= "IP Autoridad: " . $_SERVER['REMOTE_ADDR'] . "\n\n";
        $contrato .= "-- Documento generado electrónicamente --";

        $sqlContrato = "INSERT INTO contratos_empresas 
                        (empresa_id, version_contrato, texto_resumen, ip_aceptacion, fecha_aceptacion)
                        VALUES (?, 'v1.0-COMERCIAL', ?, ?, NOW())";
        $stmtContrato = $this->db->prepare($sqlContrato);
        $stmtContrato->execute([$empresaId, $contrato, $_SERVER['REMOTE_ADDR']]);
    }

    // ============ CREAR USUARIO PARA EMPRESA ============
    public function crearUsuarioEmpresa($id): void
    {
        $id = (int)$id;

        // Obtener empresa
        $stmt = $this->db->prepare("SELECT * FROM empresas WHERE id = ?");
        $stmt->execute([$id]);
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$empresa) {
            header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/empresas');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->storeUsuarioEmpresa($id);
            return;
        }

        $error = '';
        $this->view('dashboard/crear-usuario-empresa', compact('empresa', 'error'));
    }

    // ============ GUARDAR USUARIO EMPRESA ============
    private function storeUsuarioEmpresa($empresa_id): void
    {
        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        $errores = [];

        if (empty($nombre)) $errores['nombre'] = 'El nombre es obligatorio';
        if (empty($apellido)) $errores['apellido'] = 'El apellido es obligatorio';
        if (empty($email)) $errores['email'] = 'El email es obligatorio';
        if (empty($password)) $errores['password'] = 'La contraseña es obligatoria';
        if (strlen($password) < 6) $errores['password'] = 'La contraseña debe tener mínimo 6 caracteres';
        if ($password !== $password_confirm) $errores['password'] = 'Las contraseñas no coinciden';

        if (!empty($errores)) {
            $stmt = $this->db->prepare("SELECT * FROM empresas WHERE id = ?");
            $stmt->execute([$empresa_id]);
            $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->view('dashboard/crear-usuario-empresa', compact('empresa', 'errores'));
            return;
        }

        try {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO usuarios (empresa_id, nombre, apellido, email, password_hash, rol_id, estado)
                    VALUES (?, ?, ?, ?, ?, 2, 'activo')";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$empresa_id, $nombre, $apellido, $email, $password_hash]);

            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Usuario creado exitosamente'];
            header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/empresas');
            exit;

        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                $stmt = $this->db->prepare("SELECT * FROM empresas WHERE id = ?");
                $stmt->execute([$empresa_id]);
                $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
                $errores['email'] = 'Este email ya está registrado';
                $this->view('dashboard/crear-usuario-empresa', compact('empresa', 'errores'));
            } else {
                throw $e;
            }
        }
    }

    // ============ LISTAR CONTRATOS ============
    public function contratos(): void
    {
        $sql = "SELECT c.*, e.nombre as empresa_nombre 
                FROM contratos_empresas c
                JOIN empresas e ON c.empresa_id = e.id
                ORDER BY c.fecha_aceptacion DESC";
        $stmt = $this->db->query($sql);
        $contratos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('dashboard/contratos-consultora', compact('contratos'));
    }

    // ============ FACTURACIÓN Y ESTADÍSTICAS ============
    public function facturacion($subpage = null): void
    {
        if ($subpage === 'estadisticas') {
            // Reutilizamos la lógica del dashboard para las estadísticas detalladas
            // O podríamos cargar una vista específica con más detalles
            $this->estadisticas();
            return;
        }

        // Lógica para la página principal de facturación (Listado de facturas, etc.)
        // Por ahora, pasamos datos de ejemplo o vacíos
        $facturas = []; // Aquí iría la consulta a la tabla de facturas
        $this->view('dashboard/facturacion', compact('facturas'));
    }

    public function verEstadisticas(): void
    {
         // Lógica completa de estadísticas (similar al dashboard pero más detallada)
         // Por ahora reutilizamos la lógica básica del dashboard para no dejar vacío
         $sqlStats = "
            SELECT 
                e.nombre,
                COUNT(CASE WHEN iv.tipo_interaccion = 'ver_detalle' THEN 1 END) as vistas,
                COUNT(CASE WHEN iv.tipo_interaccion = 'click_aplicar' THEN 1 END) as aplicaciones,
                FORMAT(
                    (COUNT(CASE WHEN iv.tipo_interaccion = 'ver_detalle' THEN 1 END) * 0.10) +
                    (COUNT(CASE WHEN iv.tipo_interaccion = 'click_aplicar' THEN 1 END) * 0.15) +
                    (COUNT(CASE WHEN iv.tipo_interaccion = 'chat_consulta' THEN 1 END) * 0.05), 
                2) as facturacion_estimada
            FROM empresas e
            LEFT JOIN vacantes v ON e.id = v.empresa_id
            LEFT JOIN interacciones_vacante iv ON v.id = iv.vacante_id
            WHERE e.estado = 'activa'
            GROUP BY e.id
            ORDER BY facturacion_estimada DESC
        ";
        $topEmpresasStmt = $this->db->query($sqlStats);
        $topEmpresas = $topEmpresasStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $this->view('dashboard/estadisticas', compact('topEmpresas'));
    }

    // ============ INFORMACIÓN CONSULTORA ============
    public function info(): void
    {
        // Datos estáticos o de la BD sobre la consultora
        $info = [
            'nombre' => 'Consultores Chiriquí S.A.',
            'direccion' => 'Plaza Las Lomas, David, Chiriquí',
            'telefono' => '+507 775-0000',
            'email' => 'admin@consultoreschiriqui.com'
        ];
        $this->view('dashboard/info', compact('info'));
    }

    // ============ PERFIL DEL ADMINISTRADOR (NUEVO) ============
    public function perfil(): void
    {
        $user = Auth::user();
        
        // Obtenemos datos frescos de la BD
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$user['id']]);
        $perfil = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->view('dashboard/perfil-admin', compact('perfil'));
    }

    public function updatePerfil(): void
    {
        $user = Auth::user();
        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($nombre) || empty($email)) {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Nombre y Email son obligatorios.'];
            header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/perfil');
            exit;
        }

        try {
            // Actualizar password solo si se escribe algo
            if (!empty($password)) {
                if (strlen($password) < 6) {
                    $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'La contraseña debe tener al menos 6 caracteres.'];
                    header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/perfil');
                    exit;
                }
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $sql = "UPDATE usuarios SET nombre=?, apellido=?, email=?, password_hash=? WHERE id=?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$nombre, $apellido, $email, $hash, $user['id']]);
            } else {
                $sql = "UPDATE usuarios SET nombre=?, apellido=?, email=? WHERE id=?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$nombre, $apellido, $email, $user['id']]);
            }

            // Actualizar sesión
            $_SESSION['user']['nombre'] = $nombre;
            $_SESSION['user']['apellido'] = $apellido;
            $_SESSION['user']['email'] = $email;

            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Perfil actualizado correctamente.'];

        } catch (\PDOException $e) {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al actualizar: ' . $e->getMessage()];
        }

        header('Location: ' . ENV_APP['BASE_URL'] . '/consultora/perfil');
        exit;
    }
}
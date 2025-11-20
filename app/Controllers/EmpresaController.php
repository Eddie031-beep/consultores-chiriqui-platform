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
    }

    private function requireConsultora(): array
    {
        $user = Auth::user();
        if (!$user || ($user['rol'] ?? '') !== 'admin_consultora') {
            $this->redirect('/');
        }
        return $user;
    }

    public function index(): void
    {
        $this->requireConsultora();

        $sql = "SELECT * FROM empresas ORDER BY fecha_registro DESC";
        $stmt = $this->db->query($sql);
        $empresas = $stmt->fetchAll();

        $this->view('empresas/index', compact('empresas'));
    }

    public function create(): void
    {
        $this->requireConsultora();
        $this->view('empresas/form', [
            'modo' => 'crear',
            'empresa' => null,
            'errores' => [],
            'old' => [],
        ]);
    }

    public function store(): void
    {
        $this->requireConsultora();

        $tipo = $_POST['tipo'] ?? 'privada';
        $nombre = trim($_POST['nombre'] ?? '');
        $ruc = trim($_POST['ruc'] ?? '');
        $dv = trim($_POST['dv'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $provincia = trim($_POST['provincia'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $email = trim($_POST['email_contacto'] ?? '');
        $sitio = trim($_POST['sitio_web'] ?? '');
        $sector = trim($_POST['sector'] ?? '');

        $errores = [];

        if ($nombre === '') $errores['nombre'] = 'El nombre es obligatorio';
        if ($ruc === '') $errores['ruc'] = 'El RUC es obligatorio';
        if ($dv === '') $errores['dv'] = 'El DV es obligatorio';
        if ($direccion === '') $errores['direccion'] = 'La dirección es obligatoria';
        if ($provincia === '') $errores['provincia'] = 'La provincia es obligatoria';

        $old = compact('tipo', 'nombre', 'ruc', 'dv', 'direccion', 'provincia', 'telefono', 'email', 'sitio', 'sector');

        if ($errores) {
            $this->view('empresas/form', [
                'modo' => 'crear',
                'empresa' => null,
                'errores' => $errores,
                'old' => $old,
            ]);
            return;
        }

        try {
            $sql = "INSERT INTO empresas 
                    (tipo, nombre, ruc, dv, direccion, provincia, telefono, email_contacto, sitio_web, sector)
                    VALUES 
                    (:tipo, :nombre, :ruc, :dv, :direccion, :provincia, :telefono, :email, :sitio, :sector)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'tipo' => $tipo,
                'nombre' => $nombre,
                'ruc' => $ruc,
                'dv' => $dv,
                'direccion' => $direccion,
                'provincia' => $provincia,
                'telefono' => $telefono,
                'email' => $email,
                'sitio' => $sitio,
                'sector' => $sector,
            ]);

            $empresaId = $this->db->lastInsertId();

            // Crear contrato digital
            $contrato = "CONTRATO DIGITAL\n\n";
            $contrato .= "Entre Consultores Chiriquí S.A. y {$nombre}\n";
            $contrato .= "RUC: {$ruc}-{$dv}\n\n";
            $contrato .= "TARIFAS DE PEAJE:\n";
            $contrato .= "- Interacción básica (ver detalle): B/. 0.10\n";
            $contrato .= "- Click aplicar: B/. 0.15\n";
            $contrato .= "- Consulta chatbot: B/. 0.05\n\n";
            $contrato .= "Fecha: " . date('Y-m-d H:i:s') . "\n";
            $contrato .= "Las tarifas se facturarán mensualmente + 7% ITBMS.";

            $sqlContrato = "INSERT INTO contratos_empresas 
                            (empresa_id, version_contrato, texto_resumen, ip_aceptacion)
                            VALUES (:eid, :version, :texto, :ip)";
            $stmtContrato = $this->db->prepare($sqlContrato);
            $stmtContrato->execute([
                'eid' => $empresaId,
                'version' => 'v1.0',
                'texto' => $contrato,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            ]);

            $this->redirect('/consultora/empresas');
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                $errores['ruc'] = 'Este RUC ya está registrado';
                $this->view('empresas/form', [
                    'modo' => 'crear',
                    'empresa' => null,
                    'errores' => $errores,
                    'old' => $old,
                ]);
            } else {
                throw $e;
            }
        }
    }

    public function edit(): void
    {
        $this->requireConsultora();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            $this->redirect('/consultora/empresas');
        }

        $sql = "SELECT * FROM empresas WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $empresa = $stmt->fetch();

        if (!$empresa) {
            $this->redirect('/consultora/empresas');
        }

        $this->view('empresas/form', [
            'modo' => 'editar',
            'empresa' => $empresa,
            'errores' => [],
            'old' => [],
        ]);
    }

    public function update(): void
    {
        $this->requireConsultora();

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            $this->redirect('/consultora/empresas');
        }

        $tipo = $_POST['tipo'] ?? 'privada';
        $nombre = trim($_POST['nombre'] ?? '');
        $ruc = trim($_POST['ruc'] ?? '');
        $dv = trim($_POST['dv'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $provincia = trim($_POST['provincia'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $email = trim($_POST['email_contacto'] ?? '');
        $sitio = trim($_POST['sitio_web'] ?? '');
        $sector = trim($_POST['sector'] ?? '');
        $estado = $_POST['estado'] ?? 'activa';

        $errores = [];

        if ($nombre === '') $errores['nombre'] = 'El nombre es obligatorio';
        if ($direccion === '') $errores['direccion'] = 'La dirección es obligatoria';
        if ($provincia === '') $errores['provincia'] = 'La provincia es obligatoria';

        $old = compact('tipo', 'nombre', 'ruc', 'dv', 'direccion', 'provincia', 'telefono', 'email', 'sitio', 'sector', 'estado');

        if ($errores) {
            $empresa = array_merge(['id' => $id], $old);
            $this->view('empresas/form', [
                'modo' => 'editar',
                'empresa' => $empresa,
                'errores' => $errores,
                'old' => $old,
            ]);
            return;
        }

        $sql = "UPDATE empresas 
                SET tipo = :tipo, nombre = :nombre, direccion = :direccion, 
                    provincia = :provincia, telefono = :telefono, email_contacto = :email, 
                    sitio_web = :sitio, sector = :sector, estado = :estado
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'tipo' => $tipo,
            'nombre' => $nombre,
            'direccion' => $direccion,
            'provincia' => $provincia,
            'telefono' => $telefono,
            'email' => $email,
            'sitio' => $sitio,
            'sector' => $sector,
            'estado' => $estado,
            'id' => $id,
        ]);

        $this->redirect('/consultora/empresas');
    }

    public function createUser(): void
    {
        $this->requireConsultora();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            $this->redirect('/consultora/empresas');
        }

        $sql = "SELECT * FROM empresas WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $empresa = $stmt->fetch();

        if (!$empresa) {
            $this->redirect('/consultora/empresas');
        }

        $this->view('empresas/create-user', [
            'empresa' => $empresa,
            'errores' => [],
            'old' => [],
        ]);
    }

    public function storeUser(): void
    {
        $this->requireConsultora();

        $empresaId = isset($_POST['empresa_id']) ? (int)$_POST['empresa_id'] : 0;
        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $errores = [];

        if ($nombre === '') $errores['nombre'] = 'El nombre es obligatorio';
        if ($apellido === '') $errores['apellido'] = 'El apellido es obligatorio';
        if ($email === '') $errores['email'] = 'El email es obligatorio';
        if ($password === '') $errores['password'] = 'La contraseña es obligatoria';

        if ($errores) {
            $sql = "SELECT * FROM empresas WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $empresaId]);
            $empresa = $stmt->fetch();

            $this->view('empresas/create-user', [
                'empresa' => $empresa,
                'errores' => $errores,
                'old' => compact('nombre', 'apellido', 'email'),
            ]);
            return;
        }

        // Obtener rol empresa_admin
        $sql = "SELECT id FROM roles WHERE nombre = 'empresa_admin'";
        $rolId = $this->db->query($sql)->fetch()['id'];

        try {
            $sql = "INSERT INTO usuarios (empresa_id, nombre, apellido, email, password_hash, rol_id)
                    VALUES (:eid, :nombre, :apellido, :email, :pass, :rol)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'eid' => $empresaId,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'email' => $email,
                'pass' => $password, // En producción usar password_hash()
                'rol' => $rolId,
            ]);

            $this->redirect('/consultora/empresas');
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                $sql = "SELECT * FROM empresas WHERE id = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->execute(['id' => $empresaId]);
                $empresa = $stmt->fetch();

                $errores['email'] = 'Este email ya está registrado';
                $this->view('empresas/create-user', [
                    'empresa' => $empresa,
                    'errores' => $errores,
                    'old' => compact('nombre', 'apellido', 'email'),
                ]);
            } else {
                throw $e;
            }
        }
    }
}
<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Auth;
use PDO;

class VacanteController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db_connect('local'); // maestro
    }

    private function requireEmpresa(): array
    {
        $user = Auth::user();
        if (!$user || ($user['rol'] ?? '') !== 'empresa_admin') {
            $this->redirect('/');
        }
        if (empty($user['empresa_id'])) {
            throw new \RuntimeException('El usuario no tiene empresa asociada.');
        }
        return $user;
    }

    public function index(): void
    {
        $user = $this->requireEmpresa();
        $empresaId = (int)$user['empresa_id'];

        $sql = "SELECT v.*
                FROM vacantes v
                WHERE v.empresa_id = :eid
                ORDER BY v.fecha_publicacion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['eid' => $empresaId]);
        $vacantes = $stmt->fetchAll();

        $this->view('vacantes/index', compact('vacantes', 'user'));
    }

    public function create(): void
    {
        $this->requireEmpresa();
        $this->view('vacantes/form', [
            'modo' => 'crear',
            'vacante' => null,
            'errores' => [],
            'old' => [],
        ]);
    }

    public function store(): void
    {
        $user = $this->requireEmpresa();
        $empresaId = (int)$user['empresa_id'];

        $titulo        = trim($_POST['titulo'] ?? '');
        $descripcion   = trim($_POST['descripcion'] ?? '');
        $tipo_contrato = trim($_POST['tipo_contrato'] ?? '');
        $salario_min   = $_POST['salario_min'] !== '' ? (float)$_POST['salario_min'] : null;
        $salario_max   = $_POST['salario_max'] !== '' ? (float)$_POST['salario_max'] : null;
        $ubicacion     = trim($_POST['ubicacion'] ?? '');
        $modalidad     = $_POST['modalidad'] ?? 'presencial';

        $errores = [];

        if ($titulo === '')        $errores['titulo'] = 'El título es obligatorio';
        if ($descripcion === '')   $errores['descripcion'] = 'La descripción es obligatoria';
        if ($tipo_contrato === '') $errores['tipo_contrato'] = 'El tipo de contrato es obligatorio';
        if ($ubicacion === '')     $errores['ubicacion'] = 'La ubicación es obligatoria';

        $old = compact(
            'titulo','descripcion','tipo_contrato',
            'salario_min','salario_max','ubicacion','modalidad'
        );

        if ($errores) {
            $this->view('vacantes/form', [
                'modo'    => 'crear',
                'vacante' => null,
                'errores' => $errores,
                'old'     => $old,
            ]);
            return;
        }

        // slug simple
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $titulo), '-'));

        $sql = "INSERT INTO vacantes
                (empresa_id, titulo, slug, descripcion, tipo_contrato,
                 salario_min, salario_max, ubicacion, modalidad)
                VALUES
                (:eid, :titulo, :slug, :descripcion, :tipo_contrato,
                 :salario_min, :salario_max, :ubicacion, :modalidad)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'eid'          => $empresaId,
            'titulo'       => $titulo,
            'slug'         => $slug . '-' . time(),
            'descripcion'  => $descripcion,
            'tipo_contrato'=> $tipo_contrato,
            'salario_min'  => $salario_min,
            'salario_max'  => $salario_max,
            'ubicacion'    => $ubicacion,
            'modalidad'    => $modalidad,
        ]);

        $this->redirect('/empresa/vacantes');
    }

    public function edit(): void
    {
        $user = $this->requireEmpresa();
        $empresaId = (int)$user['empresa_id'];

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            $this->redirect('/empresa/vacantes');
        }

        $sql = "SELECT * FROM vacantes WHERE id = :id AND empresa_id = :eid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id, 'eid' => $empresaId]);
        $vacante = $stmt->fetch();

        if (!$vacante) {
            $this->redirect('/empresa/vacantes');
        }

        $this->view('vacantes/form', [
            'modo'    => 'editar',
            'vacante' => $vacante,
            'errores' => [],
            'old'     => [],
        ]);
    }

    public function update(): void
    {
        $user = $this->requireEmpresa();
        $empresaId = (int)$user['empresa_id'];

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            $this->redirect('/empresa/vacantes');
        }

        $titulo        = trim($_POST['titulo'] ?? '');
        $descripcion   = trim($_POST['descripcion'] ?? '');
        $tipo_contrato = trim($_POST['tipo_contrato'] ?? '');
        $salario_min   = $_POST['salario_min'] !== '' ? (float)$_POST['salario_min'] : null;
        $salario_max   = $_POST['salario_max'] !== '' ? (float)$_POST['salario_max'] : null;
        $ubicacion     = trim($_POST['ubicacion'] ?? '');
        $modalidad     = $_POST['modalidad'] ?? 'presencial';

        $errores = [];

        if ($titulo === '')        $errores['titulo'] = 'El título es obligatorio';
        if ($descripcion === '')   $errores['descripcion'] = 'La descripción es obligatoria';
        if ($tipo_contrato === '') $errores['tipo_contrato'] = 'El tipo de contrato es obligatorio';
        if ($ubicacion === '')     $errores['ubicacion'] = 'La ubicación es obligatoria';

        $old = compact(
            'titulo','descripcion','tipo_contrato',
            'salario_min','salario_max','ubicacion','modalidad'
        );

        if ($errores) {
            $vacante = array_merge(['id' => $id], $old);
            $this->view('vacantes/form', [
                'modo'    => 'editar',
                'vacante' => $vacante,
                'errores' => $errores,
                'old'     => $old,
            ]);
            return;
        }

        $sql = "UPDATE vacantes
                SET titulo = :titulo,
                    descripcion = :descripcion,
                    tipo_contrato = :tipo_contrato,
                    salario_min = :salario_min,
                    salario_max = :salario_max,
                    ubicacion = :ubicacion,
                    modalidad = :modalidad
                WHERE id = :id AND empresa_id = :eid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'titulo'        => $titulo,
            'descripcion'   => $descripcion,
            'tipo_contrato' => $tipo_contrato,
            'salario_min'   => $salario_min,
            'salario_max'   => $salario_max,
            'ubicacion'     => $ubicacion,
            'modalidad'     => $modalidad,
            'id'            => $id,
            'eid'           => $empresaId,
        ]);

        $this->redirect('/empresa/vacantes');
    }

    public function close(): void
    {
        $user = $this->requireEmpresa();
        $empresaId = (int)$user['empresa_id'];

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id > 0) {
            $sql = "UPDATE vacantes
                    SET estado = 'cerrada'
                    WHERE id = :id AND empresa_id = :eid";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id, 'eid' => $empresaId]);
        }

        $this->redirect('/empresa/vacantes');
    }
}

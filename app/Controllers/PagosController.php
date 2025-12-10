<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Auth;
use PDO;

class PagosController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db_connect('local');
        // Validar que sea empresa (o permitir pago público si tienes el ID, depende de tu lógica)
        // Por seguridad básica, pediremos login de empresa
        if (!Auth::check() || Auth::user()['rol'] !== 'empresa_admin') {
            // Si quieres permitir pagos sin login, quita este bloque.
            // Pero para tu prototipo, es mejor que se logueen.
            $_SESSION['mensaje'] = ['tipo' => 'warning', 'texto' => 'Inicia sesión para pagar.'];
            header('Location: ' . ENV_APP['BASE_URL'] . '/auth/login?tipo=empresa');
            exit;
        }
    }

    // VISTA: Formulario de Pago
    public function checkout($factura_id): void
    {
        // Verificar que la factura exista y pertenezca a la empresa logueada
        $user = Auth::user();
        $stmt = $this->db->prepare("SELECT * FROM facturas WHERE id = ? AND empresa_id = ?");
        $stmt->execute([$factura_id, $user['empresa_id']]);
        $factura = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$factura) {
            die("Factura no encontrada o no tienes permiso.");
        }

        if ($factura['estado'] === 'pagada') {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Esta factura ya está pagada.'];
            header('Location: ' . ENV_APP['BASE_URL'] . '/empresa/facturacion');
            exit;
        }

        $this->view('pagos/checkout', compact('factura'));
    }

    // ACCIÓN: Procesar Pago (Simulación)
    public function procesar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $factura_id = $_POST['factura_id'];
            
            // Simular proceso bancario... (sleep)
            sleep(1);

            // Actualizar estado a PAGADA
            $stmt = $this->db->prepare("UPDATE facturas SET estado = 'pagada' WHERE id = ?");
            $stmt->execute([$factura_id]);

            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => '¡Pago procesado exitosamente! Gracias.'];
            header('Location: ' . ENV_APP['BASE_URL'] . '/empresa/facturacion');
            exit;
        }
    }
}

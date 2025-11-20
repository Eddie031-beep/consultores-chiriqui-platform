<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Auth;

class DashboardController extends Controller
{
    public function home(): void
    {
        $titulo = 'Consultores Chiriquí Platform';
        $mensaje = 'Estructura base lista, BD conectada y router funcionando.';

        $this->view('dashboard/home', compact('titulo', 'mensaje'));
    }

    public function consultora(): void
    {
        if (!Auth::hasRole('admin_consultora')) {
            $this->redirectToHome();
            return;
        }

        $user = Auth::user();
        $this->view('dashboard/consultora', compact('user'));
    }

    public function empresa(): void
    {
        if (!Auth::hasRole('empresa_admin')) {
            $this->redirectToHome();
            return;
        }

        $user = Auth::user();
        $this->view('dashboard/empresa', compact('user'));
    }

    private function redirectToHome(): void
    {
        $base = ENV_APP['BASE_URL'];
        header("Location: {$base}/");
        exit;
    }
}

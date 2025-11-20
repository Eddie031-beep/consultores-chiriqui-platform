<?php
namespace App\Controllers;

use App\Core\Controller;

class DashboardController extends Controller
{
    public function home(): void
    {
        // Ejemplo: podrías más adelante leer estadísticas desde la BD aquí
        $titulo = 'Consultores Chiriquí Platform';
        $mensaje = 'Estructura base lista, BD conectada y router funcionando.';

        $this->view('dashboard/home', compact('titulo', 'mensaje'));
    }
}

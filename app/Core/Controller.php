<?php
namespace App\Core;

class Controller
{
    /**
     * Renderiza una vista dentro de app/Views/
     * Ejemplo: view('dashboard/home', ['nombre' => 'Will']);
     */
    protected function view(string $template, array $data = []): void
    {
        extract($data);
        $file = __DIR__ . '/../Views/' . $template . '.php';

        if (!file_exists($file)) {
            throw new \RuntimeException("Vista {$template} no encontrada en {$file}");
        }

        require $file;
    }

    /**
     * Redirección sencilla relativa a BASE_URL
     * Ejemplo: redirect('/empresa/vacantes');
     */
    protected function redirect(string $path = '/'): void
    {
        $base = ENV_APP['BASE_URL'];
        $path = '/' . ltrim($path, '/');
        header("Location: {$base}{$path}");
        exit;
    }
}

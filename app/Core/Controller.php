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
}

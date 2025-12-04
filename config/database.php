<?php
// config/database.php
require_once __DIR__ . '/env.php';

/**
 * Conecta a la base de datos simulando balanceo de carga.
 * - 'read'  -> Intenta conectar a la Réplica (Ubuntu). Si falla, usa Local.
 * - 'write' -> Conecta siempre a Local (Windows/Maestro).
 */
function db_connect(string $intent = 'write'): PDO
{
    // Por defecto conectamos al Maestro (write)
    $target = 'local';
    
    // Si la intención es leer, intentamos ir a la réplica
    if ($intent === 'read') {
        $target = 'replica';
    }

    // Validación de seguridad si la config no existe
    if (!defined('ENV_DB') || !isset(ENV_DB[$target])) {
        if ($target === 'replica' && isset(ENV_DB['local'])) {
            $target = 'local'; // Fallback al maestro
        } else {
            throw new RuntimeException("Configuración de BD '$target' no encontrada.");
        }
    }

    $cfg = ENV_DB[$target];

    try {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $cfg['host'], $cfg['port'], $cfg['db'], $cfg['charset']
        );

        return new PDO($dsn, $cfg['user'], $cfg['pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT            => 2, // Timeout rápido para no bloquear si la réplica cae
        ]);

    } catch (PDOException $e) {
        // Failover: Si la réplica falla, conectamos silenciosamente al Maestro
        if ($target === 'replica') {
            return db_connect('write');
        }
        throw $e;
    }
}

<?php
namespace App\Helpers;

class Auth
{
    public static function login(array $user): void
    {
        Session::set('user', [
            'id'        => $user['id'],
            'empresa_id'=> $user['empresa_id'],
            'nombre'    => $user['nombre'],
            'apellido'  => $user['apellido'],
            'email'     => $user['email'],
            'rol'       => $user['rol_nombre'] ?? null,
        ]);
    }

    public static function user(): ?array
    {
        return Session::get('user');
    }

    public static function logout(): void
    {
        Session::forget('user');
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function hasRole(string $role): bool
    {
        $u = self::user();
        return $u && isset($u['rol']) && $u['rol'] === $role;
    }
}

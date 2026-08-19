<?php

/**
 * Helper: Verifica si el usuario logueado tiene un permiso específico.
 * Uso en vistas: <?php if (puede('catalogos.administrar')): ?>
 *
 * @param string $permiso Clave del permiso a verificar
 * @return bool
 */
if (! function_exists('puede')) {
    function puede(string $permiso): bool
    {
        $permisos = session()->get('permisos') ?? [];
        return in_array($permiso, $permisos, true);
    }
}

/**
 * Helper: Verifica si el usuario logueado tiene AL MENOS UNO de los permisos indicados.
 *
 * @param array $permisos Array de claves de permisos
 * @return bool
 */
if (! function_exists('puedeAlguno')) {
    function puedeAlguno(array $permisos): bool
    {
        $permisosUsuario = session()->get('permisos') ?? [];
        return count(array_intersect($permisos, $permisosUsuario)) > 0;
    }
}

/**
 * Helper: Verifica si el usuario tiene TODOS los permisos indicados.
 *
 * @param array $permisos Array de claves de permisos
 * @return bool
 */
if (! function_exists('puedeTodos')) {
    function puedeTodos(array $permisos): bool
    {
        $permisosUsuario = session()->get('permisos') ?? [];
        return count(array_intersect($permisos, $permisosUsuario)) === count($permisos);
    }
}

/**
 * Helper: Devuelve el nombre completo del usuario logueado o 'Invitado'.
 *
 * @return string
 */
if (! function_exists('usuarioNombre')) {
    function usuarioNombre(): string
    {
        return session()->get('nombre_completo') ?? 'Invitado';
    }
}

/**
 * Helper: Devuelve true si hay sesión activa.
 *
 * @return bool
 */
if (! function_exists('estaLogueado')) {
    function estaLogueado(): bool
    {
        return (bool) session()->get('logueado');
    }
}
<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PermissionFilter implements FilterInterface
{
    /**
     * Antes del controlador: verifica autenticación y permisos.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Verificar sesión (AuthFilter debería haber pasado primero, pero reforzamos)
        if (! session()->get('logueado')) {
            return redirect()->to('/login')->with('error', 'Debe iniciar sesión para acceder.');
        }

        // 2. Sin permisos exigidos = solo requiere estar logueado
        if (empty($arguments)) {
            return;
        }

        $permisosUsuario = session()->get('permisos') ?? [];

        // 3. Verificar si tiene AL MENOS UNO de los permisos requeridos
        $tieneAcceso = count(array_intersect($arguments, $permisosUsuario)) > 0;

        if (! $tieneAcceso) {
            // Redirigir al dashboard con mensaje de error en lugar de texto plano
            return redirect()->to('/dashboard')
                ->with('error', 'No cuenta con los permisos necesarios para realizar esta acción.');
        }
    }

    /**
     * Después del controlador: sin lógica adicional.
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No se requiere lógica posterior
    }
}
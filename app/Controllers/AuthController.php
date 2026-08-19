<?php

namespace App\Controllers;

use App\Services\AuthService;

class AuthController extends BaseController
{
    protected AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function login()
    {
        // Si ya hay sesión activa, no tiene caso mostrar el login
        if (session()->get('logueado')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function attemptLogin()
    {
        $reglas = [
            'nombre_usuario' => 'required|min_length[4]',
            'password'       => 'required|min_length[6]',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()
                ->withInput()
                ->with('errores', $this->validator->getErrors());
        }

        $resultado = $this->authService->intentarLogin(
            $this->request->getPost('nombre_usuario'),
            $this->request->getPost('password')
        );

        if (! $resultado['exito']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $resultado['mensaje']);
        }

        // Regenerar ID de sesión: previene session fixation
        session()->regenerate(true);

        session()->set([
            'logueado'        => true,
            'usuario_id'      => $resultado['usuario']['id'],
            'nombre_usuario'  => $resultado['usuario']['nombre_usuario'],
            'nombre_completo' => $resultado['usuario']['nombre_completo'],
            'roles'           => $resultado['usuario']['roles'],
            'permisos'        => $resultado['usuario']['permisos'],
        ]);

        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('mensaje', 'Sesión finalizada.');
    }
}
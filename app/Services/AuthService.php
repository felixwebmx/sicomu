<?php

namespace App\Services;

use App\Models\UsuarioModel;
use App\Models\RolModel;

class AuthService
{
    protected UsuarioModel $usuarioModel;
    protected RolModel $rolModel;

    /** Número máximo de intentos antes de bloquear la cuenta */
    protected int $maxIntentos = 5;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->rolModel     = new RolModel();
    }

    /**
     * Intenta autenticar. Retorna un array con:
     * ['exito' => bool, 'mensaje' => string, 'usuario' => array|null]
     */
    public function intentarLogin(string $nombreUsuario, string $password): array
    {
        $usuario = $this->usuarioModel
            ->where('nombre_usuario', $nombreUsuario)
            ->first();

        // Respuesta genérica: nunca revelamos si el usuario existe o no
        $mensajeGenerico = 'Usuario o contraseña incorrectos.';

        if (! $usuario) {
            return ['exito' => false, 'mensaje' => $mensajeGenerico, 'usuario' => null];
        }

        if ($usuario['estatus'] === 'bloqueado') {
            return [
                'exito'   => false,
                'mensaje' => 'Cuenta bloqueada por seguridad. Contacte al administrador.',
                'usuario' => null,
            ];
        }

        if ($usuario['estatus'] === 'inactivo') {
            return ['exito' => false, 'mensaje' => $mensajeGenerico, 'usuario' => null];
        }

        if (! password_verify($password, $usuario['password_hash'])) {
            $this->registrarIntentoFallido($usuario);
            return ['exito' => false, 'mensaje' => $mensajeGenerico, 'usuario' => null];
        }

        // Login correcto: reiniciar contador y registrar acceso
        $this->usuarioModel->update($usuario['id'], [
            'intentos_fallidos' => 0,
            'ultimo_acceso'     => date('Y-m-d H:i:s'),
        ]);

        return [
            'exito'   => true,
            'mensaje' => 'Bienvenido.',
            'usuario' => $this->construirDatosSesion($usuario['id']),
        ];
    }

    protected function registrarIntentoFallido(array $usuario): void
    {
        $intentos = $usuario['intentos_fallidos'] + 1;

        $data = ['intentos_fallidos' => $intentos];

        if ($intentos >= $this->maxIntentos) {
            $data['estatus'] = 'bloqueado';
        }

        $this->usuarioModel->update($usuario['id'], $data);
    }

    /**
     * Arma el paquete de datos que vivirá en sesión:
     * roles + permisos aplanados (solo las claves)
     */
    protected function construirDatosSesion(int $usuarioId): array
    {
        $usuario = $this->usuarioModel->find($usuarioId);

        $roles = $this->usuarioModel->db->table('roles r')
            ->select('r.id, r.nombre')
            ->join('usuario_rol ur', 'ur.rol_id = r.id')
            ->where('ur.usuario_id', $usuarioId)
            ->get()
            ->getResultArray();

        $permisos = [];
        foreach ($roles as $rol) {
            foreach ($this->rolModel->obtenerPermisos($rol['id']) as $permiso) {
                $permisos[$permiso['clave']] = true; // set único, sin duplicados
            }
        }

        return [
            'id'              => $usuario['id'],
            'nombre_usuario'  => $usuario['nombre_usuario'],
            'nombre_completo' => $usuario['nombre_completo'],
            'roles'           => array_column($roles, 'nombre'),
            'permisos'        => array_keys($permisos),
        ];
    }
}
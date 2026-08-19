<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\RolModel;

class UsuariosController extends BaseController
{
    protected UsuarioModel $usuarioModel;
    protected RolModel $rolModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->rolModel     = new RolModel();
    }

    public function index()
    {
        $data = [
            'titulo'    => 'Usuarios del Sistema',
            'usuarios'  => $this->usuarioModel->orderBy('nombre_completo', 'ASC')->findAll(),
        ];

        return view('usuarios/index_usuarios', $data);
    }

    /**
     * Muestra el formulario. Si recibe $id, es edición; si no, es alta.
     */
    public function formulario(?int $id = null)
    {
        $usuario = null;
        $rolesAsignados = [];

        if ($id !== null) {
            $usuario = $this->usuarioModel->find($id);

            if (! $usuario) {
                return redirect()->to('usuarios')->with('error', 'Usuario no encontrado.');
            }

            $rolesAsignados = array_column(
                $this->usuarioModel->obtenerConRoles($id)['roles'],
                'id'
            );
        }

        $data = [
            'titulo'          => $id ? 'Editar Usuario' : 'Nuevo Usuario',
            'usuario'         => $usuario,
            'roles'           => $this->rolModel->where('estatus', 'activo')->findAll(),
            'rolesAsignados'  => $rolesAsignados,
        ];

        return view('usuarios/formulario_usuarios', $data);
    }

    public function guardar(?int $id = null)
    {
        $esEdicion = $id !== null;

        // Reglas base
        $reglas = [
            'nombre_usuario'  => "required|min_length[4]|max_length[50]|is_unique[usuarios.nombre_usuario,id,{$id}]",
            'correo'          => "required|valid_email|is_unique[usuarios.correo,id,{$id}]",
            'nombre_completo' => 'required|min_length[3]|max_length[150]',
            'estatus'         => 'required|in_list[activo,inactivo,bloqueado]',
        ];

        // El password solo es obligatorio al crear
        $reglas['password'] = $esEdicion
            ? 'permit_empty|min_length[8]'
            : 'required|min_length[8]';

        if (! $this->validate($reglas)) {
            return redirect()->back()
                ->withInput()
                ->with('errores', $this->validator->getErrors());
        }

        $data = [
            'nombre_usuario'  => $this->request->getPost('nombre_usuario'),
            'correo'          => $this->request->getPost('correo'),
            'nombre_completo' => $this->request->getPost('nombre_completo'),
            'estatus'         => $this->request->getPost('estatus'),
        ];

        // Solo actualizamos el hash si mandaron un password nuevo
        $password = $this->request->getPost('password');
        if (! empty($password)) {
            $data['password_hash'] = $password; // el callback beforeInsert/beforeUpdate del modelo lo hashea
        }

        // Ya validamos manualmente en el controller: evitamos doble validación en el modelo
        $this->usuarioModel->skipValidation(true);

        if ($esEdicion) {
            $this->usuarioModel->update($id, $data);
            $usuarioId = $id;
        } else {
            $usuarioId = $this->usuarioModel->insert($data, true);
        }

        // Sincronizar roles asignados
        $rolesSeleccionados = $this->request->getPost('roles') ?? [];
        $this->usuarioModel->sincronizarRoles($usuarioId, array_map('intval', $rolesSeleccionados));

        return redirect()->to('usuarios')
            ->with('mensaje', $esEdicion ? 'Usuario actualizado correctamente.' : 'Usuario creado correctamente.');
    }

    public function eliminar(int $id)
    {
        // Regla de seguridad: nadie puede eliminarse a sí mismo desde esta pantalla
        if ($id === (int) session()->get('usuario_id')) {
            return redirect()->to('usuarios')->with('error', 'No puede eliminar su propio usuario.');
        }

        $this->usuarioModel->delete($id); // soft delete, gracias a useSoftDeletes

        return redirect()->to('usuarios')->with('mensaje', 'Usuario eliminado.');
    }

    /**
     * Desbloqueo manual de cuenta (reinicia intentos fallidos y reactiva)
     */
    public function desbloquear(int $id)
    {
        $this->usuarioModel->update($id, [
            'estatus'           => 'activo',
            'intentos_fallidos' => 0,
        ]);

        return redirect()->to('usuarios')->with('mensaje', 'Cuenta desbloqueada correctamente.');
    }
}
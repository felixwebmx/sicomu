<?php

namespace App\Controllers;

use App\Models\RolModel;
use App\Models\PermisoModel;

class RolesController extends BaseController
{
    protected RolModel $rolModel;
    protected PermisoModel $permisoModel;

    public function __construct()
    {
        $this->rolModel     = new RolModel();
        $this->permisoModel = new PermisoModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Roles del Sistema',
            'roles'  => $this->rolModel->orderBy('nombre', 'ASC')->findAll(),
        ];

        return view('roles/index_roles', $data);
    }

    public function formulario(?int $id = null)
    {
        $rol = null;
        $permisosAsignados = [];

        if ($id !== null) {
            $rol = $this->rolModel->find($id);

            if (! $rol) {
                return redirect()->to('roles')->with('error', 'Rol no encontrado.');
            }

            $permisosAsignados = array_column($this->rolModel->obtenerPermisos($id), 'id');
        }

        $data = [
            'titulo'              => $id ? 'Editar Rol' : 'Nuevo Rol',
            'rol'                 => $rol,
            'permisosPorModulo'   => $this->permisoModel->agrupadosPorModulo(),
            'permisosAsignados'   => $permisosAsignados,
        ];

        return view('roles/formulario_roles', $data);
    }

    public function guardar(?int $id = null)
    {
        $reglas = [
            'nombre'      => "required|min_length[3]|max_length[60]|is_unique[roles.nombre,id,{$id}]",
            'descripcion' => 'permit_empty|max_length[255]',
            'estatus'     => 'required|in_list[activo,inactivo]',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()
                ->withInput()
                ->with('errores', $this->validator->getErrors());
        }

        $data = [
            'nombre'      => $this->request->getPost('nombre'),
            'descripcion' => $this->request->getPost('descripcion'),
            'estatus'     => $this->request->getPost('estatus'),
        ];

        $this->rolModel->skipValidation(true);

        if ($id !== null) {
            $this->rolModel->update($id, $data);
            $rolId = $id;
        } else {
            $rolId = $this->rolModel->insert($data, true);
        }

        $permisosSeleccionados = $this->request->getPost('permisos') ?? [];
        $this->rolModel->sincronizarPermisos($rolId, array_map('intval', $permisosSeleccionados));

        return redirect()->to('roles')
            ->with('mensaje', $id ? 'Rol actualizado correctamente.' : 'Rol creado correctamente.');
    }

    public function eliminar(int $id)
    {
        // Regla de seguridad: evitar eliminar un rol que sigue asignado a usuarios activos
        $usuariosConEsteRol = $this->rolModel->db->table('usuario_rol')
            ->where('rol_id', $id)
            ->countAllResults();

        if ($usuariosConEsteRol > 0) {
            return redirect()->to('roles')
                ->with('error', 'No se puede eliminar: hay usuarios con este rol asignado.');
        }

        $this->rolModel->delete($id);

        return redirect()->to('roles')->with('mensaje', 'Rol eliminado.');
    }
}
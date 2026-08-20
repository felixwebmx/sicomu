<?php

namespace App\Controllers\Catalogos;

use App\Controllers\BaseController;
use App\Models\CuentaModel;

class CuentasController extends BaseController
{
    protected CuentaModel $cuentaModel;

    public function __construct()
    {
        $this->cuentaModel = new CuentaModel();
    }

    public function index()
    {
        $data = [
            'titulo'  => 'Catálogo de Cuentas',
            'cuentas' => $this->cuentaModel->orderBy('nombre_cuenta', 'ASC')->findAll(),
        ];

        return view('catalogos/cuentas/index_cuentas', $data);
    }

    public function formulario(?int $id = null)
    {
        $cuenta = $id !== null ? $this->cuentaModel->find($id) : null;

        if ($id !== null && ! $cuenta) {
            return redirect()->to('catalogos/cuentas')->with('error', 'Cuenta no encontrada.');
        }

        $data = [
            'titulo' => $id ? 'Editar Cuenta' : 'Nueva Cuenta',
            'cuenta' => $cuenta,
        ];

        return view('catalogos/cuentas/formulario_cuentas', $data);
    }

    public function guardar(?int $id = null)
    {
        $reglas = [
            'clave_cuenta'  => 'required|max_length[4]',
            'nombre_cuenta' => 'required|max_length[50]',
			'estatus'     	=> 'required|in_list[0,1]',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $data = [
            'clave_cuenta'  => $this->request->getPost('clave_cuenta'),
            'nombre_cuenta' => $this->request->getPost('nombre_cuenta'),
			'estatus'     	=> $this->request->getPost('estatus'),
        ];

        $this->cuentaModel->skipValidation(true);

        $id !== null
            ? $this->cuentaModel->update($id, $data)
            : $this->cuentaModel->insert($data);

        return redirect()->to('catalogos/cuentas')
            ->with('mensaje', $id ? 'Cuenta actualizada.' : 'Cuenta creada.');
    }

    public function eliminar(int $id)
    {
        if ($this->cuentaModel->tienePartidas($id)) {
            return redirect()->to('catalogos/cuentas')
                ->with('error', 'No se puede eliminar: existen partidas asociadas a esta cuenta.');
        }

        $this->cuentaModel->delete($id);

        return redirect()->to('catalogos/cuentas')->with('mensaje', 'Cuenta eliminada.');
    }
}
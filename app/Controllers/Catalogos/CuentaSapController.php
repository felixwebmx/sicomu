<?php

namespace App\Controllers\Catalogos;

use App\Controllers\BaseController;
use App\Models\CuentaSapModel;

class CuentaSapController extends BaseController
{
    protected CuentaSapModel $cuentaSapModel;

    public function __construct()
    {
        $this->cuentaSapModel = new CuentaSapModel();
    }

    public function index()
	{
		$busqueda = $this->request->getGet('q');

		$builder = $this->cuentaSapModel
			->orderBy('codigo_sap', 'asc');

		if (! empty($busqueda)) {
			$builder->groupStart()
				->like('codigo_sap', $busqueda, 'both', null, true)
				->orLike('descripcion', $busqueda, 'both', null, true)
				->groupEnd();
		}

		$data = [
			'titulo'     => 'Catálogo de Códigos SAP',
			'cuentasSap' => $builder->findAll(),
			'busqueda'   => $busqueda,
		];

		return view('catalogos/cuenta_sap/index_cuenta_sap', $data);
	}

    public function formulario(?int $id = null)
    {
        $cuentaSap = $id !== null ? $this->cuentaSapModel->find($id) : null;

        if ($id !== null && ! $cuentaSap) {
            return redirect()->to('catalogos/sap')->with('error', 'Código SAP no encontrado.');
        }

        return view('catalogos/cuenta_sap/formulario_cuenta_sap', [
            'titulo'    => $id ? 'Editar Código SAP' : 'Nuevo Código SAP',
            'cuentaSap' => $cuentaSap,
        ]);
    }

    public function guardar(?int $id = null)
    {
        $reglas = [
            'codigo_sap'  => 'required|max_length[20]',
            'descripcion' => 'permit_empty|max_length[100]',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $data = [
            'codigo_sap'  => trim($this->request->getPost('codigo_sap')),
            'descripcion' => trim($this->request->getPost('descripcion')),
        ];

        if ($id !== null) {
            $this->cuentaSapModel->update($id, $data);
            $mensaje = 'Código SAP actualizado.';
        } else {
            $this->cuentaSapModel->insert($data);
            $mensaje = 'Código SAP creado.';
        }

        return redirect()->to('catalogos/sap')->with('mensaje', $mensaje);
    }

    public function eliminar(int $id)
    {
        // SoftDelete: solo marca deleted_at, no borra físicamente
        $this->cuentaSapModel->delete($id);

        return redirect()->to('catalogos/sap')->with('mensaje', 'Código SAP eliminado.');
    }
}
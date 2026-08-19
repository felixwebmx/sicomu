<?php

declare(strict_types=1);

namespace App\Controllers\Catalogos;

use App\Controllers\BaseController;
use App\Models\ColoniaModel;

class ColoniasController extends BaseController
{
    protected ColoniaModel $coloniaModel;

    public function __construct()
    {
        $this->coloniaModel = new ColoniaModel();
    }

    public function index()
    {
        $data = [
            'titulo'   => 'Catálogo de Colonias',
            'colonias' => $this->coloniaModel->orderBy('nombre_colonia', 'ASC')->findAll(),
        ];

        return view('catalogos/colonias/index_colonias', $data);
    }

    public function formulario(?int $id = null)
    {
        $colonia = $id !== null ? $this->coloniaModel->find($id) : null;

        if ($id !== null && ! $colonia) {
            return redirect()->to('catalogos/colonias')->with('error', 'Colonia no encontrada.');
        }

        $data = [
            'titulo'  => $id ? 'Editar Colonia' : 'Nueva Colonia',
            'colonia' => $colonia,
        ];

        return view('catalogos/colonias/formulario_colonias', $data);
    }

    public function guardar(?int $id = null)
    {
        $reglas = [
            'nombre_colonia' => 'required|max_length[50]',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $data = [
            'nombre_colonia' => $this->request->getPost('nombre_colonia'),
        ];

        $this->coloniaModel->skipValidation(true);

        $id !== null
            ? $this->coloniaModel->update($id, $data)
            : $this->coloniaModel->insert($data);

        return redirect()->to('catalogos/colonias')
            ->with('mensaje', $id ? 'Colonia actualizada.' : 'Colonia creada.');
    }

    public function eliminar(int $id)
    {
        if ($this->coloniaModel->tieneVialidades($id)) {
            return redirect()->to('catalogos/colonias')
                ->with('error', 'No se puede eliminar: existen vialidades asociadas a esta colonia.');
        }

        $this->coloniaModel->delete($id);

        return redirect()->to('catalogos/colonias')->with('mensaje', 'Colonia eliminada.');
    }
}
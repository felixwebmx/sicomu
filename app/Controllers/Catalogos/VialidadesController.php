<?php

declare(strict_types=1);

namespace App\Controllers\Catalogos;

use App\Controllers\BaseController;
use App\Models\VialidadModel;
use App\Models\ColoniaModel;

class VialidadesController extends BaseController
{
    protected VialidadModel $vialidadModel;
    protected ColoniaModel $coloniaModel;

    public function __construct()
    {
        $this->vialidadModel = new VialidadModel();
        $this->coloniaModel  = new ColoniaModel();
    }

    public function index()
    {
        $data = [
            'titulo'     => 'Catálogo de Vialidades',
            'vialidades' => $this->vialidadModel->listarConColonia(),
        ];

        return view('catalogos/vialidades/index_vialidades', $data);
    }

    public function formulario(?int $id = null)
    {
        $vialidad = $id !== null ? $this->vialidadModel->find($id) : null;

        if ($id !== null && ! $vialidad) {
            return redirect()->to('catalogos/vialidades')->with('error', 'Vialidad no encontrada.');
        }

        $data = [
            'titulo'   => $id ? 'Editar Vialidad' : 'Nueva Vialidad',
            'vialidad' => $vialidad,
            'colonias' => $this->coloniaModel->orderBy('nombre_colonia', 'ASC')->findAll(),
        ];

        return view('catalogos/vialidades/formulario_vialidades', $data);
    }

    public function guardar(?int $id = null)
    {
        $reglas = [
            'nombre_vialidad' => 'required|max_length[50]',
            'id_colonia'      => 'required|is_natural_no_zero',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $data = [
            'nombre_vialidad' => $this->request->getPost('nombre_vialidad'),
            'id_colonia'      => $this->request->getPost('id_colonia'),
        ];

        $this->vialidadModel->skipValidation(true);

        $id !== null
            ? $this->vialidadModel->update($id, $data)
            : $this->vialidadModel->insert($data);

        return redirect()->to('catalogos/vialidades')
            ->with('mensaje', $id ? 'Vialidad actualizada.' : 'Vialidad creada.');
    }

    public function eliminar(int $id)
    {
        $this->vialidadModel->delete($id);

        return redirect()->to('catalogos/vialidades')->with('mensaje', 'Vialidad eliminada.');
    }

    public function porColoniaJson(int $idColonia)
    {
        $vialidades = $this->vialidadModel->obtenerPorColonia($idColonia);
        
        return $this->jsonResponse(true, 'OK', $vialidades);
    }
}
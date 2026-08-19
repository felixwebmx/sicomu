<?php

declare(strict_types=1);

namespace App\Controllers\Catalogos;

use App\Controllers\BaseController;
use App\Models\ProgramaModel;

class ProgramaController extends BaseController
{
    protected ProgramaModel $programaModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        $this->programaModel = new ProgramaModel();
    }

    public function index(): string
    {
        $data = [
            'titulo'    => 'Catálogo de Programas',
            'programas' => $this->programaModel->listarTodos(),
        ];

        return view('catalogos/programa/index_programa', $data);
    }

    public function formulario(int $id = null): string
    {
        $programa = $id ? $this->programaModel->find($id) : null;

        if ($id && ! $programa) {
            return redirect()->to('catalogos/programas')
                ->with('error', 'Programa no encontrado.');
        }

        $data = [
            'titulo'   => $id ? 'Editar Programa' : 'Nuevo Programa',
            'programa' => $programa,
        ];

        return view('catalogos/programa/formulario_programa', $data);
    }

    public function guardar(int $id = null)
    {
        $reglas = [
            'nombre_programa' => 'required|max_length[50]',
            'anio_programa'   => 'required|exact_length[4]|numeric',
        ];

        if (! $id) {
            $reglas['nombre_programa'] .= '|is_unique[programa.nombre_programa]';
        } else {
            $reglas['nombre_programa'] .= '|is_unique[programa.nombre_programa,id_programa,' . $id . ']';
        }

        if (! $this->validate($reglas)) {
            return redirect()->back()
                ->withInput()
                ->with('errores', $this->validator->getErrors());
        }

        $datos = [
            'nombre_programa' => $this->request->getPost('nombre_programa'),
            'anio_programa'   => $this->request->getPost('anio_programa'),
        ];

        if ($id) {
            $this->programaModel->update($id, $datos);
            session()->setFlashdata('mensaje', 'Programa actualizado correctamente.');
        } else {
            $this->programaModel->insert($datos);
            session()->setFlashdata('mensaje', 'Programa registrado correctamente.');
        }

        return redirect()->to('catalogos/programas');
    }

    public function eliminar(int $id)
    {
        $programa = $this->programaModel->find($id);

        if (! $programa) {
            return redirect()->to('catalogos/programas')
                ->with('error', 'Programa no encontrado.');
        }

        if ($this->programaModel->tieneObras($id)) {
            return redirect()->to('catalogos/programas')
                ->with('error', 'No se puede eliminar el programa porque tiene obras asociadas.');
        }

        $this->programaModel->delete($id);

        return redirect()->to('catalogos/programas')
            ->with('mensaje', 'Programa eliminado.');
    }
}
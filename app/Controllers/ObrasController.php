<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\ObraModel;
use App\Models\ProgramaModel;
use App\Models\ColoniaModel;
use App\Models\VialidadModel;

class ObrasController extends BaseController
{
    protected ObraModel $obraModel;
    protected ProgramaModel $programaModel;
    protected ColoniaModel $coloniaModel;
    protected VialidadModel $vialidadModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        $this->obraModel      = new ObraModel();
        $this->programaModel  = new ProgramaModel();
        $this->coloniaModel   = new ColoniaModel();
        $this->vialidadModel  = new VialidadModel();
    }

    public function index(): string
    {
        $data = [
            'titulo' => 'Gestión de Obras',
            'obras'  => $this->obraModel->listarConRelaciones(),
        ];

        return view('obras/index_obras', $data);
    }

    public function formulario(int $id = null): string
    {
        $obra = null;

        if ($id) {
            $obra = $this->obraModel->obtenerConRelaciones($id);
            if (! $obra) {
                return redirect()->to('obras')
                    ->with('error', 'Obra no encontrada.');
            }
        }

        $data = [
            'titulo'     => $id ? 'Editar Obra' : 'Nueva Obra',
            'obra'       => $obra,
            'programas'  => $this->programaModel->listarTodos(),
            'colonias'   => $this->coloniaModel->findAll(),
            'vialidades' => $id && $obra ? $this->vialidadModel->obtenerPorColonia($obra['id_colonia']) : [],
        ];

        return view('obras/formulario_obras', $data);
    }

    public function guardar(int $id = null)
    {
        $reglas = [
            'nombre_obra'      => 'required|max_length[100]',
            'id_programa'      => 'required|is_natural_no_zero',
            'id_colonia'       => 'required|is_natural_no_zero',
            'id_vialidad'      => 'required|is_natural_no_zero',
            'costo_total'      => 'required|decimal|greater_than[0]',
            'total_ml'         => 'required|decimal|greater_than[0]',
            'derecha'          => 'required|integer|greater_than_equal_to[0]',
            'izquierda'        => 'required|integer|greater_than_equal_to[0]',
            'por_gobierno'     => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
            'por_vecinos'      => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
            'estatus_obra'     => 'required|in_list[0,1]',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()
                ->withInput()
                ->with('errores', $this->validator->getErrors());
        }

        $porGobierno = (int) $this->request->getPost('por_gobierno');
        $porVecinos  = (int) $this->request->getPost('por_vecinos');

        if (($porGobierno + $porVecinos) !== 100) {
            return redirect()->back()
                ->withInput()
                ->with('errores', ['por_gobierno' => 'La suma de los porcentajes debe ser exactamente 100%.']);
        }

        $derecha   = (int) $this->request->getPost('derecha');
        $izquierda = (int) $this->request->getPost('izquierda');
        $totalVecinos = $derecha + $izquierda;

        if ($totalVecinos === 0 && $porVecinos > 0) {
            return redirect()->back()
                ->withInput()
                ->with('errores', ['derecha' => 'Debe haber al menos un vecino si los vecinos aportan.']);
        }

        $datos = [
            'nombre_obra'      => $this->request->getPost('nombre_obra'),
            'id_programa'      => $this->request->getPost('id_programa'),
            'id_colonia'       => $this->request->getPost('id_colonia'),
            'id_vialidad'      => $this->request->getPost('id_vialidad'),
            'costo_total'      => $this->request->getPost('costo_total'),
            'total_ml'         => $this->request->getPost('total_ml'),
            'derecha'          => $derecha,
            'izquierda'        => $izquierda,
            'por_gobierno'     => $porGobierno,
            'por_vecinos'      => $porVecinos,
            'estatus_obra'     => $this->request->getPost('estatus_obra'),
        ];

        if ($id) {
            $this->obraModel->update($id, $datos);
            session()->setFlashdata('mensaje', 'Obra actualizada correctamente.');
        } else {
            $this->obraModel->insert($datos);
            session()->setFlashdata('mensaje', 'Obra registrada correctamente.');
        }

        return redirect()->to('obras');
    }

    public function eliminar(int $id)
    {
        $obra = $this->obraModel->find($id);

        if (! $obra) {
            return redirect()->to('obras')
                ->with('error', 'Obra no encontrada.');
        }

        if ($this->obraModel->tieneVecinos($id)) {
            return redirect()->to('obras')
                ->with('error', 'No se puede eliminar la obra porque tiene vecinos asociados.');
        }

        $this->obraModel->delete($id);

        return redirect()->to('obras')
            ->with('mensaje', 'Obra eliminada.');
    }

    public function porProgramaJson(int $idPrograma)
    {
        $obras = $this->obraModel->obtenerPorPrograma($idPrograma);
        return $this->jsonResponse(true, 'OK', $obras);
    }

    public function porColoniaJson(int $idColonia)
    {
        $obras = $this->obraModel->obtenerPorColonia($idColonia);
        return $this->jsonResponse(true, 'OK', $obras);
    }
}
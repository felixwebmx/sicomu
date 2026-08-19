<?php

declare(strict_types=1);

namespace App\Controllers\Catalogos;

use App\Controllers\BaseController;
use App\Models\ConceptoModel;
use App\Models\CuentaSapModel;
use App\Models\CuentaModel;
use App\Models\PartidaModel;

class ConceptosController extends BaseController
{
    protected ConceptoModel $conceptoModel;
    protected CuentaModel $cuentaModel;
    protected PartidaModel $partidaModel;
    protected CuentaSapModel $cuentaSapModel;

    public function __construct()
    {
        $this->conceptoModel = new ConceptoModel();
        $this->cuentaSapModel = new CuentaSapModel();
        $this->cuentaModel   = new CuentaModel();
        $this->partidaModel  = new PartidaModel();
    }

    public function index()
    {
        $busqueda = $this->request->getGet('q');

        $data = [
            'titulo'    => 'Catálogo de Conceptos',
            'conceptos' => $this->conceptoModel->listarConDetalle($busqueda)->findAll(),
            'pager'     => $this->conceptoModel->pager,
            'busqueda'  => $busqueda,
        ];

        return view('catalogos/conceptos/index_conceptos', $data);
    }

    public function formulario(?int $id = null)
    {
        $concepto = $id !== null ? $this->conceptoModel->find($id) : null;

        if ($id !== null && ! $concepto) {
            return redirect()->to('catalogos/conceptos')->with('error', 'Concepto no encontrado.');
        }

        $idCuentaPreseleccionada = null;
        $partidasActuales = [];
        
        if ($concepto) {
            $partida = $this->partidaModel->find($concepto['id_partida']);
            if ($partida) {
                $idCuentaPreseleccionada = $partida['id_cuenta'];
                $partidasActuales = $this->partidaModel->obtenerPorCuenta($idCuentaPreseleccionada);
            }
        }
        
        $data = [
            'titulo'                   => $id ? 'Editar Concepto' : 'Nuevo Concepto',
            'concepto'                 => $concepto,
            'cuentas'                  => $this->cuentaModel->orderBy('nombre_cuenta', 'ASC')->findAll(),
            'idCuentaPreseleccionada'  => $idCuentaPreseleccionada,
            'partidasActuales'         => $partidasActuales,
            'cuentasSap'               => $this->cuentaSapModel->listar(),
        ];

        return view('catalogos/conceptos/formulario_conceptos', $data);
    }

    public function guardar(?int $id = null)
    {
        $reglas = [
            'id_partida'      => 'required|is_natural_no_zero',
            'cuenta_sap_id'   => 'permit_empty|integer',
            'clave_concepto'  => 'required|is_natural',
            'nombre_concepto' => 'required|max_length[100]',
            'monto_concepto'  => 'required|decimal',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $idPartida = (int) $this->request->getPost('id_partida');
        $partida   = $this->partidaModel->find($idPartida);

        if (! $partida) {
            return redirect()->back()->withInput()->with('error', 'La partida seleccionada no existe.');
        }

        $data = [
            'cuenta_sap_id'   => $this->request->getPost('cuenta_sap_id') ?: null,
            'id_cuenta'       => $partida['id_cuenta'],
            'id_partida'      => $idPartida,
            'clave_concepto'  => $this->request->getPost('clave_concepto'),
            'nombre_concepto' => $this->request->getPost('nombre_concepto'),
            'monto_concepto'  => $this->request->getPost('monto_concepto'),
        ];

        $this->conceptoModel->skipValidation(true);

        $id !== null
            ? $this->conceptoModel->update($id, $data)
            : $this->conceptoModel->insert($data);

        return redirect()->to('catalogos/conceptos')
            ->with('mensaje', $id ? 'Concepto actualizado.' : 'Concepto creado.');
    }

    public function eliminar(int $id)
    {
        $this->conceptoModel->delete($id);
        return redirect()->to('catalogos/conceptos')->with('mensaje', 'Concepto eliminado.');
    }

    public function siguienteClaveJson(int $idPartida)
    {
        $maxClave = $this->conceptoModel
            ->where('id_partida', $idPartida)
            ->selectMax('clave_concepto')
            ->first();

        $siguiente = ($maxClave['clave_concepto'] ?? 0) + 1;

        return $this->response->setJSON([
            'siguiente_clave' => $siguiente,
            'partida_id'      => $idPartida,
        ]);
    }
}
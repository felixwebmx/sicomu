<?php

declare(strict_types=1);

namespace App\Controllers\Catalogos;

use App\Controllers\BaseController;
use App\Models\PartidaModel;
use App\Models\CuentaModel;

class PartidasController extends BaseController
{
    protected PartidaModel $partidaModel;
    protected CuentaModel $cuentaModel;

    public function __construct()
    {
        $this->partidaModel = new PartidaModel();
        $this->cuentaModel  = new CuentaModel();
    }

    public function index()
    {
        $data = [
            'titulo'   => 'Catálogo de Partidas',
            'partidas' => $this->partidaModel->listarConCuenta(),
        ];

        return view('catalogos/partidas/index_partidas', $data);
    }

    public function formulario(?int $id = null)
	{
		$partida = $id !== null ? $this->partidaModel->find($id) : null;

		if ($id !== null && ! $partida) {
			return redirect()->to('catalogos/partidas')->with('error', 'Partida no encontrada.');
		}

		$data = [
			'titulo'  => $id ? 'Editar Partida' : 'Nueva Partida',
			'partida' => $partida,
			'cuentas' => $this->cuentaModel->paraSelect($partida['id_cuenta'] ?? null),
		];

		return view('catalogos/partidas/formulario_partidas', $data);
	}

    public function guardar(?int $id = null)
    {
        $reglas = [
            'clave_partida'  => 'required|is_natural',
            'nombre_partida' => 'required|max_length[50]',
            'id_cuenta'      => 'required|is_natural_no_zero',
			'estatus'        => 'required|in_list[0,1]',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $data = [
            'clave_partida'  => $this->request->getPost('clave_partida'),
            'nombre_partida' => $this->request->getPost('nombre_partida'),
            'id_cuenta'      => $this->request->getPost('id_cuenta'),
        ];

        $this->partidaModel->skipValidation(true);

        $id !== null
            ? $this->partidaModel->update($id, $data)
            : $this->partidaModel->insert($data);

        return redirect()->to('catalogos/partidas')
            ->with('mensaje', $id ? 'Partida actualizada.' : 'Partida creada.');
    }

    public function eliminar(int $id)
    {
        if ($this->partidaModel->tieneConceptos($id)) {
            return redirect()->to('catalogos/partidas')
                ->with('error', 'No se puede eliminar: existen conceptos asociados a esta partida.');
        }

        $this->partidaModel->delete($id);

        return redirect()->to('catalogos/partidas')->with('mensaje', 'Partida eliminada.');
    }

    public function porCuentaJson(int $idCuenta)
	{
		return $this->response->setJSON(
			$this->partidaModel->obtenerPorCuenta($idCuenta) // sin $idActual → solo activas
		);
	}

    public function siguienteClaveJson(int $idCuenta)
    {
        $maxClave = $this->partidaModel
            ->where('id_cuenta', $idCuenta)
            ->selectMax('clave_partida')
            ->first();

        $siguiente = ($maxClave['clave_partida'] ?? 0) + 1;

        return $this->response->setJSON([
            'siguiente_clave' => $siguiente,
            'cuenta_id'       => $idCuenta,
        ]);
    }
}
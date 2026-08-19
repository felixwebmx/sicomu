<?php

namespace App\Controllers;

use App\Models\VecinoModel;
use App\Models\ObraModel;
use App\Models\ColoniaModel;
use App\Models\VialidadModel;

class VecinosController extends BaseController
{
    protected VecinoModel $vecinoModel;
    protected ObraModel $obraModel;
    protected ColoniaModel $coloniaModel;
    protected VialidadModel $vialidadModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        $this->vecinoModel   = new VecinoModel();
        $this->obraModel     = new ObraModel();
        $this->coloniaModel  = new ColoniaModel();
        $this->vialidadModel = new VialidadModel();
    }

    /**
     * LISTADO GENERAL (con filtro opcional por obra)
     */
    public function index(): string
    {
        $idObra = $this->request->getGet('obra');
        $idObra = $idObra ? (int) $idObra : null;

        $data = [
            'titulo'     => $idObra ? 'Vecinos de la Obra' : 'Gestión de Vecinos',
            'vecinos'    => $this->vecinoModel->listarConRelaciones($idObra),
            'obras'      => $this->obraModel->where('estatus_obra', 1)->findAll(),
            'obraActual' => $idObra ? $this->obraModel->obtenerConRelaciones($idObra) : null,
            'idObra'     => $idObra,
        ];

        return view('vecinos/index_vecinos', $data);
    }

    /**
     * LISTADO DE VECINOS POR OBRA (desde el botón de obras)
     */
    public function porObra(int $idObra): string
    {
        $obra = $this->obraModel->obtenerConRelaciones($idObra);

        if (! $obra) {
            return redirect()->to('obras')
                ->with('error', 'Obra no encontrada.');
        }

        $data = [
            'titulo'     => 'Vecinos: ' . $obra['nombre_obra'],
            'vecinos'    => $this->vecinoModel->listarConRelaciones($idObra),
            'obra'       => $obra,
            'resumen'    => $this->vecinoModel->resumenPorObra($idObra),
            'idObra'     => $idObra,
        ];

        return view('vecinos/index_vecinos', $data);
    }

    /**
     * FORMULARIO (nuevo / editar)
     */
    public function formulario(int $id = null): string
    {
        $vecino = null;
        $obraPreseleccionada = null;

        if ($id) {
            $vecino = $this->vecinoModel->obtenerConRelaciones($id);
            if (! $vecino) {
                return redirect()->to('vecinos')
                    ->with('error', 'Vecino no encontrado.');
            }
            $obraPreseleccionada = $this->obraModel->find($vecino['id_obra']);
        } else {
            // Si viene ?obra=ID en la URL, preseleccionar esa obra
            $idObra = $this->request->getGet('obra');
            if ($idObra) {
                $obraPreseleccionada = $this->obraModel->find((int) $idObra);
            }
        }

        $data = [
            'titulo'              => $id ? 'Editar Vecino' : 'Nuevo Vecino',
            'vecino'              => $vecino,
            'obraPreseleccionada' => $obraPreseleccionada,
            'obras'               => $this->obraModel->where('estatus_obra', 1)->findAll(),
            'colonias'            => $this->coloniaModel->findAll(),
            'vialidades'          => $vecino ? $this->vialidadModel->obtenerPorColonia($vecino['id_colonia']) : [],
        ];

        return view('vecinos/formulario_vecinos', $data);
    }

    /**
     * GUARDAR (insert / update)
     */
    public function guardar(int $id = null)
    {
        $reglas = [
            'nombre_vecino'  => 'required|max_length[50]',
            'id_obra'        => 'required|is_natural_no_zero',
            'id_colonia'     => 'required|is_natural_no_zero',
            'id_vialidad'    => 'required|is_natural_no_zero',
            'no_exterior'    => 'required|max_length[5]',
            'no_bis'         => 'permit_empty|max_length[2]',
            'no_interior'    => 'permit_empty|max_length[5]',
            'ml'             => 'required|decimal|greater_than[0]',
            'acera'          => 'required|in_list[D,I]',
            'estatus_vecino' => 'required|in_list[0,1]',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()
                ->withInput()
                ->with('errores', $this->validator->getErrors());
        }

        // Obtener la obra para calcular costo_ml
        $idObra = (int) $this->request->getPost('id_obra');
        $obra = $this->obraModel->find($idObra);

        if (! $obra) {
            return redirect()->back()
                ->withInput()
                ->with('errores', ['id_obra' => 'La obra seleccionada no existe.']);
        }

        $ml = (float) $this->request->getPost('ml');

        $datos = [
            'nombre_vecino'  => $this->request->getPost('nombre_vecino'),
            'id_obra'        => $idObra,
            'id_colonia'     => $this->request->getPost('id_colonia'),
            'id_vialidad'    => $this->request->getPost('id_vialidad'),
            'no_exterior'    => $this->request->getPost('no_exterior'),
            'no_bis'         => $this->request->getPost('no_bis') ?? '',
            'no_interior'    => $this->request->getPost('no_interior') ?? '',
            'ml'             => $ml,
            'costo_ml'       => $obra['costo_x_ml'],  // ← Toma de la obra
            'acera'          => $this->request->getPost('acera'),
            'estatus_vecino' => $this->request->getPost('estatus_vecino'),
        ];

        if ($id) {
            // Update: mantener pagado actual y recalcular resto
            $vecinoActual = $this->vecinoModel->find($id);
            $datos['pagado'] = $vecinoActual['pagado'] ?? 0;
            $datos['ultimo_pago'] = $vecinoActual['ultimo_pago'] ?? 0;
            $datos['fecha_ultimo_pago'] = $vecinoActual['fecha_ultimo_pago'] ?? null;

            if (! $this->vecinoModel->update($id, $datos)) {
                return redirect()->back()
                    ->withInput()
                    ->with('errores', $this->vecinoModel->errors());
            }
            session()->setFlashdata('mensaje', 'Vecino actualizado correctamente.');
        } else {
            // Insert
            if (! $this->vecinoModel->insert($datos)) {
                return redirect()->back()
                    ->withInput()
                    ->with('errores', $this->vecinoModel->errors());
            }
            session()->setFlashdata('mensaje', 'Vecino registrado correctamente.');
        }

        // Redirigir a la obra si venía desde allí
        $redirectObra = $this->request->getPost('redirect_obra');
        if ($redirectObra) {
            return redirect()->to('obras/vecinos/' . $redirectObra);
        }

        return redirect()->to('vecinos');
    }

    /**
     * ELIMINAR
     */
	public function eliminar(int $id)
	{
		$vecino = $this->vecinoModel->find($id);
		if (! $vecino) {
			return redirect()->to('vecinos')->with('error', 'Vecino no encontrado.');
		}
		if (! $this->vecinoModel->delete($id)) {
			return redirect()->to('vecinos')->with('error', 'Error al eliminar el vecino.');
		}
		return redirect()->to('vecinos')->with('mensaje', 'Vecino eliminado correctamente.');
	}

    /**
     * AJAX: Obtener datos de obra (costo_x_ml) para cálculo en frontend
     */
    public function datosObraJson(int $idObra)
    {
        $obra = $this->obraModel->find($idObra);
        
        if (! $obra) {
            return $this->jsonResponse(false, 'Obra no encontrada.', [], 404);
        }

        return $this->jsonResponse(true, 'OK', [
            'costo_x_ml'     => $obra['costo_x_ml'],
            'nombre_obra'    => $obra['nombre_obra'],
            'costo_total'    => $obra['costo_total'],
            'monto_vecinos'  => $obra['monto_vecinos'],
        ]);
    }
	/**
	 * AJAX: Server-Side Processing para DataTables
	 */
	public function ajaxListado()
	{
		$request = service('request');
		$idObra = $request->getGet('obra') ? (int) $request->getGet('obra') : null;

		// Parámetros de DataTables
		$draw = (int) $request->getGet('draw');
		$start = (int) $request->getGet('start');
		$length = (int) $request->getGet('length');
		$search = $request->getGet('search')['value'] ?? '';
		$orderColumn = $request->getGet('order')[0]['column'] ?? 0;
		$orderDir = $request->getGet('order')[0]['dir'] ?? 'asc';

		// Mapeo de columnas
		$columns = [
			'vecinos.id_vecino',
			'vecinos.nombre_vecino',
			null, // dirección (compuesta)
			'obras.nombre_obra',
			'vecinos.acera',
			'vecinos.ml',
			'vecinos.costo_ml',
			'vecinos.total_aportacion',
			'vecinos.pagado',
			'vecinos.resto',
			null, // último pago (compuesta)
			'vecinos.estatus_vecino',
			null, // acciones
		];

		$builder = $this->vecinoModel
			->select('
				vecinos.id_vecino,
				vecinos.nombre_vecino,
				vecinos.no_exterior,
				vecinos.no_bis,
				vecinos.no_interior,
				vecinos.ml,
				vecinos.costo_ml,
				vecinos.total_aportacion,
				vecinos.pagado,
				vecinos.resto,
				vecinos.acera,
				vecinos.fecha_ultimo_pago,
				vecinos.ultimo_pago,
				vecinos.estatus_vecino,
				obras.nombre_obra,
				colonia.nombre_colonia,
				vialidad.nombre_vialidad
			')
			->join('obras', 'obras.id_obra = vecinos.id_obra')
			->join('colonia', 'colonia.id_colonia = vecinos.id_colonia')
			->join('vialidad', 'vialidad.id_vialidad = vecinos.id_vialidad');

		// Filtro por obra
		if ($idObra) {
			$builder->where('vecinos.id_obra', $idObra);
		}

		// Búsqueda global
		if (!empty($search)) {
			$builder->groupStart()
				->like('vecinos.nombre_vecino', $search)
				->orLike('vecinos.no_exterior', $search)
				->orLike('obras.nombre_obra', $search)
				->orLike('colonia.nombre_colonia', $search)
				->orLike('vialidad.nombre_vialidad', $search)
				->groupEnd();
		}

		// Contar total filtrado
		$recordsFiltered = $builder->countAllResults(false);

		// Ordenamiento
		if (isset($columns[$orderColumn]) && $columns[$orderColumn]) {
			$builder->orderBy($columns[$orderColumn], $orderDir);
		}

		// Paginación
		$builder->limit($length, $start);

		$data = $builder->findAll();

		// Contar total sin filtro
		$recordsTotal = $this->vecinoModel->countAllResults();

		return $this->response->setJSON([
			'draw' => $draw,
			'recordsTotal' => $recordsTotal,
			'recordsFiltered' => $recordsFiltered,
			'data' => $data,
		]);
	}
}
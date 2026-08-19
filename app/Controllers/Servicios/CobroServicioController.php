<?php

namespace App\Controllers\Servicios;

use App\Controllers\BaseController;
use App\Models\CajaAperturaModel;
use App\Models\CobroModel;
use App\Models\ConceptoModel;
use App\Services\CobroServicioService;
use RuntimeException;

class CobroServicioController extends BaseController
{
    protected ConceptoModel $conceptoModel;
    protected CajaAperturaModel $aperturaModel;
    protected CobroModel $cobroModel;
    protected CobroServicioService $cobroService;

    public function __construct()
    {
        $this->conceptoModel = new ConceptoModel();
        $this->aperturaModel = new CajaAperturaModel();
        $this->cobroModel    = new CobroModel();
        $this->cobroService  = new CobroServicioService();
    }

    /**
     * Pantalla "Caja" (cobro de servicios). Requiere caja abierta.
     */
    public function index()
    {
        $apertura = $this->aperturaAbiertaOFallar();

        if ($apertura instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $apertura;
        }

        return view('servicios/cobro_servicio', [
            'titulo'   => 'Caja - Cobro de Servicios',
            'apertura' => $apertura,
            'fecha'    => date('d/m/Y'),
        ]);
    }

    /**
     * Endpoint AJAX para el buscador tipo Select2 de conceptos.
     */
    public function buscarConceptoJson()
    {
        $termino = (string) $this->request->getGet('q');

        $conceptos = $this->conceptoModel
            ->listarConDetalle($termino)
            ->findAll(20);

        $resultados = array_map(static fn ($c) => [
            'id'             => $c['id_concepto'],
            'text'           => "{$c['clave_concepto']} - {$c['nombre_concepto']}",
            'nombre_cuenta'  => $c['nombre_cuenta'],
            'nombre_partida' => $c['nombre_partida'],
            'monto'          => $c['monto_concepto'],
        ], $conceptos);

        return $this->response->setJSON(['results' => $resultados]);
    }

    /**
     * Guarda el cobro completo (encabezado + detalles) vía AJAX (JSON).
     */
    public function guardar()
    {
        $apertura = $this->aperturaModel->aperturaDeUsuario((int) session()->get('usuario_id'));

        if (! $apertura) {
            return $this->jsonResponse(false, 'No tiene una caja abierta.', [], 422);
        }

        $payload = $this->request->getJSON(true) ?? $this->request->getPost();

        $renglones = $payload['renglones'] ?? [];

        try {
            $cobro = $this->cobroService->crear(
                $payload,
                $renglones,
                (int) $apertura['id'],
                (int) session()->get('usuario_id')
            );

            return $this->jsonResponse(true, 'Cobro registrado correctamente.', ['cobro' => $cobro]);
        } catch (RuntimeException $e) {
            return $this->jsonResponse(false, $e->getMessage(), [], 422);
        }
    }

    /**
     * Historial de cobros de la apertura actual (o del cajero logueado).
     */
    public function historial()
    {
        $usuarioId = (int) session()->get('usuario_id');
        $apertura  = $this->aperturaModel->aperturaDeUsuario($usuarioId);

        $cobros = [];
        if ($apertura) {
            $cobros = $this->cobroModel
                ->where('caja_apertura_id', $apertura['id'])
                ->orderBy('cobro_id', 'DESC')
                ->findAll();
        }

        return view('servicios/historial_servicio', [
            'titulo'   => 'Historial de Cobros',
            'cobros'   => $cobros,
            'apertura' => $apertura,
        ]);
    }

    /**
     * Detalle de un cobro (recibo).
     */
    public function detalle(int $cobroId)
    {
        $cobro = $this->cobroModel->conDetalle($cobroId);

        if (! $cobro) {
            return redirect()->to('servicios/cobro/historial')
                ->with('error', 'Cobro no encontrado.');
        }

        return view('servicios/detalle_servicio', [
            'titulo' => 'Recibo - Folio ' . $cobro['numero_folio'],
            'cobro'  => $cobro,
        ]);
    }

    /**
     * Cancela un cobro (soft delete lógico).
     */
    public function cancelar(int $cobroId)
    {
        $motivo = $this->request->getPost('motivo');

        if (empty($motivo)) {
            return redirect()->back()
                ->with('error', 'Debe indicar el motivo de cancelación.');
        }

        try {
            $this->cobroService->cancelar(
                $cobroId,
                (int) session()->get('usuario_id'),
                $motivo
            );
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to('servicios/cobro/historial')
            ->with('mensaje', 'Cobro cancelado correctamente.');
    }

    /**
     * Verifica que el cajero tenga caja abierta; si no, redirige.
     */
    private function aperturaAbiertaOFallar()
    {
        $apertura = $this->aperturaModel->aperturaDeUsuario((int) session()->get('usuario_id'));

        if (! $apertura) {
            return redirect()->to(site_url('caja'))->with('error', 'Primero debe aperturar una caja.');
        }

        return $apertura;
    }
	
	public function imprimir($id)
	{
		$cobroModel    = new \App\Models\CobroModel(); 
		$detalleModel  = new \App\Models\CobroDetalleModel();
		$conceptoModel = new \App\Models\ConceptoModel(); 

		// Obtenemos el cobro principal
		$cobro = $cobroModel->find($id); 

		if (!$cobro) {
			return redirect()->to('/servicios/cobro')->with('error', 'Cobro no encontrado.');
		}

		// Obtenemos los renglones (detalles)
		$detallesBD = $detalleModel->where('cobro_id', $id)->findAll();
		$detallesParaVista = [];

		foreach ($detallesBD as $det) {
			$conceptoInfo = $conceptoModel->find($det['concepto_id']);

			$detallesParaVista[] = [
				'clave_concepto' => $conceptoInfo ? ($conceptoInfo['clave_concepto'] ?? '') : '',
				'descripcion'    => $conceptoInfo ? ($conceptoInfo['nombre_concepto'] ?? 'Concepto sin nombre') : 'Concepto Desconocido',
				'importe'        => $det['total']
			];
		}

		$version = $this->request->getGet('v') ?? 1;

		$data = [
			$cobro['cobro_id'] ?? null, // Asegurar que el ID de la BD esté disponible
			'cobro'    => $cobro,
			'detalles' => $detallesParaVista,
			'version'  => $version
		];

		return view('servicios/imprimir_servicio', $data);
	}
}
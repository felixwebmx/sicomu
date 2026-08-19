<?php

namespace App\Controllers\Aportaciones;

use App\Controllers\BaseController;
use App\Models\CajaAperturaModel;
use App\Models\CobroAportacionModel;
use App\Models\VecinoModel;
use App\Services\CobroAportacionService;
use RuntimeException;

class CobroAportacionController extends BaseController
{
    protected CajaAperturaModel $aperturaModel;
    protected CobroAportacionModel $cobroModel;
    protected VecinoModel $vecinoModel;
    protected CobroAportacionService $cobroService;

    public function __construct()
    {
        $this->aperturaModel = new CajaAperturaModel();
        $this->cobroModel    = new CobroAportacionModel();
        $this->vecinoModel   = new VecinoModel();
        $this->cobroService  = new CobroAportacionService();
    }

    private function aperturaAbiertaOFallar()
    {
        $apertura = $this->aperturaModel->aperturaDeUsuario((int) session()->get('usuario_id'));

        if (! $apertura) {
            return redirect()->to(site_url('caja'))->with('error', 'Primero debe aperturar una caja.');
        }

        return $apertura;
    }

    public function index()
    {
        $apertura = $this->aperturaAbiertaOFallar();

        if ($apertura instanceof \CodeIgniter\HTTP\RedirectResponse) {
            return $apertura;
        }

        return view('aportaciones/cobro_aportacion', [
            'titulo'   => 'Cobro de Aportaciones',
            'apertura' => $apertura,
            'fecha'    => date('d/m/Y'),
        ]);
    }

    /**
     * AJAX: buscador de vecinos con saldo pendiente.
     * Devuelve datos completos para mostrar en la tarjeta del vecino.
     */
    public function buscarVecinoJson()
    {
        $termino = (string) $this->request->getGet('q');

        $vecinos = $this->vecinoModel
            ->select('
                vecinos.id_vecino,
                vecinos.nombre_vecino,
                vecinos.total_aportacion,
                vecinos.pagado,
                vecinos.resto,
                vecinos.acera,
                vecinos.ml,
                obras.nombre_obra,
                colonia.nombre_colonia,
                vialidad.nombre_vialidad
            ')
            ->join('obras', 'obras.id_obra = vecinos.id_obra')
            ->join('colonia', 'colonia.id_colonia = vecinos.id_colonia')
            ->join('vialidad', 'vialidad.id_vialidad = vecinos.id_vialidad')
            ->where('vecinos.estatus_vecino', 1)
            ->where('vecinos.resto >', 0)
            ->groupStart()
                ->like('vecinos.nombre_vecino', $termino)
                ->orLike('obras.nombre_obra', $termino)
            ->groupEnd()
            ->orderBy('vecinos.nombre_vecino', 'ASC')
            ->findAll(20);

        $resultados = array_map(static fn ($v) => [
            'id'               => $v['id_vecino'],
            'text'             => "{$v['nombre_vecino']} - {$v['nombre_obra']} (Resto: $" . number_format((float)$v['resto'], 2) . ")",
            'nombre_vecino'    => $v['nombre_vecino'],
            'nombre_obra'      => $v['nombre_obra'],
            'nombre_colonia'   => $v['nombre_colonia'],
            'nombre_vialidad'  => $v['nombre_vialidad'],
            'acera'            => $v['acera'],
            'ml'               => (float) $v['ml'],
            'total_aportacion' => (float) $v['total_aportacion'],
            'pagado'           => (float) $v['pagado'],
            'resto'            => (float) $v['resto'],
        ], $vecinos);

        return $this->response->setJSON(['results' => $resultados]);
    }

    public function guardar()
    {
        $apertura = $this->aperturaModel->aperturaDeUsuario((int) session()->get('usuario_id'));

        if (! $apertura) {
            return $this->jsonResponse(false, 'No tiene una caja abierta.', [], 422);
        }

        $payload = $this->request->getJSON(true) ?? $this->request->getPost();

        try {
            $cobro = $this->cobroService->crear(
                $payload,
                (int) $apertura['id'],
                (int) session()->get('usuario_id')
            );

            return $this->jsonResponse(true, 'Cobro de aportación registrado correctamente.', ['cobro' => $cobro]);
        } catch (RuntimeException $e) {
            return $this->jsonResponse(false, $e->getMessage(), [], 422);
        }
    }

    public function historial()
    {
        $usuarioId = (int) session()->get('usuario_id');
        $apertura  = $this->aperturaModel->aperturaDeUsuario($usuarioId);

        $cobros = [];
        if ($apertura) {
            $cobros = $this->cobroModel
                ->select('cobro_aportaciones.*, vecinos.nombre_vecino, obras.nombre_obra')
                ->join('vecinos', 'vecinos.id_vecino = cobro_aportaciones.vecino_id')
                ->join('obras', 'obras.id_obra = vecinos.id_obra')
                ->where('cobro_aportaciones.caja_apertura_id', $apertura['id'])
                ->orderBy('cobro_aportaciones.id', 'DESC')
                ->findAll();
        }

        return view('aportaciones/historial_aportacion', [
            'titulo'   => 'Historial de Aportaciones',
            'cobros'   => $cobros,
            'apertura' => $apertura,
        ]);
    }

    public function detalle(int $id)
    {
        $cobro = $this->cobroModel->conDetalle($id);

        if (! $cobro) {
            return redirect()->to('aportaciones/cobro/historial')
                ->with('error', 'Cobro no encontrado.');
        }

        return view('aportaciones/detalle_aportacion', [
            'titulo' => 'Recibo Aportación - Folio ' . $cobro['numero_folio'],
            'cobro'  => $cobro,
        ]);
    }

    public function cancelar(int $id)
    {
        $motivo = $this->request->getPost('motivo');

        if (empty($motivo)) {
            return redirect()->back()->with('error', 'Debe indicar el motivo de cancelación.');
        }

        try {
            $this->cobroService->cancelar($id, (int) session()->get('usuario_id'), $motivo);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to('aportaciones/cobro/historial')
            ->with('mensaje', 'Cobro de aportación cancelado correctamente.');
    }
}
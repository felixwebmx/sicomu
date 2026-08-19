<?php

namespace App\Controllers\Caja;

use App\Controllers\BaseController;
use App\Models\CajaAperturaModel;
use App\Models\CajaArqueoModel;
use App\Models\CajaModel;
use App\Services\CajaService;
use App\Services\FolioService;
use RuntimeException;

class CajaController extends BaseController
{
    protected CajaModel $cajaModel;
    protected CajaAperturaModel $aperturaModel;
    protected CajaArqueoModel $arqueoModel;
    protected CajaService $cajaService;
    protected FolioService $folioService;

    public function __construct()
    {
        $this->cajaModel     = new CajaModel();
        $this->aperturaModel = new CajaAperturaModel();
        $this->arqueoModel   = new CajaArqueoModel();
        $this->cajaService   = new CajaService();
        $this->folioService  = new FolioService();
    }

    public function index()
    {
        $usuarioId = (int) session()->get('usuario_id');
        $apertura  = $this->aperturaModel->aperturaDeUsuario($usuarioId);

        // Si tiene caja abierta, al panel; si no, al formulario de apertura
        if ($apertura) {
            return redirect()->to(site_url('caja/panel'));
        }

        return view('caja/apertura', [
            'titulo'         => 'Apertura de Caja',
            'cajas'          => $this->cajaModel->activas(),
            'folioSiguiente' => $this->folioService->siguienteFolioInformativo(),
            'nombreUsuario'  => session()->get('nombre_completo'),
        ]);
    }

    public function abrir()
    {
        $reglas = [
            'caja_id'       => 'required|integer',
            'monto_inicial' => 'permit_empty|decimal',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $usuarioId = (int) session()->get('usuario_id');

        try {
            $this->cajaService->abrirCaja(
                (int) $this->request->getPost('caja_id'),
                $usuarioId,
                (float) ($this->request->getPost('monto_inicial') ?: 0)
            );
        } catch (RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to(site_url('caja/panel'))->with('mensaje', 'Caja aperturada correctamente.');
    }

    public function panel()
    {
        $usuarioId = (int) session()->get('usuario_id');
        $apertura  = $this->aperturaModel->aperturaDeUsuario($usuarioId);

        if (! $apertura) {
            return redirect()->to(site_url('caja'))->with('error', 'Primero debe aperturar una caja.');
        }

        $totales = $this->cajaService->calcularTotalesApertura($apertura['id']);

        return view('caja/panel', [
            'titulo'   => 'Caja',
            'apertura' => $apertura,
            'totales'  => $totales,
        ]);
    }

    /**
     * GET: Muestra la pantalla de cierre con el formulario.
     */
    public function cerrar()
    {
        $usuarioId = (int) session()->get('usuario_id');
        $apertura  = $this->aperturaModel->aperturaDeUsuario($usuarioId);

        if (! $apertura) {
            return redirect()->to(site_url('caja'))->with('error', 'No tiene una caja abierta.');
        }

        // Solo GET aquí. Mostramos el formulario.
        $totales = $this->cajaService->calcularTotalesApertura($apertura['id']);

        return view('caja/cierre', [
            'titulo'   => 'Cierre de Caja',
            'apertura' => $apertura,
            'totales'  => $totales,
        ]);
    }
	
	/**
     * POST: Recibe el formulario de cierre y procesa el arqueo.
     */
    public function procesarCierre()
    {
        $usuarioId = (int) session()->get('usuario_id');
        $apertura  = $this->aperturaModel->aperturaDeUsuario($usuarioId);

        if (! $apertura) {
            return redirect()->to(site_url('caja'))->with('error', 'No tiene una caja abierta.');
        }

        $efectivo = $this->request->getPost('efectivo_contado');

        if ($efectivo === null || trim((string)$efectivo) === '' || ! is_numeric($efectivo)) {
            return redirect()->to(site_url('caja/cerrar'))
                ->with('error', 'Debe capturar un monto válido en efectivo contado (ej: 1500.50).');
        }

        try {
            $this->cajaService->cerrarCaja(
                $apertura['id'],
                $usuarioId,
                (float) $efectivo,
                $this->request->getPost('observaciones')
            );
        } catch (RuntimeException $e) {
            return redirect()->to(site_url('caja/cerrar'))
                ->with('error', $e->getMessage());
        }

        return redirect()->to(site_url('caja/arqueo/' . $apertura['id']))
            ->with('mensaje', 'Caja cerrada y arqueo registrado correctamente.');
    }

    public function arqueo(int $aperturaId)
    {
        $arqueo = $this->arqueoModel->conDetallePorApertura($aperturaId);

        if (! $arqueo) {
            return redirect()->to(site_url('caja'))->with('error', 'Arqueo no encontrado.');
        }

        return view('caja/arqueo', [
            'titulo' => 'Arqueo de Caja #' . $aperturaId,
            'arqueo' => $arqueo,
        ]);
    }
	
	/**
     * Reporte Diario de Caja + Reporte de Aportaciones (si aplica).
     */
    public function reporteDiario(int $aperturaId)
    {
        $apertura = $this->aperturaModel->find($aperturaId);

        if (! $apertura) {
            return redirect()->to(site_url('caja'))->with('error', 'Apertura no encontrada.');
        }

        $arqueo       = $this->arqueoModel->conDetallePorApertura($aperturaId);
        $servicios    = $this->cajaService->obtenerServiciosPorApertura($aperturaId);
        $aportaciones = $this->cajaService->obtenerAportacionesPorApertura($aperturaId);
        $caja         = $this->cajaModel->find($apertura['caja_id']);

        return view('caja/reporte_diario', [
            'titulo'          => 'Reporte Diario de Caja',
            'apertura'        => $apertura,
            'arqueo'          => $arqueo,
            'servicios'       => $servicios,
            'aportaciones'    => $aportaciones,
            'caja'            => $caja,
            'fecha'           => date('d/m/Y', strtotime($apertura['fecha_apertura'])),
            'totalServicios'  => array_sum(array_column($servicios, 'total')),
            'totalAportaciones' => array_sum(array_column($aportaciones, 'total')),
            'hayAportaciones' => count($aportaciones) > 0,
        ]);
    }

    public function arqueos()
    {
        return view('caja/arqueos', [
            'titulo'  => 'Historial de Arqueos',
            'arqueos' => $this->arqueoModel->listarTodos(),
        ]);
    }
}
<?php

namespace App\Controllers;

use App\Models\CajaAperturaModel;
use App\Models\CajaModel;
use App\Services\CajaService;
use Config\Database;

class DashboardController extends BaseController
{
    public function index()
    {
        $usuarioId = (int) session()->get('usuario_id');
        $aperturaModel = new CajaAperturaModel();
        $cajaModel = new CajaModel();
        $cajaService = new CajaService();

        $apertura = $aperturaModel->aperturaDeUsuario($usuarioId);
        $tieneCaja = !empty($apertura);

        $totales = [];
        $ultimosCobros = [];
        $cajaNombre = null;

        if ($tieneCaja) {
            $totales = $cajaService->calcularTotalesApertura($apertura['id']);
            $caja = $cajaModel->find($apertura['caja_id']);
            $cajaNombre = $caja['nombre'] ?? '—';

            $db = Database::connect();
            $sql = "
                (SELECT c.cobro_id as id, c.numero_folio as folio,
                        c.nombre_contribuyente as nombre, c.total_cobro as monto,
                        c.fecha_cobro, 'Servicio' as tipo
                 FROM cobros c
                 WHERE c.caja_apertura_id = ? AND c.estatus_cobro = 'activo'
                 ORDER BY c.cobro_id DESC LIMIT 5)
                UNION ALL
                (SELECT ca.id as id, ca.numero_folio as folio,
                        v.nombre_vecino as nombre, ca.monto_pagado as monto,
                        ca.fecha_cobro, 'Aportación' as tipo
                 FROM cobro_aportaciones ca
                 JOIN vecinos v ON v.id_vecino = ca.vecino_id
                 WHERE ca.caja_apertura_id = ? AND ca.estatus = 'activo'
                 ORDER BY ca.id DESC LIMIT 5)
                ORDER BY fecha_cobro DESC LIMIT 5
            ";
            $ultimosCobros = $db->query($sql, [$apertura['id'], $apertura['id']])->getResultArray();
        }

        return view('dashboard/index_dashboard', [
            'titulo'        => 'Panel Principal',
            'tieneCaja'     => $tieneCaja,
            'apertura'      => $apertura,
            'cajaNombre'    => $cajaNombre,
            'totales'       => $totales,
            'ultimosCobros' => $ultimosCobros,
        ]);
    }
}
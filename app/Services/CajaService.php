<?php

namespace App\Services;

use App\Models\CajaAperturaModel;
use App\Models\CajaArqueoModel;
use App\Models\CajaModel;
use App\Models\CobroModel;
use App\Models\CobroAportacionModel;
use App\Models\FolioModel;
use RuntimeException;

class CajaService
{
    protected CajaModel $cajaModel;
    protected CajaAperturaModel $aperturaModel;
    protected CajaArqueoModel $arqueoModel;
    protected CobroModel $cobroModel;
    protected CobroAportacionModel $cobroAportacionModel;
    protected FolioModel $folioModel;
    protected FolioService $folioService;

    public function __construct()
    {
        $this->cajaModel     = new CajaModel();
        $this->aperturaModel = new CajaAperturaModel();
        $this->arqueoModel   = new CajaArqueoModel();
        $this->cobroModel    = new CobroModel();
        $this->folioModel    = new FolioModel();
        $this->folioService  = new FolioService();
        $this->cobroAportacionModel = new CobroAportacionModel();
    }

    public function abrirCaja(int $cajaId, int $usuarioId, float $montoInicial = 0): array
    {
        if ($this->aperturaModel->aperturaDeUsuario($usuarioId)) {
            throw new RuntimeException('Ya tiene una caja abierta. Debe cerrarla antes de abrir otra.');
        }

        if ($this->cajaModel->aperturaAbierta($cajaId)) {
            throw new RuntimeException('Esta caja ya fue aperturada por otro cajero y sigue abierta.');
        }

        $folioInformativo = $this->folioService->siguienteFolioInformativo();

        $id = $this->aperturaModel->insert([
            'caja_id'        => $cajaId,
            'usuario_id'     => $usuarioId,
            'fecha_apertura' => date('Y-m-d'),
            'hora_apertura'  => date('Y-m-d H:i:s'),
            'folio_inicial'  => $folioInformativo,
            'monto_inicial'  => $montoInicial,
            'estatus'        => 'abierta',
        ], true);

        return $this->aperturaModel->find($id);
    }

    public function calcularTotalesApertura(int $cajaAperturaId): array
    {
        $folios = $this->folioModel->porApertura($cajaAperturaId);
        $activos = array_filter($folios, fn ($f) => $f['estatus'] === 'activo');

        $folioInicial = $folios ? $folios[0]['numero_folio'] : null;
        $folioFinal   = $folios ? end($folios)['numero_folio'] : null;

        $totalServicios = (float) ($this->cobroModel
            ->where('caja_apertura_id', $cajaAperturaId)
            ->where('estatus_cobro', 'activo')
            ->selectSum('total_cobro', 'total')
            ->first()['total'] ?? 0);

        // ← FIX: Ahora suma las aportaciones activas reales
        $totalAportaciones = $this->cobroAportacionModel->totalPorApertura($cajaAperturaId);

        return [
            'folio_inicial'      => $folioInicial,
            'folio_final'        => $folioFinal,
            'folios_servicio'    => count(array_filter($activos, fn ($f) => $f['modulo_origen'] === 'servicio')),
            'folios_aportacion'  => count(array_filter($activos, fn ($f) => $f['modulo_origen'] === 'aportacion')),
            'total_servicios'    => $totalServicios,
            'total_aportaciones' => $totalAportaciones,
            'total_sistema'      => $totalServicios + $totalAportaciones,
        ];
    }

    public function cerrarCaja(int $cajaAperturaId, int $usuarioCierreId, float $efectivoContado, ?string $observaciones = null): array
    {
        $apertura = $this->aperturaModel->find($cajaAperturaId);

        if (! $apertura || $apertura['estatus'] !== 'abierta') {
            throw new RuntimeException('La apertura de caja no existe o ya está cerrada.');
        }

        $totales = $this->calcularTotalesApertura($cajaAperturaId);

        $totalServicios    = $totales['total_servicios'];
        $totalAportaciones = $totales['total_aportaciones'];
        $totalSistema      = $totalServicios + $totalAportaciones + (float) ($apertura['monto_inicial'] ?? 0);
        $diferencia        = $efectivoContado - $totalSistema;

        $db = \Config\Database::connect();
        $db->transStart();

        $arqueoData = [
            'caja_apertura_id'   => $cajaAperturaId,
            'total_servicios'    => $totalServicios,
            'total_aportaciones' => $totalAportaciones,
            'total_sistema'      => $totalSistema,
            'efectivo_contado'   => $efectivoContado,
            'diferencia'         => $diferencia,
            'folio_inicial'      => $totales['folio_inicial'] ?? 0,
            'folio_final'        => $totales['folio_final'] ?? 0,
            'observaciones'      => $observaciones ?? '',
            'usuario_id'         => $usuarioCierreId,
            'fecha_arqueo'       => date('Y-m-d H:i:s'),
        ];

        if (! $this->arqueoModel->insert($arqueoData)) {
            $db->transRollback();
            $errores = $this->arqueoModel->errors();
            throw new RuntimeException(
                'Error al guardar arqueo: ' . (!empty($errores) ? implode(', ', $errores) : 'Error desconocido en la base de datos.')
            );
        }

        if (! $this->aperturaModel->update($cajaAperturaId, [
            'estatus'           => 'cerrada',
            'hora_cierre'       => date('Y-m-d H:i:s'),
            'usuario_cierre_id' => $usuarioCierreId,
        ])) {
            $db->transRollback();
            throw new RuntimeException('Error al actualizar el estatus de la caja.');
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new RuntimeException('No fue posible cerrar la caja. Intente nuevamente. Detalle: ' . $db->error()['message']);
        }

        return $this->aperturaModel->find($cajaAperturaId);
    }
	
	/**
     * Solo cobros de SERVICIOS (con códigos de cuenta/partida/concepto).
     */
    public function obtenerServiciosPorApertura(int $cajaAperturaId): array
    {
        $db = \Config\Database::connect();

        $sql = "
            SELECT 
                c.cobro_id,
                c.numero_folio as folio,
                c.nombre_contribuyente as nombre,
                c.domicilio_contribuyente as domicilio,
                col.nombre_colonia as colonia,
                cd.total as monto,
                co.nombre_concepto,
                cu.clave_cuenta,
                pa.clave_partida,
                co.clave_concepto
            FROM cobros c
            LEFT JOIN colonia col ON col.id_colonia = c.id_colonia
            JOIN cobros_detalles cd ON cd.cobro_id = c.cobro_id
            JOIN conceptos co ON co.id_concepto = cd.concepto_id
            JOIN cuentas cu ON cu.id_cuenta = cd.id_cuenta
            JOIN partidas pa ON pa.id_partida = cd.id_partida
            WHERE c.caja_apertura_id = ? AND c.estatus_cobro = 'activo'
            ORDER BY c.numero_folio, cd.detalle_id
        ";

        $rows = $db->query($sql, [$cajaAperturaId])->getResultArray();
        return $this->agruparPorCobro($rows, 'servicio');
    }
	
	/**
     * Solo cobros de APORTACIONES (vecinos / obras).
     */
    public function obtenerAportacionesPorApertura(int $cajaAperturaId): array
    {
        $db = \Config\Database::connect();

        $sql = "
            SELECT 
                ca.id as cobro_id,
                ca.numero_folio as folio,
                ca.fecha_cobro,
                v.nombre_vecino as nombre,
                vi.nombre_vialidad,
                v.no_exterior,
                v.no_bis,
                v.no_interior,
                col.nombre_colonia as colonia,
                ca.monto_pagado as monto,
                'Aportación Vecinal' as nombre_concepto,
                o.id_obra,
                o.nombre_obra,
                p.nombre_programa,
                p.anio_programa
            FROM cobro_aportaciones ca
            JOIN vecinos v ON v.id_vecino = ca.vecino_id
            LEFT JOIN colonia col ON col.id_colonia = v.id_colonia
            LEFT JOIN vialidad vi ON vi.id_vialidad = v.id_vialidad
            JOIN obras o ON o.id_obra = v.id_obra
            JOIN programa p ON p.id_programa = o.id_programa
            WHERE ca.caja_apertura_id = ? AND ca.estatus = 'activo'
            ORDER BY ca.numero_folio
        ";

        $rows = $db->query($sql, [$cajaAperturaId])->getResultArray();
        return $this->agruparPorCobro($rows, 'aportacion');
    }

        /**
     * Agrupa filas por cobro_id.
     */
    private function agruparPorCobro(array $rows, string $tipo): array
    {
        $agrupados = [];
        foreach ($rows as $row) {
            $id = $row['cobro_id'] . '_' . $tipo;
            if (!isset($agrupados[$id])) {
                $agrupados[$id] = [
                    'folio'     => $row['folio'],
                    'nombre'    => $row['nombre'],
                    'domicilio' => $row['domicilio'] ?? '',
                    'colonia'   => $row['colonia'] ?? '',
                    'conceptos' => [],
                    'total'     => 0,
                ];
                if ($tipo === 'aportacion') {
                    $agrupados[$id]['id_obra']         = $row['id_obra'] ?? '';
                    $agrupados[$id]['nombre_obra']     = $row['nombre_obra'] ?? '';
                    $agrupados[$id]['nombre_programa'] = $row['nombre_programa'] ?? '';
                    $agrupados[$id]['anio_programa']   = $row['anio_programa'] ?? '';
                    $agrupados[$id]['nombre_vialidad'] = $row['nombre_vialidad'] ?? '';
                    $agrupados[$id]['no_exterior']     = $row['no_exterior'] ?? '';
                    $agrupados[$id]['no_bis']          = $row['no_bis'] ?? '';
                    $agrupados[$id]['no_interior']     = $row['no_interior'] ?? '';
                }
            }

            $codigo = '';
            if ($tipo === 'servicio') {
                $codigo = sprintf('%s-%02d-%04d',
                    $row['clave_cuenta'],
                    (int)$row['clave_partida'],
                    (int)$row['clave_concepto']
                );
            }

            $agrupados[$id]['conceptos'][] = [
                'codigo'      => $codigo,
                'nombre'      => $row['nombre_concepto'],
                'monto'       => (float)$row['monto'],
                'fecha_cobro' => $row['fecha_cobro'] ?? null,
            ];
            $agrupados[$id]['total'] += (float)$row['monto'];
        }
        return array_values($agrupados);
    }
}
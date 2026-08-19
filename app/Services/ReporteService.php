<?php

namespace App\Services;

class ReporteService
{
    /**
     * Reporte de SERVICIOS por rango de fechas.
     */
    public function reporteServiciosPorFecha(string $fechaInicio, string $fechaFin): array
    {
        $db = \Config\Database::connect();

        $sql = "
            SELECT 
                cu.clave_cuenta as cuenta,
                pa.clave_partida as partida,
                LPAD(co.clave_concepto, 4, '0') as claconcep,
                co.nombre_concepto,
                cd.total as monto,
                c.nombre_contribuyente as nombre_contribuyente,
                c.numero_folio as folio,
                DATE(c.fecha_cobro) as fecha,
                UPPER(c.estatus_cobro) as estatus_cobro
            FROM cobros c
            JOIN cobros_detalles cd ON cd.cobro_id = c.cobro_id
            JOIN conceptos co ON co.id_concepto = cd.concepto_id
            JOIN cuentas cu ON cu.id_cuenta = cd.id_cuenta
            JOIN partidas pa ON pa.id_partida = cd.id_partida
            WHERE DATE(c.fecha_cobro) BETWEEN ? AND ?
              AND c.estatus_cobro = 'activo'
            ORDER BY c.fecha_cobro, c.numero_folio, cd.detalle_id
        ";

        return $db->query($sql, [$fechaInicio, $fechaFin])->getResultArray();
    }

    /**
     * Reporte de APORTACIONES por rango de fechas.
     */
    public function reporteAportacionesPorFecha(string $fechaInicio, string $fechaFin): array
    {
        $db = \Config\Database::connect();

        $sql = "
            SELECT 
                v.nombre_vecino as nomvec,
                ca.numero_folio as foliopaq,
                o.nombre_obra as obra,
                ca.monto_pagado as monto,
                DATE(ca.fecha_cobro) as fechault,
                UPPER(ca.estatus) as status1
            FROM cobro_aportaciones ca
            JOIN vecinos v ON v.id_vecino = ca.vecino_id
            JOIN obras o ON o.id_obra = v.id_obra
            WHERE DATE(ca.fecha_cobro) BETWEEN ? AND ?
              AND ca.estatus = 'activo'
            ORDER BY ca.fecha_cobro, ca.numero_folio
        ";

        return $db->query($sql, [$fechaInicio, $fechaFin])->getResultArray();
    }

    /**
     * Reporte de VECINOS por OBRA.
     */
    public function reporteVecinosPorObra(int $obraId): array
    {
        $db = \Config\Database::connect();

        $sql = "
            SELECT 
                v.nombre_vecino as nomvec,
                vi.nombre_vialidad as vialidad,
                v.no_exterior,
                v.no_bis,
                v.no_interior,
                c.nombre_colonia as colonia,
                v.ml,
                v.costo_ml,
                v.total_aportacion,
                v.pagado,
                v.resto,
                v.acera,
                o.nombre_obra as obra,
                p.nombre_programa as programa
            FROM vecinos v
            JOIN obras o ON o.id_obra = v.id_obra
            LEFT JOIN colonia c ON c.id_colonia = v.id_colonia
            LEFT JOIN vialidad vi ON vi.id_vialidad = v.id_vialidad
            LEFT JOIN programa p ON p.id_programa = o.id_programa
            WHERE v.id_obra = ?
            ORDER BY v.nombre_vecino
        ";

        return $db->query($sql, [$obraId])->getResultArray();
    }

    /**
     * Reporte de ARQUEOS por rango de fechas.
     */
    public function reporteArqueosPorFecha(string $fechaInicio, string $fechaFin): array
    {
        $db = \Config\Database::connect();

        $sql = "
            SELECT 
                DATE(ca.fecha_arqueo) as fecha,
                c.nombre as caja_nombre,
                u_apertura.nombre_completo as cajero_apertura,
                u_cierre.nombre_completo as cajero_cierre,
                ca.folio_inicial,
                ca.folio_final,
                ca.total_servicios,
                ca.total_aportaciones,
                ca.total_sistema,
                ca.efectivo_contado,
                ca.diferencia,
                ca.observaciones
            FROM caja_arqueos ca
            JOIN caja_aperturas cap ON cap.id = ca.caja_apertura_id
            JOIN cajas c ON c.id = cap.caja_id
            JOIN usuarios u_apertura ON u_apertura.id = cap.usuario_id
            JOIN usuarios u_cierre ON u_cierre.id = ca.usuario_id
            WHERE DATE(ca.fecha_arqueo) BETWEEN ? AND ?
            ORDER BY ca.fecha_arqueo DESC, ca.id DESC
        ";

        return $db->query($sql, [$fechaInicio, $fechaFin])->getResultArray();
    }

    /**
     * Listado de obras para el select.
     */
    public function listarObras(): array
    {
        $db = \Config\Database::connect();
        return $db->table('obras')
            ->select('id_obra, nombre_obra')
            ->orderBy('nombre_obra', 'asc')
            ->get()
            ->getResultArray();
    }
}
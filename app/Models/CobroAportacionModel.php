<?php

namespace App\Models;

use CodeIgniter\Model;

class CobroAportacionModel extends Model
{
    protected $table            = 'cobro_aportaciones';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'folio_id', 'numero_folio', 'caja_apertura_id', 'usuario_id', 'vecino_id',
        'fecha_cobro', 'monto_pagado', 'metodo_pago', 'observaciones',
        'estatus', 'usuario_cancela_id', 'fecha_cancelacion', 'motivo_cancelacion',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Cobro con datos del vecino y obra.
     */
    public function conDetalle(int $id): ?array
    {
        $cobro = $this->find($id);

        if (! $cobro) {
            return null;
        }

        $cobro['vecino'] = $this->db->table('vecinos')
            ->select('vecinos.*, obras.nombre_obra, colonia.nombre_colonia, vialidad.nombre_vialidad')
            ->join('obras', 'obras.id_obra = vecinos.id_obra')
            ->join('colonia', 'colonia.id_colonia = vecinos.id_colonia')
            ->join('vialidad', 'vialidad.id_vialidad = vecinos.id_vialidad')
            ->where('vecinos.id_vecino', $cobro['vecino_id'])
            ->get()
            ->getRowArray();

        return $cobro;
    }

    /**
     * Suma de aportaciones activas dentro de una apertura.
     */
    public function totalPorApertura(int $cajaAperturaId): float
    {
        $fila = $this->selectSum('monto_pagado', 'total')
            ->where('caja_apertura_id', $cajaAperturaId)
            ->where('estatus', 'activo')
            ->first();

        return (float) ($fila['total'] ?? 0);
    }
}
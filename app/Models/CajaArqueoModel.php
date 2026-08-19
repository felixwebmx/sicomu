<?php

namespace App\Models;

use CodeIgniter\Model;

class CajaArqueoModel extends Model
{
    protected $table            = 'caja_arqueos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'caja_apertura_id', 'total_servicios', 'total_aportaciones', 'total_sistema',
        'efectivo_contado', 'diferencia', 'folio_inicial', 'folio_final',
        'observaciones', 'usuario_id', 'fecha_arqueo',
    ];
    protected $useTimestamps = false;
    //protected $createdField  = 'created_at';

    /**
     * Arqueo completo con datos de caja y cajeros.
     */
    public function conDetallePorApertura(int $cajaAperturaId): ?array
    {
        return $this->select('
                caja_arqueos.*,
                caja_aperturas.caja_id,
                caja_aperturas.fecha_apertura,
                caja_aperturas.hora_apertura,
                caja_aperturas.monto_inicial,
                cajas.nombre as caja_nombre,
                u_cierre.nombre_completo as cajero_cierre,
                u_apertura.nombre_completo as cajero_apertura
            ')
            ->join('caja_aperturas', 'caja_aperturas.id = caja_arqueos.caja_apertura_id')
            ->join('cajas', 'cajas.id = caja_aperturas.caja_id')
            ->join('usuarios u_cierre', 'u_cierre.id = caja_arqueos.usuario_id')
            ->join('usuarios u_apertura', 'u_apertura.id = caja_aperturas.usuario_id')
            ->where('caja_arqueos.caja_apertura_id', $cajaAperturaId)
            ->first();
    }

    /**
     * Listado general de arqueos para supervisión.
     */
    public function listarTodos(): array
    {
        return $this->select('
                caja_arqueos.id,
                caja_arqueos.caja_apertura_id,
                caja_arqueos.fecha_arqueo,
                caja_arqueos.total_sistema,
                caja_arqueos.efectivo_contado,
                caja_arqueos.diferencia,
                cajas.nombre as caja_nombre,
                u.nombre_completo as cajero
            ')
            ->join('caja_aperturas', 'caja_aperturas.id = caja_arqueos.caja_apertura_id')
            ->join('cajas', 'cajas.id = caja_aperturas.caja_id')
            ->join('usuarios u', 'u.id = caja_aperturas.usuario_id')
            ->orderBy('caja_arqueos.id', 'DESC')
            ->findAll();
    }
}
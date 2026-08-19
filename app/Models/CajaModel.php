<?php

namespace App\Models;

use CodeIgniter\Model;

class CajaModel extends Model
{
    protected $table            = 'cajas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nombre', 'estatus'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /**
     * Cajas activas para el select de apertura.
     */
    public function activas(): array
    {
        return $this->where('estatus', 'activa')->orderBy('nombre', 'asc')->findAll();
    }

    /**
     * Devuelve la apertura vigente ('abierta') de una caja, si existe.
     */
    public function aperturaAbierta(int $cajaId): ?array
    {
        return $this->db->table('caja_aperturas')
            ->where('caja_id', $cajaId)
            ->where('estatus', 'abierta')
            ->get()
            ->getRowArray();
    }
}
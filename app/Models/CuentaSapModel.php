<?php

namespace App\Models;

use CodeIgniter\Model;

class CuentaSapModel extends Model
{
    protected $table            = 'cuenta_sap';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
	protected $allowedFields    = ['codigo_sap', 'descripcion', 'created_at', 'updated_at', 'deleted_at'];

	// ─── Timestamps & SoftDeletes ───
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $useSoftDeletes = true;
		
    /**
     * Listado ordenado para selects.
     */
    public function listar(): array
    {
        return $this->orderBy('codigo_sap', 'asc')->findAll();
    }
}
<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class VialidadModel extends Model
{
    protected $table            = 'vialidad';
    protected $primaryKey       = 'id_vialidad';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nombre_vialidad', 'id_colonia', 'created_at', 'updated_at', 'deleted_at'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes = true;

    protected $validationRules = [
        'nombre_vialidad' => 'required|max_length[50]',
        'id_colonia'      => 'required|is_natural_no_zero',
    ];

    /**
     * Listado completo con el nombre de la colonia (join)
     */
    public function listarConColonia(): array
    {
        return $this->select('vialidad.*, colonia.nombre_colonia')
            ->join('colonia', 'colonia.id_colonia = vialidad.id_colonia')
            ->orderBy('colonia.nombre_colonia', 'ASC')
            ->orderBy('vialidad.nombre_vialidad', 'ASC')
            ->findAll();
    }

    /**
     * Vialidades de una colonia específica (para select en cascada vía AJAX)
     */
    public function obtenerPorColonia(int $idColonia): array
    {
        return $this->where('id_colonia', $idColonia)
            ->orderBy('nombre_vialidad', 'ASC')
            ->findAll();
    }
}
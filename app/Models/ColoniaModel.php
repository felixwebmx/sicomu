<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class ColoniaModel extends Model
{
    protected $table            = 'colonia';
    protected $primaryKey       = 'id_colonia';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nombre_colonia', 'created_at', 'updated_at', 'deleted_at'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes = true;

    protected $validationRules = [
        'nombre_colonia' => 'required|max_length[50]|is_unique[colonia.nombre_colonia,id,{id}]',
    ];

    protected $validationMessages = [
        'nombre_colonia' => [
            'is_unique' => 'Ya existe una colonia con ese nombre.',
        ],
    ];

    /**
     * Verifica si la colonia tiene vialidades asociadas
     */
    public function tieneVialidades(int $idColonia): bool
    {
        return $this->db->table('vialidad')
            ->where('id_colonia', $idColonia)
            ->countAllResults() > 0;
    }
}
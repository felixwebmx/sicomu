<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class ProgramaModel extends Model
{
    protected $table            = 'programa';
    protected $primaryKey       = 'id_programa';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'nombre_programa',
        'anio_programa',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes = true;

    protected $validationRules = [
        'nombre_programa' => 'required|max_length[50]|is_unique[programa.nombre_programa,id_programa,{id_programa}]',
        'anio_programa'   => 'required|exact_length[4]|numeric|greater_than[1999]|less_than[2100]',
    ];

    protected $validationMessages = [
        'nombre_programa' => [
            'is_unique' => 'Ya existe un programa con ese nombre.',
        ],
        'anio_programa' => [
            'exact_length' => 'El año debe tener exactamente 4 dígitos.',
            'numeric'      => 'El año debe ser un número válido.',
            'greater_than' => 'El año debe ser mayor a 1999.',
            'less_than'    => 'El año debe ser menor a 2100.',
        ],
    ];

    public function tieneObras(int $idPrograma): bool
    {
        return $this->db->table('obras')
            ->where('id_programa', $idPrograma)
            ->countAllResults() > 0;
    }

    public function listarTodos(): array
    {
        return $this->orderBy('anio_programa', 'DESC')
            ->orderBy('nombre_programa', 'ASC')
            ->findAll();
    }
}
<?php

namespace App\Models;

use CodeIgniter\Model;

class PermisoModel extends Model
{
    protected $table            = 'permisos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = ['clave', 'nombre', 'modulo'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'clave'  => 'required|is_unique[permisos.clave,id,{id}]',
        'nombre' => 'required|max_length[120]',
        'modulo' => 'required|max_length[60]',
    ];

    /**
     * Agrupa permisos por módulo, útil para pantallas de asignación
     */
    public function agrupadosPorModulo(): array
    {
        $permisos = $this->orderBy('modulo', 'ASC')->findAll();

        $agrupados = [];
        foreach ($permisos as $permiso) {
            $agrupados[$permiso['modulo']][] = $permiso;
        }

        return $agrupados;
    }
}
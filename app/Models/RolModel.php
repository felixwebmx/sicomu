<?php

namespace App\Models;

use CodeIgniter\Model;

class RolModel extends Model
{
    protected $table            = 'roles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = ['nombre', 'descripcion', 'estatus'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nombre' => 'required|min_length[3]|max_length[60]|is_unique[roles.nombre,id,{id}]',
    ];

    /**
     * Trae todos los permisos asociados a un rol
     */
    public function obtenerPermisos(int $rolId): array
    {
        return $this->db->table('permisos p')
            ->select('p.id, p.clave, p.nombre, p.modulo')
            ->join('rol_permiso rp', 'rp.permiso_id = p.id')
            ->where('rp.rol_id', $rolId)
            ->get()
            ->getResultArray();
    }

    /**
     * Sincroniza permisos de un rol (reemplaza el set completo)
     */
    public function sincronizarPermisos(int $rolId, array $permisoIds): void
    {
        $this->db->table('rol_permiso')->where('rol_id', $rolId)->delete();

        if (empty($permisoIds)) {
            return;
        }

        $registros = array_map(
            fn ($permisoId) => ['rol_id' => $rolId, 'permiso_id' => $permisoId],
            $permisoIds
        );

        $this->db->table('rol_permiso')->insertBatch($registros);
    }
}
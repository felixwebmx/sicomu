<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'nombre_usuario',
        'correo',
        'nombre_completo',
        'password_hash',
        'estatus',
        'intentos_fallidos',
        'ultimo_acceso',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validación a nivel de modelo (defensa adicional a la del Controller)
    protected $validationRules = [
        'nombre_usuario'  => 'required|min_length[4]|max_length[50]|is_unique[usuarios.nombre_usuario,id,{id}]',
        'correo'          => 'required|valid_email|is_unique[usuarios.correo,id,{id}]',
        'nombre_completo' => 'required|min_length[3]|max_length[150]',
    ];

    protected $validationMessages = [
        'nombre_usuario' => [
            'is_unique' => 'Ese nombre de usuario ya está en uso.',
        ],
        'correo' => [
            'is_unique' => 'Ese correo ya está registrado.',
        ],
    ];

    protected $skipValidation = false;

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Hashea el password solo si viene presente en los datos
     * (evita rehash accidental en updates que no tocan el password)
     */
    protected function hashPassword(array $data): array
    {
        if (! isset($data['data']['password_hash'])) {
            return $data;
        }

        $data['data']['password_hash'] = password_hash(
            $data['data']['password_hash'],
            PASSWORD_BCRYPT,
            ['cost' => 12]
        );

        return $data;
    }

    /**
     * Obtiene un usuario con sus roles cargados (join)
     */
    public function obtenerConRoles(int $usuarioId): ?array
    {
        $usuario = $this->find($usuarioId);

        if (! $usuario) {
            return null;
        }

        $roles = $this->db->table('roles r')
            ->select('r.id, r.nombre')
            ->join('usuario_rol ur', 'ur.rol_id = r.id')
            ->where('ur.usuario_id', $usuarioId)
            ->get()
            ->getResultArray();

        $usuario['roles'] = $roles;

        return $usuario;
    }
	/**
	 * Sincroniza los roles de un usuario (reemplaza el set completo)
	 */
	public function sincronizarRoles(int $usuarioId, array $rolIds): void
	{
		$this->db->table('usuario_rol')->where('usuario_id', $usuarioId)->delete();

		if (empty($rolIds)) {
			return;
		}

		$registros = array_map(
			fn ($rolId) => ['usuario_id' => $usuarioId, 'rol_id' => $rolId],
			$rolIds
		);

		$this->db->table('usuario_rol')->insertBatch($registros);
	}
}
<?php

namespace App\Models;

use CodeIgniter\Model;

class CuentaModel extends Model
{
    protected $table            = 'cuentas';
    protected $primaryKey       = 'id_cuenta';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    //protected $allowedFields    = ['clave_cuenta', 'nombre_cuenta', 'created_at', 'updated_at', 'deleted_at'];
	protected $allowedFields = ['clave_cuenta', 'nombre_cuenta', 'estatus', 'created_at', 'updated_at', 'deleted_at'];


    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes = true;

    protected $validationRules = [
        'clave_cuenta'  => 'required|max_length[4]',
        'nombre_cuenta' => 'required|max_length[50]',
    ];

    public function tienePartidas(int $idCuenta): bool
    {
        return $this->db->table('partidas')
            ->where('id_cuenta', $idCuenta)
            ->countAllResults() > 0;
    }
	
	public function paraSelect(?int $idActual = null): array
	{
		$this->groupStart()->where('estatus', 1);
		if ($idActual) {
			$this->orWhere('id_cuenta', $idActual);
		}
		$this->groupEnd();

		return $this->orderBy('nombre_cuenta', 'ASC')->findAll();
	}
}
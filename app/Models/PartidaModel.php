<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class PartidaModel extends Model
{
    protected $table            = 'partidas';
    protected $primaryKey       = 'id_partida';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    //protected $allowedFields    = ['clave_partida', 'nombre_partida', 'id_cuenta', 'created_at', 'updated_at', 'deleted_at'];
	protected $allowedFields = ['clave_partida', 'nombre_partida', 'id_cuenta', 'estatus', 'created_at', 'updated_at', 'deleted_at'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes = true;

    protected $validationRules = [
        'clave_partida'  => 'required|is_natural',
        'nombre_partida' => 'required|max_length[50]',
        'id_cuenta'      => 'required|is_natural_no_zero',
    ];

    /**
     * Listado completo con el nombre de la cuenta ya resuelto (join)
     */
    public function listarConCuenta(): array
    {
        return $this->select('partidas.*, cuentas.nombre_cuenta')
            ->join('cuentas', 'cuentas.id_cuenta = partidas.id_cuenta')
            ->orderBy('cuentas.nombre_cuenta', 'ASC')
            ->orderBy('partidas.nombre_partida', 'ASC')
            ->findAll();
    }

    /**
     * Partidas de una cuenta específica (para el select en cascada vía AJAX)
     */
    public function obtenerPorCuenta(int $idCuenta, ?int $idActual = null): array
	{
		$this->where('id_cuenta', $idCuenta)
			 ->groupStart()->where('estatus', 1);
		if ($idActual) {
			$this->orWhere('id_partida', $idActual);
		}
		$this->groupEnd();

		return $this->orderBy('nombre_partida', 'ASC')->findAll();
	}

    public function tieneConceptos(int $idPartida): bool
    {
        return $this->db->table('conceptos')
            ->where('id_partida', $idPartida)
            ->countAllResults() > 0;
    }
	
	
}
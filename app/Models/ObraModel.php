<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class ObraModel extends Model
{
    protected $table            = 'obras';
    protected $primaryKey       = 'id_obra';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'nombre_obra',
        'id_vialidad',
        'id_colonia',
        'id_programa',
        'costo_total',
        'total_ml',
        'derecha',
        'izquierda',
        'por_gobierno',
        'por_vecinos',
        'monto_gobierno',
        'monto_vecinos',
        'costo_x_ml',
        'fecha_captura',
        'estatus_obra',
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
        'nombre_obra'      => 'required|max_length[100]',
        'id_vialidad'      => 'required|is_natural_no_zero',
        'id_colonia'       => 'required|is_natural_no_zero',
        'id_programa'      => 'required|is_natural_no_zero',
        'costo_total'      => 'required|decimal|greater_than[0]',
        'total_ml'         => 'required|decimal|greater_than[0]',
        'derecha'          => 'required|integer|greater_than_equal_to[0]',
        'izquierda'        => 'required|integer|greater_than_equal_to[0]',
        'por_gobierno'     => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
        'por_vecinos'      => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
        'estatus_obra'     => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'costo_total' => [
            'greater_than' => 'El costo total de la obra debe ser mayor a 0.',
        ],
        'total_ml' => [
            'greater_than' => 'El total de metros lineales debe ser mayor a 0.',
        ],
    ];

    protected function calcularCampos(array $data): array
    {
        $costoTotal = (float) ($data['data']['costo_total'] ?? 0);
        $totalMl    = (float) ($data['data']['total_ml'] ?? 0);
        $porGob     = (int) ($data['data']['por_gobierno'] ?? 0);
        $porVec     = (int) ($data['data']['por_vecinos'] ?? 0);

        $data['data']['monto_gobierno'] = round($costoTotal * ($porGob / 100), 2);
        $data['data']['monto_vecinos']  = round($costoTotal * ($porVec / 100), 2);
        
        if ($totalMl > 0) {
            $data['data']['costo_x_ml'] = round($data['data']['monto_vecinos'] / $totalMl, 2);
        } else {
            $data['data']['costo_x_ml'] = 0;
        }

        if (empty($data['data']['fecha_captura'])) {
            $data['data']['fecha_captura'] = date('Y-m-d');
        }

        return $data;
    }

    public function __construct()
    {
        parent::__construct();
        $this->beforeInsert[] = 'calcularCampos';
        $this->beforeUpdate[] = 'calcularCampos';
    }

    public function listarConRelaciones(): array
    {
        return $this->select('
                obras.*,
                programa.nombre_programa,
                programa.anio_programa,
                colonia.nombre_colonia,
                vialidad.nombre_vialidad
            ')
            ->join('programa', 'programa.id_programa = obras.id_programa')
            ->join('colonia', 'colonia.id_colonia = obras.id_colonia')
            ->join('vialidad', 'vialidad.id_vialidad = obras.id_vialidad')
            ->orderBy('programa.anio_programa', 'DESC')
            ->orderBy('obras.nombre_obra', 'ASC')
            ->findAll();
    }

    public function obtenerPorPrograma(int $idPrograma): array
    {
        return $this->where('id_programa', $idPrograma)
            ->where('estatus_obra', 1)
            ->orderBy('nombre_obra', 'ASC')
            ->findAll();
    }

    public function obtenerPorColonia(int $idColonia): array
    {
        return $this->where('id_colonia', $idColonia)
            ->where('estatus_obra', 1)
            ->orderBy('nombre_obra', 'ASC')
            ->findAll();
    }

    public function tieneVecinos(int $idObra): bool
    {
        return $this->db->table('vecinos')
            ->where('id_obra', $idObra)
            ->countAllResults() > 0;
    }

    public function obtenerConRelaciones(int $idObra): ?array
    {
        return $this->select('
                obras.*,
                programa.nombre_programa,
                programa.anio_programa,
                colonia.nombre_colonia,
                vialidad.nombre_vialidad
            ')
            ->join('programa', 'programa.id_programa = obras.id_programa')
            ->join('colonia', 'colonia.id_colonia = obras.id_colonia')
            ->join('vialidad', 'vialidad.id_vialidad = obras.id_vialidad')
            ->where('obras.id_obra', $idObra)
            ->first();
    }
}
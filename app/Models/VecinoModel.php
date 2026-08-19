<?php

namespace App\Models;

use CodeIgniter\Model;

class VecinoModel extends Model
{
    protected $table            = 'vecinos';
    protected $primaryKey       = 'id_vecino';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'nombre_vecino',
        'id_vialidad',
        'no_exterior',
        'no_bis',
        'no_interior',
        'id_colonia',
        'id_obra',
        'ml',
        'costo_ml',
        'total_aportacion',
        'acera',
        'fecha_captura',
        'pagado',
        'resto',
        'fecha_ultimo_pago',
        'ultimo_pago',
        'estatus_vecino',
    ];

    protected $validationRules = [
        'nombre_vecino'  => 'required|max_length[50]',
        'id_vialidad'    => 'required|is_natural_no_zero',
        'no_exterior'    => 'required|max_length[5]',
        'no_bis'         => 'permit_empty|max_length[2]',
        'no_interior'    => 'permit_empty|max_length[5]',
        'id_colonia'     => 'required|is_natural_no_zero',
        'id_obra'        => 'required|is_natural_no_zero',
        'ml'             => 'required|decimal|greater_than[0]',
        'acera'          => 'required|in_list[D,I]',
        'estatus_vecino' => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'ml' => [
            'greater_than' => 'Los metros lineales deben ser mayor a 0.',
        ],
        'acera' => [
            'in_list' => 'La acera debe ser Derecha (D) o Izquierda (I).',
        ],
    ];

    /**
     * Callback: calcula campos automáticos antes de insert/update.
     * SOLO recalcula total_aportacion y resto cuando ml/costo_ml están presentes,
     * protegiendo updates parciales (pagos, cancelaciones) de corromper el registro.
     */
    protected function calcularCampos(array $data): array
	{
		$esInsert = ! isset($data['id']);

		// ─── Defaults en insert (ANTES de calcular resto, para que el cálculo los vea) ───
		if ($esInsert) {
			if (empty($data['data']['fecha_captura'])) {
				$data['data']['fecha_captura'] = date('Y-m-d');
			}
			if (! isset($data['data']['pagado'])) {
				$data['data']['pagado'] = 0;
			}
			if (! isset($data['data']['ultimo_pago'])) {
				$data['data']['ultimo_pago'] = 0;
			}
		}

		// ─── total_aportacion: solo si tenemos ml y costo_ml ───
		$ml      = isset($data['data']['ml']) ? (float) $data['data']['ml'] : null;
		$costoMl = isset($data['data']['costo_ml']) ? (float) $data['data']['costo_ml'] : null;

		if ($ml !== null && $costoMl !== null) {
			$data['data']['total_aportacion'] = round($ml * $costoMl, 2);
		}

		// ─── resto: solo si tenemos pagado y conocemos total_aportacion ───
		$pagado          = isset($data['data']['pagado']) ? (float) $data['data']['pagado'] : null;
		$totalAportacion = isset($data['data']['total_aportacion']) ? (float) $data['data']['total_aportacion'] : null;

		if ($pagado !== null && $totalAportacion !== null) {
			$data['data']['resto'] = round($totalAportacion - $pagado, 2);
		}

		return $data;
	}

    public function __construct()
    {
        parent::__construct();
        $this->beforeInsert[] = 'calcularCampos';
        $this->beforeUpdate[] = 'calcularCampos';
    }

    /**
     * Listado completo con joins (nombres de relaciones)
     */
    public function listarConRelaciones(?int $idObra = null): array
    {
        $builder = $this->select('
                vecinos.*,
                obras.nombre_obra,
                obras.costo_total,
                obras.costo_x_ml,
                colonia.nombre_colonia,
                vialidad.nombre_vialidad
            ')
            ->join('obras', 'obras.id_obra = vecinos.id_obra')
            ->join('colonia', 'colonia.id_colonia = vecinos.id_colonia')
            ->join('vialidad', 'vialidad.id_vialidad = vecinos.id_vialidad');

        if ($idObra !== null) {
            $builder->where('vecinos.id_obra', $idObra);
        }

        return $builder->orderBy('obras.nombre_obra', 'ASC')
            ->orderBy('vecinos.acera', 'ASC')
            ->orderBy('vecinos.nombre_vecino', 'ASC')
            ->findAll();
    }

    /**
     * Vecinos por obra (para listado interno de obra)
     */
    public function obtenerPorObra(int $idObra): array
    {
        return $this->where('id_obra', $idObra)
            ->orderBy('acera', 'ASC')
            ->orderBy('nombre_vecino', 'ASC')
            ->findAll();
    }

    /**
     * Resumen por obra: totales y estadísticas
     */
    public function resumenPorObra(int $idObra): array
    {
        $result = $this->select('
                COUNT(*) as total_vecinos,
                SUM(ml) as total_ml,
                SUM(total_aportacion) as total_aportacion,
                SUM(pagado) as total_pagado,
                SUM(resto) as total_resto,
                SUM(CASE WHEN resto <= 0 THEN 1 ELSE 0 END) as vecinos_pagados,
                SUM(CASE WHEN resto > 0 THEN 1 ELSE 0 END) as vecinos_deudores
            ')
            ->where('id_obra', $idObra)
            ->first();

        return $result ?: [
            'total_vecinos'    => 0,
            'total_ml'         => 0,
            'total_aportacion' => 0,
            'total_pagado'     => 0,
            'total_resto'      => 0,
            'vecinos_pagados'  => 0,
            'vecinos_deudores' => 0,
        ];
    }

    /**
     * Obtener un vecino con sus relaciones (para editar)
     */
    public function obtenerConRelaciones(int $idVecino): ?array
    {
        return $this->select('
                vecinos.*,
                obras.nombre_obra,
                obras.costo_x_ml,
                colonia.nombre_colonia,
                vialidad.nombre_vialidad
            ')
            ->join('obras', 'obras.id_obra = vecinos.id_obra')
            ->join('colonia', 'colonia.id_colonia = vecinos.id_colonia')
            ->join('vialidad', 'vialidad.id_vialidad = vecinos.id_vialidad')
            ->where('vecinos.id_vecino', $idVecino)
            ->first();
    }

    /**
     * Registrar un pago (acumulativo, soporta pagos parciales).
     * Actualiza pagado, resto, ultimo_pago y fecha_ultimo_pago.
     */
    public function registrarPago(int $idVecino, float $monto): bool
    {
        $vecino = $this->find($idVecino);
        if (! $vecino) {
            return false;
        }

        $nuevoPagado = (float) $vecino['pagado'] + $monto;
        $nuevoResto  = (float) $vecino['total_aportacion'] - $nuevoPagado;

        return $this->update($idVecino, [
            'pagado'            => $nuevoPagado,
            'resto'             => max(0, $nuevoResto),
            'ultimo_pago'       => $monto,
            'fecha_ultimo_pago' => date('Y-m-d'),
        ]);
    }
}
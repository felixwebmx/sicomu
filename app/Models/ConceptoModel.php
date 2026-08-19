<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class ConceptoModel extends Model
{
    protected $table            = 'conceptos';
    protected $primaryKey       = 'id_concepto';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'cuenta_sap_id', 'id_cuenta', 'id_partida', 'clave_concepto',
        'nombre_concepto', 'monto_concepto', 'created_at', 'updated_at', 'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes = true;

    protected $validationRules = [
        'id_cuenta'       => 'required|is_natural_no_zero',
        'id_partida'      => 'required|is_natural_no_zero',
        'clave_concepto'  => 'required|is_natural',
        'nombre_concepto' => 'required|max_length[100]',
        'monto_concepto'  => 'required|decimal',
    ];

    /**
     * Retorna el builder ya armado con joins y búsqueda opcional,
     * listo para .paginate() o .findAll() en el controller.
     */
    public function listarConDetalle(?string $busqueda = null): self
    {
        $this->select('conceptos.*, cuentas.nombre_cuenta, partidas.nombre_partida, cuenta_sap.codigo_sap, cuenta_sap.descripcion as sap_descripcion')
            ->join('cuentas', 'cuentas.id_cuenta = conceptos.id_cuenta')
            ->join('partidas', 'partidas.id_partida = conceptos.id_partida')
            ->join('cuenta_sap', 'cuenta_sap.id = conceptos.cuenta_sap_id', 'left')
            ->orderBy('conceptos.id_concepto', 'DESC');

        if (! empty($busqueda)) {
            $this->like('conceptos.nombre_concepto', $busqueda);
        }

        return $this;
    }
}
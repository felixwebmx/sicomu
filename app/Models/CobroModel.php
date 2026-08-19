<?php

namespace App\Models;

use CodeIgniter\Model;

class CobroModel extends Model
{
    protected $table            = 'cobros';
    protected $primaryKey       = 'cobro_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'folio_id', 'numero_folio', 'caja_apertura_id', 'usuario_id', 'fecha_cobro',
        'estatus_cobro', 'metodo_pago', 'nombre_contribuyente', 'rfc_contribuyente',
        'domicilio_contribuyente', 'ext_contribuyente', 'bis_contribuyente', 'int_contribuyente',
        'colonia_contribuyente', 'total_cobro', 'monto_recibido', 'cambio', 'observaciones_cobro',
        'usuario_cancela_id', 'fecha_cancelacion', 'motivo_cancelacion',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Cobro con su detalle y datos de folio, listo para el recibo/impresión.
     */
    public function conDetalle(int $cobroId): ?array
    {
        $cobro = $this->find($cobroId);

        if (! $cobro) {
            return null;
        }

        $cobro['detalles'] = $this->db->table('cobros_detalles')
            ->select('cobros_detalles.*, conceptos.nombre_concepto, cuentas.nombre_cuenta, partidas.nombre_partida')
            ->join('conceptos', 'conceptos.id_concepto = cobros_detalles.concepto_id')
            ->join('cuentas', 'cuentas.id_cuenta = cobros_detalles.id_cuenta')
            ->join('partidas', 'partidas.id_partida = cobros_detalles.id_partida')
            ->where('cobro_id', $cobroId)
            ->get()
            ->getResultArray();

        return $cobro;
    }

    /**
     * Suma de cobros activos dentro de una apertura de caja (para el arqueo).
     */
    public function totalPorApertura(int $cajaAperturaId): float
    {
        $fila = $this->selectSum('total_cobro')
            ->where('caja_apertura_id', $cajaAperturaId)
            ->where('estatus_cobro', 'activo')
            ->first();

        return (float) ($fila['total_cobro'] ?? 0);
    }
}
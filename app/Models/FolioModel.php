<?php

namespace App\Models;

use CodeIgniter\Model;

class FolioModel extends Model
{
    protected $table            = 'folios';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'numero_folio', 'modulo_origen', 'caja_apertura_id', 'usuario_id',
        'fecha_hora', 'estatus', 'motivo_cancelacion', 'usuario_cancela_id',
        'fecha_cancelacion',
    ];
    protected $useTimestamps = false; // created_at se fija manualmente en FolioService
    protected $createdField  = 'created_at';

    /**
     * Folios (de ambos módulos) emitidos dentro de una apertura de caja.
     * Útil para el arqueo/corte de caja.
     */
    public function porApertura(int $cajaAperturaId): array
    {
        return $this->where('caja_apertura_id', $cajaAperturaId)
            ->orderBy('numero_folio', 'asc')
            ->findAll();
    }
}
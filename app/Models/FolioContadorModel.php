<?php

namespace App\Models;

use CodeIgniter\Model;

class FolioContadorModel extends Model
{
    protected $table         = 'folio_contador';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['ultimo_folio', 'updated_at'];
    protected $useTimestamps = false;

    /**
     * Lee el último folio SIN bloquear (solo informativo, ej. para mostrar
     * "Folio Inicial" en la pantalla de apertura de caja).
     */
    public function ultimoFolioInformativo(): int
    {
        $fila = $this->find(1);
        return (int) ($fila['ultimo_folio'] ?? 0);
    }
}
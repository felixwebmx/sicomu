<?php

namespace App\Models;

use CodeIgniter\Model;

class CajaAperturaModel extends Model
{
    protected $table            = 'caja_aperturas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'caja_id', 'usuario_id', 'fecha_apertura', 'hora_apertura',
        'folio_inicial', 'monto_inicial', 'estatus',
        'hora_cierre', 'usuario_cierre_id',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Apertura activa del usuario logueado (la caja donde está "parado" cobrando).
     * Un cajero solo debería tener una apertura abierta a la vez.
     */
    public function aperturaDeUsuario(int $usuarioId): ?array
    {
        return $this->where('usuario_id', $usuarioId)
            ->where('estatus', 'abierta')
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * Todas las aperturas abiertas hoy (para pantalla de supervisión / selector de cierre).
     */
    public function abiertasHoy(): array
    {
        return $this->select('caja_aperturas.*, cajas.nombre as caja_nombre, usuarios.nombre_completo as cajero')
            ->join('cajas', 'cajas.id = caja_aperturas.caja_id')
            ->join('usuarios', 'usuarios.id = caja_aperturas.usuario_id')
            ->where('caja_aperturas.estatus', 'abierta')
            ->where('caja_aperturas.fecha_apertura', date('Y-m-d'))
            ->findAll();
    }
}
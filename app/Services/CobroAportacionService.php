<?php

namespace App\Services;

use App\Models\CobroAportacionModel;
use App\Models\VecinoModel;
use RuntimeException;

class CobroAportacionService
{
    protected CobroAportacionModel $cobroModel;
    protected VecinoModel $vecinoModel;
    protected FolioService $folioService;

    public function __construct()
    {
        $this->cobroModel    = new CobroAportacionModel();
        $this->vecinoModel   = new VecinoModel();
        $this->folioService  = new FolioService();
    }

    /**
     * Crea un cobro de aportación: genera folio, guarda cobro, actualiza vecino.
     */
    public function crear(array $datos, int $cajaAperturaId, int $usuarioId): array
    {
        $vecinoId = (int) ($datos['vecino_id'] ?? 0);
        $monto    = (float) ($datos['monto_pagado'] ?? 0);

        $vecino = $this->vecinoModel->find($vecinoId);

        if (! $vecino) {
            throw new RuntimeException('El vecino seleccionado no existe.');
        }

        if ($monto <= 0) {
            throw new RuntimeException('El monto a pagar debe ser mayor a 0.');
        }

        if ($monto > (float) $vecino['resto']) {
            throw new RuntimeException('El monto a pagar no puede ser mayor al resto pendiente ($' . number_format((float)$vecino['resto'], 2) . ').');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $folio = $this->folioService->generarFolio('aportacion', $cajaAperturaId, $usuarioId);

        $cobroId = $this->cobroModel->insert([
            'folio_id'         => $folio['id'],
            'numero_folio'     => $folio['numero_folio'],
            'caja_apertura_id' => $cajaAperturaId,
            'usuario_id'       => $usuarioId,
            'vecino_id'        => $vecinoId,
            'fecha_cobro'      => date('Y-m-d H:i:s'),
            'monto_pagado'     => $monto,
            'metodo_pago'      => $datos['metodo_pago'] ?? 'efectivo',
            'observaciones'    => $datos['observaciones'] ?? null,
            'estatus'          => 'activo',
        ], true);

        // Actualizar saldo del vecino
        $this->vecinoModel->registrarPago($vecinoId, $monto);

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new RuntimeException('No fue posible registrar el cobro de aportación. Intente nuevamente.');
        }

        return $this->cobroModel->conDetalle($cobroId);
    }

    /**
     * Cancela un cobro de aportación y revierte el pago del vecino.
     */
    public function cancelar(int $cobroId, int $usuarioId, string $motivo): bool
    {
        $cobro = $this->cobroModel->find($cobroId);

        if (! $cobro || $cobro['estatus'] === 'cancelado') {
            return false;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Revertir pago del vecino
        $vecino = $this->vecinoModel->find($cobro['vecino_id']);
        if ($vecino) {
            $nuevoPagado = max(0, (float) $vecino['pagado'] - (float) $cobro['monto_pagado']);
            $nuevoResto  = (float) $vecino['total_aportacion'] - $nuevoPagado;

            $this->vecinoModel->update($cobro['vecino_id'], [
                'pagado' => $nuevoPagado,
                'resto'  => max(0, $nuevoResto),
            ]);
        }

        // Cancelar folio
        $this->folioService->cancelarFolio($cobro['numero_folio'], $usuarioId, $motivo);

        // Cancelar cobro
        $this->cobroModel->update($cobroId, [
            'estatus'            => 'cancelado',
            'usuario_cancela_id' => $usuarioId,
            'fecha_cancelacion'  => date('Y-m-d H:i:s'),
            'motivo_cancelacion' => $motivo,
        ]);

        $db->transComplete();

        return $db->transStatus() !== false;
    }
}
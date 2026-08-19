<?php

namespace App\Services;

use App\Models\CobroDetalleModel;
use App\Models\CobroModel;
use App\Models\ConceptoModel;
use RuntimeException;

class CobroServicioService
{
    protected CobroModel $cobroModel;
    protected CobroDetalleModel $detalleModel;
    protected ConceptoModel $conceptoModel;
    protected FolioService $folioService;

    public function __construct()
    {
        $this->cobroModel    = new CobroModel();
        $this->detalleModel  = new CobroDetalleModel();
        $this->conceptoModel = new ConceptoModel();
        $this->folioService  = new FolioService();
    }

    /**
     * Crea un cobro de servicio completo: genera folio, guarda encabezado
     * y detalles, todo en una sola transacción.
     *
     * @param array $datosContribuyente ['nombre_contribuyente','rfc_contribuyente','domicilio_contribuyente',
     *                                   'ext_contribuyente','bis_contribuyente','int_contribuyente','id_colonia',
     *                                   'metodo_pago','monto_recibido','observaciones_cobro']
     * @param array $renglones Lista de ['concepto_id' => int, 'cantidad' => float, 'caracteristicas' => ?string]
     * @param int   $cajaAperturaId Apertura de caja vigente del cajero
     * @param int   $usuarioId      Cajero que realiza el cobro
     *
     * @return array Cobro recién creado (con folio y total)
     * @throws RuntimeException si no hay renglones, algún concepto no existe, o falla el guardado
     */
    public function crear(array $datosContribuyente, array $renglones, int $cajaAperturaId, int $usuarioId): array
    {
        if (empty($renglones)) {
            throw new RuntimeException('Debe agregar al menos un concepto para cobrar.');
        }

        // Arma los renglones con precio/cuenta/partida SNAPSHOT desde el catálogo vigente
        $detallesPreparados = [];
        $total = 0.0;

        foreach ($renglones as $renglon) {
            $concepto = $this->conceptoModel->find((int) $renglon['concepto_id']);

            if (! $concepto) {
                throw new RuntimeException("El concepto seleccionado (ID {$renglon['concepto_id']}) ya no existe.");
            }

            $cantidad       = (float) ($renglon['cantidad'] ?? 1);
            $montoCatalogo  = (float) $concepto['monto_concepto'];

            // El cajero puede ajustar el monto unitario; si no manda nada,
            // se usa el precio de catálogo por default.
            $montoUnitario = isset($renglon['monto_unitario']) && $renglon['monto_unitario'] !== ''
                ? (float) $renglon['monto_unitario']
                : $montoCatalogo;

            if ($montoUnitario < 0) {
                throw new RuntimeException("El monto para '{$concepto['nombre_concepto']}' no puede ser negativo.");
            }

            $totalRenglon = round($cantidad * $montoUnitario, 2);
            $total       += $totalRenglon;

            $detallesPreparados[] = [
                'concepto_id'       => $concepto['id_concepto'],
                'id_cuenta'         => $concepto['id_cuenta'],
                'id_partida'        => $concepto['id_partida'],
                'concepto_cantidad' => $cantidad,
                'concepto_monto'    => $montoUnitario,
                'monto_catalogo'    => $montoCatalogo,
                'total'             => $totalRenglon,
                'caracteristicas'   => $renglon['caracteristicas'] ?? null,
            ];
        }

        $montoRecibido = (float) ($datosContribuyente['monto_recibido'] ?? $total);

        if ($montoRecibido < $total) {
            throw new RuntimeException('El monto recibido no puede ser menor al total a pagar.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $folio = $this->folioService->generarFolio('servicio', $cajaAperturaId, $usuarioId);

        $cobroId = $this->cobroModel->insert([
            'folio_id'                => $folio['id'],
            'numero_folio'            => $folio['numero_folio'],
            'caja_apertura_id'        => $cajaAperturaId,
            'usuario_id'              => $usuarioId,
            'fecha_cobro'             => date('Y-m-d H:i:s'),
            'estatus_cobro'           => 'activo',
            'metodo_pago'             => $datosContribuyente['metodo_pago'] ?? 'efectivo',
            'nombre_contribuyente'    => $datosContribuyente['nombre_contribuyente'],
            'rfc_contribuyente'       => $datosContribuyente['rfc_contribuyente'] ?? null,
            'domicilio_contribuyente' => $datosContribuyente['domicilio_contribuyente'] ?? null,
            'ext_contribuyente'       => $datosContribuyente['ext_contribuyente'] ?? null,
            'bis_contribuyente'       => $datosContribuyente['bis_contribuyente'] ?? null,
            'int_contribuyente'       => $datosContribuyente['int_contribuyente'] ?? null,
            'id_colonia'              => $datosContribuyente['id_colonia'] ?? null,
            'total_cobro'             => $total,
            'monto_recibido'          => $montoRecibido,
            'cambio'                  => round($montoRecibido - $total, 2),
            'observaciones_cobro'     => $datosContribuyente['observaciones_cobro'] ?? null,
        ], true);

        foreach ($detallesPreparados as $detalle) {
            $detalle['cobro_id'] = $cobroId;
            $this->detalleModel->insert($detalle);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new RuntimeException('No fue posible registrar el cobro. Intente nuevamente.');
        }

        return $this->cobroModel->conDetalle($cobroId);
    }

    /**
     * Cancela un cobro y su folio asociado (no elimina, por auditoría).
     */
    public function cancelar(int $cobroId, int $usuarioId, string $motivo): bool
    {
        $cobro = $this->cobroModel->find($cobroId);

        if (! $cobro || $cobro['estatus_cobro'] === 'cancelado') {
            return false;
        }

        $this->folioService->cancelarFolio($cobro['numero_folio'], $usuarioId, $motivo);

        return (bool) $this->cobroModel->update($cobroId, [
            'estatus_cobro'       => 'cancelado',
            'usuario_cancela_id'  => $usuarioId,
            'fecha_cancelacion'   => date('Y-m-d H:i:s'),
            'motivo_cancelacion'  => $motivo,
        ]);
    }
}
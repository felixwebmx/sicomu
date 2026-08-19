<?php

namespace App\Services;

use App\Models\FolioContadorModel;
use App\Models\FolioModel;
use RuntimeException;

/**
 * Servicio responsable de emitir folios consecutivos GLOBALES,
 * compartidos entre el módulo de Servicios y el de Aportaciones.
 *
 * El folio nunca se reinicia. La numeración se protege con
 * SELECT ... FOR UPDATE dentro de una transacción para evitar
 * duplicados cuando dos cajas cobran al mismo tiempo.
 */
class FolioService
{
    protected FolioContadorModel $contadorModel;
    protected FolioModel $folioModel;

    public function __construct()
    {
        $this->contadorModel = new FolioContadorModel();
        $this->folioModel    = new FolioModel();
    }

    /**
     * Genera y registra un nuevo folio. Debe llamarse SIEMPRE dentro del
     * flujo de guardado de un cobro (servicio o aportación), nunca de forma
     * anticipada/especulativa, para no dejar huecos innecesarios.
     *
     * @param 'servicio'|'aportacion' $moduloOrigen
     * @param int $cajaAperturaId Apertura de caja vigente del cajero
     * @param int $usuarioId      Cajero que realiza el cobro
     *
     * @return array{id: int, numero_folio: int} PK y número de folio recién asignado
     */
    public function generarFolio(string $moduloOrigen, int $cajaAperturaId, int $usuarioId): array
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Bloquea la única fila del contador hasta que termine la transacción
        $contador = $db->query(
            'SELECT ultimo_folio FROM folio_contador WHERE id = 1 FOR UPDATE'
        )->getRowArray();

        if ($contador === null) {
            $db->transRollback();
            throw new RuntimeException('No existe el registro de folio_contador (id=1). Verifique la migración/seed.');
        }

        $siguienteFolio = (int) $contador['ultimo_folio'] + 1;

        $db->table('folio_contador')
            ->where('id', 1)
            ->update(['ultimo_folio' => $siguienteFolio, 'updated_at' => date('Y-m-d H:i:s')]);

        $db->table('folios')->insert([
            'numero_folio'     => $siguienteFolio,
            'modulo_origen'    => $moduloOrigen,
            'caja_apertura_id' => $cajaAperturaId,
            'usuario_id'       => $usuarioId,
            'fecha_hora'       => date('Y-m-d H:i:s'),
            'estatus'          => 'activo',
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        $folioId = (int) $db->insertID();

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new RuntimeException('No fue posible generar el folio. Intente nuevamente.');
        }

        return ['id' => $folioId, 'numero_folio' => $siguienteFolio];
    }

    /**
     * Valor informativo (NO reservado) del siguiente folio a emitir.
     * Se usa únicamente para mostrarlo en pantalla de apertura de caja.
     */
    public function siguienteFolioInformativo(): int
    {
        return $this->contadorModel->ultimoFolioInformativo() + 1;
    }

    /**
     * Cancela un folio ya emitido (no lo elimina, por auditoría).
     * El consecutivo NUNCA retrocede ni se reutiliza.
     */
    public function cancelarFolio(int $numeroFolio, int $usuarioId, string $motivo): bool
    {
        $folio = $this->folioModel->where('numero_folio', $numeroFolio)->first();

        if (! $folio || $folio['estatus'] === 'cancelado') {
            return false;
        }

        return (bool) $this->folioModel->update($folio['id'], [
            'estatus'            => 'cancelado',
            'motivo_cancelacion' => $motivo,
            'usuario_cancela_id' => $usuarioId,
            'fecha_cancelacion'  => date('Y-m-d H:i:s'),
        ]);
    }
}
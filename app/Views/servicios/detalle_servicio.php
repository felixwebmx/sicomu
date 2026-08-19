<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<style>
    .invoice {
        background: #fff;
        border: 1px solid var(--bs-border-color);
        border-radius: 0.375rem;
        position: relative;
    }
    .invoice-title {
        margin-top: 0;
        font-weight: 700;
    }
    .invoice-col {
        line-height: 1.6;
    }
    .invoice-col address {
        margin-bottom: 0;
    }
    .table-invoice thead {
        background-color: var(--bs-primary);
        color: #fff;
    }
    .table-invoice tbody tr:nth-child(odd) {
        background-color: rgba(0,0,0,0.02);
    }
    .table-totals th {
        width: 60%;
        text-align: right;
        padding-right: 1rem;
    }
    .table-totals td {
        text-align: right;
        font-weight: 600;
    }
    .table-totals tr.total-final td,
    .table-totals tr.total-final th {
        font-size: 1.15rem;
        font-weight: 700;
        border-top: 2px solid var(--bs-primary);
    }
    .sello-cancelado {
        position: absolute;
        top: 40%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-25deg);
        border: 4px solid #dc3545;
        color: #dc3545;
        font-size: 2.5rem;
        font-weight: 900;
        padding: 0.5rem 1.5rem;
        text-transform: uppercase;
        opacity: 0.25;
        pointer-events: none;
        z-index: 10;
    }

    /* ═══════════════════════════════════════════════ */
    /* PRINT STYLES                                    */
    /* ═══════════════════════════════════════════════ */
    @media print {
        .no-print,
        .app-header,
        .app-sidebar,
        .app-footer,
        .app-content-header,
        .btn-close {
            display: none !important;
        }
        .app-wrapper {
            margin-left: 0 !important;
        }
        .app-main {
            padding: 0 !important;
            margin: 0 !important;
        }
        .invoice {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        body {
            background: white !important;
        }
        .sello-cancelado {
            opacity: 0.15;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<div class="row">
    <div class="col-12">

        <div class="invoice p-3 mb-3">

            <?php if ($cobro['estatus_cobro'] === 'cancelado'): ?>
                <div class="sello-cancelado">C A N C E L A D O</div>
            <?php endif; ?>

            <!-- ═══ TITLE ROW ═══ -->
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="invoice-title d-flex justify-content-between align-items-center">
                        <span>
                            <i class="bi bi-receipt me-2 text-primary"></i>
                            H. Ayuntamiento de Uriangato
                        </span>
                        <small class="text-muted fs-5">Folio #<?= esc($cobro['numero_folio']) ?></small>
                    </h4>
                </div>
            </div>

            <!-- ═══ INFO ROW ═══ -->
            <div class="row invoice-info mb-4">
                <div class="col-sm-4 invoice-col">
                    <strong>De</strong>
                    <address>
                        <strong>SICOMU</strong><br>
                        Presidencia Municipal de Uriangato<br>
                        Uriangato, Gto.<br>
                        Tel: (445) XXX-XXXX
                    </address>
                </div>

                <div class="col-sm-4 invoice-col">
                    <strong>Para</strong>
                    <address>
                        <strong><?= esc($cobro['nombre_contribuyente']) ?></strong><br>
                        <?php if ($cobro['rfc_contribuyente']): ?>
                            RFC: <?= esc($cobro['rfc_contribuyente']) ?><br>
                        <?php endif; ?>
                        <?php if ($cobro['domicilio_contribuyente']): ?>
                            <?= esc($cobro['domicilio_contribuyente']) ?>
                            <?= $cobro['ext_contribuyente'] ? ' Ext. ' . esc($cobro['ext_contribuyente']) : '' ?>
                            <?= $cobro['bis_contribuyente'] ? ' Bis ' . esc($cobro['bis_contribuyente']) : '' ?>
                            <?= $cobro['int_contribuyente'] ? ' Int. ' . esc($cobro['int_contribuyente']) : '' ?>
							<?php if (!empty($cobro['colonia_contribuyente'])): ?>
                                <br>Col. <?= esc($cobro['colonia_contribuyente']) ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">Sin domicilio registrado</span>
                        <?php endif; ?>
                    </address>
                </div>

                <div class="col-sm-4 invoice-col">
                    <b>Folio:</b> #<?= esc($cobro['numero_folio']) ?><br>
                    <b>Fecha de cobro:</b> <?= date('d/m/Y H:i', strtotime($cobro['fecha_cobro'])) ?><br>
                    <b>Método de pago:</b> <?= esc(ucfirst($cobro['metodo_pago'])) ?><br>
                    <b>Estatus:</b>
                    <?php if ($cobro['estatus_cobro'] === 'activo'): ?>
                        <span class="badge text-bg-success">Activo</span>
                    <?php else: ?>
                        <span class="badge text-bg-danger">Cancelado</span>
                    <?php endif; ?>
                    <?php if ($cobro['estatus_cobro'] === 'cancelado' && $cobro['motivo_cancelacion']): ?>
                        <br><small class="text-danger"><b>Motivo:</b> <?= esc($cobro['motivo_cancelacion']) ?></small>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ═══ TABLE ROW ═══ -->
            <div class="row">
                <div class="col-12 table-responsive">
                    <table class="table table-invoice table-striped table-bordered mb-3">
                        <thead>
                            <tr>
                                <th style="width: 50%;">Concepto</th>
                                <th class="text-center" style="width: 10%;">Cant.</th>
                                <th class="text-end" style="width: 20%;">Monto Unit.</th>
                                <th class="text-end" style="width: 20%;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cobro['detalles'] as $d): ?>
                                <tr>
                                    <td>
                                        <?= esc($d['nombre_concepto']) ?>
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-folder me-1"></i><?= esc($d['nombre_cuenta']) ?>
                                            <i class="bi bi-bookmark ms-2 me-1"></i><?= esc($d['nombre_partida']) ?>
                                        </small>
                                    </td>
                                    <td class="text-center"><?= $d['concepto_cantidad'] ?></td>
                                    <td class="text-end">$<?= number_format((float)$d['concepto_monto'], 2) ?></td>
                                    <td class="text-end">$<?= number_format((float)$d['total'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ═══ TOTALS ROW ═══ -->
            <div class="row">
                <div class="col-6">
                    <?php if ($cobro['observaciones_cobro']): ?>
                        <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                            <strong>Observaciones:</strong><br>
                            <?= nl2br(esc($cobro['observaciones_cobro'])) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="col-6">
                    <div class="table-responsive">
                        <table class="table table-totals">
                            <tr>
                                <th>Subtotal:</th>
                                <td>$<?= number_format((float)$cobro['total_cobro'], 2) ?></td>
                            </tr>
                            <tr>
                                <th>Monto Recibido:</th>
                                <td>$<?= number_format((float)$cobro['monto_recibido'], 2) ?></td>
                            </tr>
                            <tr class="total-final text-success">
                                <th>Cambio:</th>
                                <td>$<?= number_format((float)$cobro['cambio'], 2) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ═══ FOOTER / ACTIONS ═══ -->
            <div class="row mt-4 pt-3 border-top no-print">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <a href="<?= site_url('servicios/cobro/historial') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Regresar al Historial
                    </a>
                    <div>
                        <?php if ($cobro['estatus_cobro'] === 'activo'): ?>
                            <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#modalCancelar<?= $cobro['cobro_id'] ?>">
                                <i class="bi bi-x-circle me-1"></i> Cancelar Cobro
                            </button>
                        <?php endif; ?>
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="bi bi-printer me-1"></i> Imprimir Recibo
                        </button>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<?php if ($cobro['estatus_cobro'] === 'activo'): ?>
<!-- Modal Cancelar (inline en el recibo para comodidad) -->
<div class="modal fade no-print" id="modalCancelar<?= $cobro['cobro_id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <?= form_open('servicios/cobro/cancelar/' . $cobro['cobro_id']) ?>
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Cancelar Cobro #<?= esc($cobro['numero_folio']) ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de cancelar este cobro por <strong>$<?= number_format((float)$cobro['total_cobro'], 2) ?></strong>?</p>
                <div class="mb-3">
                    <label class="form-label fw-bold">Motivo de cancelación <span class="text-danger">*</span></label>
                    <textarea name="motivo" class="form-control" rows="2" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-danger">Confirmar Cancelación</button>
            </div>
        </div>
        <?= form_close() ?>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
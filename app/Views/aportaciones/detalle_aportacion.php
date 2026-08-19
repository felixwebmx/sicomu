<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<style>
    .recibo-aportacion {
        background: #fff;
        border: 1px solid var(--bs-border-color);
        border-radius: 0.375rem;
        padding: 2rem;
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
    @media print {
        .no-print, .app-header, .app-sidebar, .app-footer, .app-content-header { display: none !important; }
        .app-wrapper { margin-left: 0 !important; }
        .recibo-aportacion { border: none !important; padding: 0 !important; }
        body { background: white !important; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="recibo-aportacion shadow position-relative">

            <?php if ($cobro['estatus'] === 'cancelado'): ?>
                <div class="sello-cancelado">C A N C E L A D O</div>
            <?php endif; ?>

            <div class="text-center mb-4">
                <h4 class="mb-1">H. Ayuntamiento de Uriangato</h4>
                <p class="text-muted mb-0">Recibo de Aportación Vecinal</p>
            </div>

            <div class="row mb-3">
                <div class="col-6">
                    <small class="text-muted d-block">Folio</small>
                    <strong class="fs-5"><?= esc($cobro['numero_folio']) ?></strong>
                </div>
                <div class="col-6 text-end">
                    <small class="text-muted d-block">Fecha</small>
                    <strong><?= date('d/m/Y H:i', strtotime($cobro['fecha_cobro'])) ?></strong>
                </div>
            </div>

            <hr>

            <div class="mb-3">
                <small class="text-muted d-block">Vecino</small>
                <strong class="fs-5"><?= esc($cobro['vecino']['nombre_vecino'] ?? '—') ?></strong>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <small class="text-muted d-block">Obra</small>
                    <strong><?= esc($cobro['vecino']['nombre_obra'] ?? '—') ?></strong>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-muted d-block">Ubicación</small>
                    <span><?= esc($cobro['vecino']['nombre_colonia'] ?? '') ?> <?= esc($cobro['vecino']['nombre_vialidad'] ?? '') ?></span>
                </div>
            </div>

            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr><th>Concepto</th><th class="text-end">Monto</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Aportación Vecinal</td>
                        <td class="text-end fw-bold">$<?= number_format((float)$cobro['monto_pagado'], 2) ?></td>
                    </tr>
                </tbody>
                <tfoot class="table-group-divider">
                    <tr class="fs-5 fw-bold">
                        <td class="text-end">Total Pagado:</td>
                        <td class="text-end text-success">$<?= number_format((float)$cobro['monto_pagado'], 2) ?></td>
                    </tr>
                </tfoot>
            </table>

            <?php if ($cobro['observaciones']): ?>
                <div class="alert alert-light border">
                    <small class="text-muted">Observaciones:</small><br>
                    <?= nl2br(esc($cobro['observaciones'])) ?>
                </div>
            <?php endif; ?>

            <div class="text-center mt-4 no-print">
                <button onclick="window.print()" class="btn btn-primary btn-lg">
                    <i class="bi bi-printer me-1"></i> Imprimir
                </button>
                <a href="<?= site_url('aportaciones/cobro/historial') ?>" class="btn btn-secondary btn-lg ms-2">
                    <i class="bi bi-arrow-left me-1"></i> Regresar
                </a>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
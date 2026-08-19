<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<style>
    .arqueo-box {
        background: #fff;
        border: 1px solid var(--bs-border-color);
        border-radius: 0.375rem;
        padding: 2rem;
    }
    .sello-cerrado {
        position: absolute;
        top: 35%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-20deg);
        border: 5px solid #198754;
        color: #198754;
        font-size: 3rem;
        font-weight: 900;
        padding: 0.75rem 2rem;
        text-transform: uppercase;
        opacity: 0.2;
        pointer-events: none;
        z-index: 10;
    }
    @media print {
        .no-print, .app-header, .app-sidebar, .app-footer, .app-content-header { display: none !important; }
        .app-wrapper { margin-left: 0 !important; }
        .arqueo-box { border: none !important; box-shadow: none !important; padding: 0 !important; }
        body { background: white !important; }
        .sello-cerrado { opacity: 0.12; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        <div class="arqueo-box shadow position-relative">

            <div class="sello-cerrado">A R Q U E O</div>

            <div class="text-center mb-4">
                <h4 class="mb-1">H. Ayuntamiento de Uriangato</h4>
                <p class="text-muted mb-0">Arqueo de Caja / Corte de Turno</p>
            </div>

            <div class="row mb-4">
                <div class="col-6">
                    <small class="text-muted d-block">Caja</small>
                    <strong><?= esc($arqueo['caja_nombre'] ?? '—') ?></strong>
                </div>
                <div class="col-6 text-end">
                    <small class="text-muted d-block">Fecha de Cierre</small>
                    <strong><?= date('d/m/Y H:i', strtotime($arqueo['fecha_arqueo'])) ?></strong>
                </div>
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col-sm-6">
                    <small class="text-muted d-block">Cajero (Apertura)</small>
                    <strong><?= esc($arqueo['cajero_apertura'] ?? '—') ?></strong>
                </div>
                <div class="col-sm-6 text-sm-end">
                    <small class="text-muted d-block">Cajero (Cierre)</small>
                    <strong><?= esc($arqueo['cajero_cierre'] ?? '—') ?></strong>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-6">
                    <small class="text-muted d-block">Folio Inicial</small>
                    <strong><?= esc($arqueo['folio_inicial'] ?? '—') ?></strong>
                </div>
                <div class="col-6 text-end">
                    <small class="text-muted d-block">Folio Final</small>
                    <strong><?= esc($arqueo['folio_final'] ?? '—') ?></strong>
                </div>
            </div>

            <table class="table table-bordered table-sm">
                <thead class="table-dark">
                    <tr><th>Concepto</th><th class="text-end">Monto</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Fondo de Caja Inicial</td>
                        <td class="text-end">$<?= number_format((float) ($arqueo['monto_inicial'] ?? 0), 2) ?></td>
                    </tr>
                    <tr>
                        <td>Total Servicios Cobrados</td>
                        <td class="text-end text-success">$<?= number_format((float) $arqueo['total_servicios'], 2) ?></td>
                    </tr>
                    <tr>
                        <td>Total Aportaciones Cobradas</td>
                        <td class="text-end text-primary">$<?= number_format((float) $arqueo['total_aportaciones'], 2) ?></td>
                    </tr>
                </tbody>
                <tfoot class="table-group-divider">
                    <tr class="fw-bold">
                        <td>Total Sistema (Esperado)</td>
                        <td class="text-end fs-5">$<?= number_format((float) $arqueo['total_sistema'], 2) ?></td>
                    </tr>
                    <tr>
                        <td>Efectivo Contado (Real)</td>
                        <td class="text-end fw-bold">$<?= number_format((float) $arqueo['efectivo_contado'], 2) ?></td>
                    </tr>
                    <tr class="<?= $arqueo['diferencia'] == 0 ? 'text-success' : ($arqueo['diferencia'] > 0 ? 'text-warning' : 'text-danger') ?> fw-bold">
                        <td>Diferencia</td>
                        <td class="text-end fs-5">
                            <?= $arqueo['diferencia'] >= 0 ? '+' : '' ?>$<?= number_format((float) $arqueo['diferencia'], 2) ?>
                        </td>
                    </tr>
                </tfoot>
            </table>

            <?php if ($arqueo['observaciones']): ?>
                <div class="alert alert-light border mt-3">
                    <small class="text-muted">Observaciones:</small><br>
                    <?= nl2br(esc($arqueo['observaciones'])) ?>
                </div>
            <?php endif; ?>
			
			<div class="text-center mt-4 no-print">
                <button onclick="window.print()" class="btn btn-primary btn-lg">
                    <i class="bi bi-printer me-1"></i> Imprimir Arqueo
                </button>
                
                <a href="<?= site_url('caja/reporte-diario/' . $arqueo['caja_apertura_id']) ?>" 
                   class="btn btn-outline-dark btn-lg ms-2" target="_blank">
                    <i class="bi bi-file-earmark-text me-1"></i> Reporte Diario
                </a>
                
                <a href="<?= site_url('caja') ?>" class="btn btn-success btn-lg ms-2">
                    <i class="bi bi-check-circle me-1"></i> Finalizar
                </a>
                <?php if (puede('cobros.crear')): ?>
                    <a href="<?= site_url('caja/arqueos') ?>" class="btn btn-outline-secondary btn-lg ms-2">
                        <i class="bi bi-list-ul me-1"></i> Ver Todos
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
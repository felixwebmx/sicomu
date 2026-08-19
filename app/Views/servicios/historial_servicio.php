<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
            <i class="bi bi-receipt me-2"></i>Historial de Cobros
        </h3>
        <div>
            <?php if ($apertura): ?>
                <span class="badge text-bg-success me-2">
                    Caja abierta: #<?= esc($apertura['id']) ?>
                </span>
            <?php endif; ?>
            <a href="<?= site_url('servicios/cobro') ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Nuevo Cobro
            </a>
        </div>
    </div>

    <div class="card-body">
        <?php if (empty($cobros)): ?>
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle me-2"></i>No hay cobros registrados en esta apertura.
            </div>
        <?php else: ?>
            <table id="tablaCobros" class="table table-striped table-hover w-100">
                <thead class="table-dark">
                    <tr>
                        <th>Folio</th>
                        <th>Contribuyente</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Método</th>
                        <th class="text-center">Estado</th>
                        <th>Fecha</th>
                        <th class="text-end no-export">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cobros as $c): ?>
                        <tr class="<?= $c['estatus_cobro'] === 'cancelado' ? 'table-danger' : '' ?>">
                            <td><strong><?= esc($c['numero_folio']) ?></strong></td>
                            <td>
                                <?= esc($c['nombre_contribuyente']) ?>
                                <?php if ($c['rfc_contribuyente']): ?>
                                    <br><small class="text-muted"><?= esc($c['rfc_contribuyente']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-bold">
                                $<?= number_format((float)$c['total_cobro'], 2) ?>
                            </td>
                            <td class="text-center">
                                <span class="badge text-bg-secondary">
                                    <?= esc(ucfirst($c['metodo_pago'])) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if ($c['estatus_cobro'] === 'activo'): ?>
                                    <span class="badge text-bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge text-bg-danger">Cancelado</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($c['fecha_cobro'])) ?></td>
                            <td class="text-end no-export">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= site_url('servicios/cobro/detalle/' . $c['cobro_id']) ?>"
                                       class="btn btn-outline-primary" title="Ver / Imprimir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if ($c['estatus_cobro'] === 'activo'): ?>
                                        <button type="button" class="btn btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalCancelar<?= $c['cobro_id'] ?>"
                                                title="Cancelar">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>

                        <?php if ($c['estatus_cobro'] === 'activo'): ?>
                        <!-- Modal Cancelar -->
                        <div class="modal fade" id="modalCancelar<?= $c['cobro_id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <?= form_open('servicios/cobro/cancelar/' . $c['cobro_id']) ?>
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Cancelar Cobro #<?= esc($c['numero_folio']) ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>¿Está seguro de cancelar este cobro por <strong>$<?= number_format((float)$c['total_cobro'], 2) ?></strong>?</p>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Motivo de cancelación</label>
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

                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initDataTable('#tablaCobros', {
        columnDefs: [
            { orderable: false, targets: 6 },
            { searchable: false, targets: 6 }
        ],
        order: [[0, 'desc']]
    });
});
</script>
<?= $this->endSection() ?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0"><i class="bi bi-people me-2"></i>Historial de Aportaciones</h3>
        <a href="<?= site_url('aportaciones/cobro') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Nueva Aportación
        </a>
    </div>
    <div class="card-body">
        <?php if (empty($cobros)): ?>
            <div class="alert alert-info mb-0">No hay cobros de aportaciones en esta apertura.</div>
        <?php else: ?>
            <table id="tablaAportaciones" class="table table-striped table-hover w-100">
                <thead class="table-dark">
                    <tr>
                        <th>Folio</th>
                        <th>Vecino</th>
                        <th>Obra</th>
                        <th class="text-end">Monto</th>
                        <th class="text-center">Método</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end no-export">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cobros as $c): ?>
                        <tr class="<?= $c['estatus'] === 'cancelado' ? 'table-danger' : '' ?>">
                            <td><strong><?= esc($c['numero_folio']) ?></strong></td>
                            <td><?= esc($c['nombre_vecino']) ?></td>
                            <td><?= esc($c['nombre_obra']) ?></td>
                            <td class="text-end fw-bold">$<?= number_format((float)$c['monto_pagado'], 2) ?></td>
                            <td class="text-center"><?= esc(ucfirst($c['metodo_pago'])) ?></td>
                            <td class="text-center">
                                <?php if ($c['estatus'] === 'activo'): ?>
                                    <span class="badge text-bg-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge text-bg-danger">Cancelado</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end no-export">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= site_url('aportaciones/cobro/detalle/' . $c['id']) ?>" class="btn btn-outline-primary" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if ($c['estatus'] === 'activo'): ?>
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalCancelar<?= $c['id'] ?>">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php if ($c['estatus'] === 'activo'): ?>
                        <div class="modal fade" id="modalCancelar<?= $c['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <?= form_open('aportaciones/cobro/cancelar/' . $c['id']) ?>
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Cancelar Folio #<?= esc($c['numero_folio']) ?></h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>¿Cancelar cobro de <strong><?= esc($c['nombre_vecino']) ?></strong> por <strong>$<?= number_format((float)$c['monto_pagado'], 2) ?></strong>?</p>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Motivo <span class="text-danger">*</span></label>
                                            <textarea name="motivo" class="form-control" rows="2" required></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-danger">Confirmar</button>
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
    initDataTable('#tablaAportaciones', {
        columnDefs: [
            { orderable: false, targets: 6 },
            { searchable: false, targets: 6 }
        ],
        order: [[0, 'desc']]
    });
});
</script>
<?= $this->endSection() ?>
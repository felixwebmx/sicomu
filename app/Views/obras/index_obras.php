<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
            <i class="bi bi-building me-2"></i>Gestión de Obras
        </h3>
        <?php if (puede('obras.administrar')): ?>
            <a href="<?= site_url('obras/nuevo') ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Nueva Obra
            </a>
        <?php endif; ?>
    </div>

    <div class="card-body">
        <table id="tablaObras" class="table table-striped table-hover w-100">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Programa</th>
                    <th>Colonia / Vialidad</th>
                    <th>Costo Total</th>
                    <th>ML</th>
                    <th>Vecinos</th>
                    <th>Gobierno</th>
                    <th>Vecinos</th>
                    <th>Costo/ML</th>
                    <th>Estatus</th>
                    <th class="text-end no-export">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($obras as $o): ?>
                    <tr>
                        <td><?= esc($o['id_obra']) ?></td>
                        <td><strong><?= esc($o['nombre_obra']) ?></strong></td>
                        <td>
                            <span class="badge text-bg-primary">
                                <?= esc($o['nombre_programa']) ?> (<?= esc($o['anio_programa']) ?>)
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">
                                <?= esc($o['nombre_colonia']) ?><br>
                                <?= esc($o['nombre_vialidad']) ?>
                            </small>
                        </td>
                        <td class="text-end">
                            $<?= number_format($o['costo_total'], 2) ?>
                        </td>
                        <td class="text-end">
                            <?= number_format($o['total_ml'], 2) ?> ml
                        </td>
                        <td class="text-center">
                            <span class="badge text-bg-secondary" title="Derecha">
                                <?= esc($o['derecha']) ?> D
                            </span>
                            <span class="badge text-bg-secondary" title="Izquierda">
                                <?= esc($o['izquierda']) ?> I
                            </span>
                            <br>
                            <small class="text-muted">Total: <?= $o['derecha'] + $o['izquierda'] ?></small>
                        </td>
                        <td class="text-end">
                            <span class="badge text-bg-success">
                                <?= esc($o['por_gobierno']) ?>%
                            </span>
                            <br>
                            <small>$<?= number_format($o['monto_gobierno'], 2) ?></small>
                        </td>
                        <td class="text-end">
                            <span class="badge text-bg-warning">
                                <?= esc($o['por_vecinos']) ?>%
                            </span>
                            <br>
                            <small>$<?= number_format($o['monto_vecinos'], 2) ?></small>
                        </td>
                        <td class="text-end">
                            <strong>$<?= number_format($o['costo_x_ml'], 2) ?></strong>
                        </td>
                        <td class="text-center">
                            <?php if ($o['estatus_obra'] == 1): ?>
                                <span class="badge text-bg-success">Activa</span>
                            <?php else: ?>
                                <span class="badge text-bg-danger">Inactiva</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end no-export">
							<a href="<?= site_url('vecinos/por-obra/' . $o['id_obra']) ?>" class="btn btn-outline-info" title="Ver Vecinos">
								<i class="bi bi-people"></i>
							</a>
							<?php if (puede('obras.administrar')): ?>
								<a href="<?= site_url('obras/editar/' . $o['id_obra']) ?>" class="btn btn-outline-primary" title="Editar">
									<i class="bi bi-pencil"></i>
								</a>
								<form action="<?= site_url('obras/eliminar/' . $o['id_obra']) ?>" method="post" class="d-inline" onsubmit="return confirm('¿Eliminar la obra &quot;<?= esc($o['nombre_obra'], 'js') ?>&quot;?');">
									<?= csrf_field() ?>
									<button type="submit" class="btn btn-outline-danger" title="Eliminar">
										<i class="bi bi-trash"></i>
									</button>
								</form>
							<?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initDataTable('#tablaObras', {
        columnDefs: [
            { orderable: false, targets: 11 },
            { searchable: false, targets: 11 },
            { className: 'text-end', targets: [4, 5, 7, 8, 9] },
            { className: 'text-center', targets: [6, 10] }
        ],
        order: [[0, 'desc']]
    });
});
</script>
<?= $this->endSection() ?>
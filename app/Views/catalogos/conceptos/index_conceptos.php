<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title mb-0"><i class="bi bi-journal-bookmark me-2"></i>Catálogo de Conceptos</h3>
        <div class="card-tools">
			<?php if (puede('catalogos.administrar')): ?>
				<a href="<?= site_url('catalogos/conceptos/nuevo') ?>" class="btn btn-primary btn-sm">
					<i class="bi bi-plus-lg"></i> Nuevo Concepto
				</a>
			<?php endif; ?>
		</div>
    </div>

    <div class="card-body">
        <table id="tablaConceptos" class="table table-striped table-hover w-100">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
					<th>Código SAP</th>
                    <th>Clave</th>
                    <th>Concepto</th>
                    <th>Estatus</th>
                    <th class="text-end">Monto</th>
                    <th class="text-end no-export">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($conceptos as $c): ?>
                    <tr>
                        <td><?= esc($c['id_concepto']) ?></td>
                        <td>
                            <?php if (!empty($c['codigo_sap'])): ?>
                                <code><?= esc($c['codigo_sap']) ?></code>
                                <?php if (!empty($c['sap_descripcion'])): ?>
                                    <small class="text-muted d-block"><?= esc($c['sap_descripcion']) ?></small>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
						<td><code><?= esc($c['clave_concepto']) ?></code></td>
                        <td><?= esc($c['nombre_concepto']) ?></td>
                        
                        <td class="text-end fw-bold text-success" data-order="<?= $c['monto_concepto'] ?>">
                            $<?= number_format((float)$c['monto_concepto'], 2) ?>
                        </td>
						<td class="text-center">
							<?php if ($c['estatus'] == 1): ?>
								<span class="badge text-bg-success">Activo</span>
							<?php else: ?>
								<span class="badge text-bg-secondary">Inactivo</span>
							<?php endif; ?>
						</td>
                        <td class="text-end no-export">
                            <?php if (puede('catalogos.administrar')): ?>
								<a href="<?= site_url('catalogos/conceptos/editar/' . $c['id_concepto']) ?>" class="btn btn-outline-warning" title="Editar">
									<i class="bi bi-pencil"></i>
								</a>
								<form action="<?= site_url('catalogos/conceptos/eliminar/' . $c['id_concepto']) ?>" method="post" class="d-inline" onsubmit="return confirm('¿Eliminar el concepto &quot;<?= esc($c['nombre_concepto'], 'js') ?>&quot;?');">
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
    initDataTable('#tablaConceptos', {
        columnDefs: [
            { orderable: false, targets: 5 },
            { searchable: false, targets: 5 }
        ]
    });
});
</script>
<?= $this->endSection() ?>
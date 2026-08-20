<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title mb-0">
            <i class="bi bi-calendar-event me-2"></i>Catálogo de Programas
        </h3>
        <div class="card-tools">
			<?php if (puede('programas.administrar')): ?>
				<a href="<?= site_url('catalogos/programas/nuevo') ?>" class="btn btn-primary btn-sm">
					<i class="bi bi-plus-lg"></i> Nuevo Programa
				</a>
			<?php endif; ?>
		</div>
    </div>

    <div class="card-body">
        <table id="tablaProgramas" class="table table-striped table-hover w-100">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre del Programa</th>
                    <th>Año</th>
                    <th class="text-end no-export">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($programas as $p): ?>
                    <tr>
                        <td><?= esc($p['id_programa']) ?></td>
                        <td><strong><?= esc($p['nombre_programa']) ?></strong></td>
                        <td>
                            <span class="badge text-bg-info">
                                <?= esc($p['anio_programa']) ?>
                            </span>
                        </td>
                        <td class="text-end no-export">
                            <?php if (puede('programas.administrar')): ?>
								<a href="<?= site_url('catalogos/programas/editar/' . $p['id_programa']) ?>" class="btn btn-outline-warning" title="Editar">
									<i class="bi bi-pencil"></i>
								</a>
								<form action="<?= site_url('catalogos/programas/eliminar/' . $p['id_programa']) ?>" method="post" class="d-inline" onsubmit="return confirm('¿Eliminar el programa &quot;<?= esc($p['nombre_programa'], 'js') ?>&quot;?');">
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
    initDataTable('#tablaProgramas', {
        columnDefs: [
            { orderable: false, targets: 3 },
            { searchable: false, targets: 3 }
        ],
        order: [[2, 'desc'], [1, 'asc']]
    });
});
</script>
<?= $this->endSection() ?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title mb-0"><i class="bi bi-geo-alt me-2"></i>Catálogo de Colonias</h3>
        <div class="card-tools">
			<?php if (puede('catalogos.administrar')): ?>
				<a href="<?= site_url('catalogos/colonias/nuevo') ?>" class="btn btn-primary btn-sm">
					<i class="bi bi-plus-lg"></i> Nueva Colonia
				</a>
			<?php endif; ?>
		</div>
    </div>

    <div class="card-body">
        <table id="tablaColonias" class="table table-striped table-hover w-100">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th class="text-end no-export">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($colonias as $c): ?>
                    <tr>
                        <td><?= esc($c['id_colonia']) ?></td>
                        <td><strong><?= esc($c['nombre_colonia']) ?></strong></td>
                        <td class="text-end no-export">
                            <?php if (puede('catalogos.administrar')): ?>
								<a href="<?= site_url('catalogos/colonias/editar/' . $c['id_colonia']) ?>" 
								   class="btn btn-outline-warning" 
								   title="Editar">
									<i class="bi bi-pencil"></i>
								</a>
								<form action="<?= site_url('catalogos/colonias/eliminar/' . $c['id_colonia']) ?>" method="post" class="d-inline" onsubmit="return confirm('¿Eliminar la colonia &quot;<?= esc($c['nombre_colonia'], 'js') ?>&quot;?');">
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
    initDataTable('#tablaColonias', {
        columnDefs: [
            { orderable: false, targets: 2 },
            { searchable: false, targets: 2 }
        ]
    });
});
</script>
<?= $this->endSection() ?>
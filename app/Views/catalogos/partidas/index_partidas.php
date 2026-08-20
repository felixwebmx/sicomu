<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title mb-0"><i class="bi bi-journal-bookmark me-2"></i>Catálogo de Partidas</h3>
        <div class="card-tools">
			<?php if (puede('catalogos.administrar')): ?>
				<a href="<?= site_url('catalogos/partidas/nuevo') ?>" class="btn btn-primary btn-sm">
					<i class="bi bi-plus-lg"></i> Nueva Partida
				</a>
			<?php endif; ?>
		</div>
    </div>

    <div class="card-body">
        <table id="tablaPartidas" class="table table-striped table-hover w-100">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Clave</th>
                    <th>Partida</th>
                    <th>Cuenta</th>
                    <th>Estatus</th>
                    <th class="text-end no-export">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($partidas as $p): ?>
                    <tr>
                        <td><?= esc($p['id_partida']) ?></td>
                        <td><?= esc($p['clave_partida']) ?></td>
                        <td><?= esc($p['nombre_partida']) ?></td>
                        <td>
                            <span class="badge text-bg-info">
                                <i class="bi bi-folder me-1"></i><?= esc($p['nombre_cuenta']) ?>
                            </span>
                        </td>
						<td class="text-center">
							<?php if ($p['estatus'] == 1): ?>
								<span class="badge text-bg-success">Activo</span>
							<?php else: ?>
								<span class="badge text-bg-secondary">Inactivo</span>
							<?php endif; ?>
						</td>
                        <td class="text-end no-export">
                            <?php if (puede('catalogos.administrar')): ?>
								<a href="<?= site_url('catalogos/partidas/editar/' . $p['id_partida']) ?>" class="btn btn-outline-warning" title="Editar">
									<i class="bi bi-pencil"></i>
								</a>
								<form action="<?= site_url('catalogos/partidas/eliminar/' . $p['id_partida']) ?>" method="post" class="d-inline" onsubmit="return confirm('¿Eliminar la partida &quot;<?= esc($p['nombre_partida'], 'js') ?>&quot;?');">
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
    initDataTable('#tablaPartidas', {
        columnDefs: [
            { orderable: false, targets: 4 },
            { searchable: false, targets: 4 }
        ]
    });
});
</script>
<?= $this->endSection() ?>
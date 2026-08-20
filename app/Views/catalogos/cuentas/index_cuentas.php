<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title mb-0"><i class="bi bi-journal-bookmark me-2"></i>Catálogo de Cuentas</h3>
        <div class="card-tools">
			<?php if (puede('catalogos.administrar')): ?>
				<a href="<?= site_url('catalogos/cuentas/nuevo') ?>" class="btn btn-primary btn-sm">
					<i class="bi bi-plus-lg"></i> Nueva Cuenta
				</a>
			<?php endif; ?>
		</div>
	</div>

    <div class="card-body">
        <table id="tablaCuentas" class="table table-striped table-hover w-100">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Clave</th>
                    <th>Nombre</th>
                    <th>Estatus</th>
                    <th class="text-end no-export">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cuentas as $c): ?>
                    <tr>
                        <td><?= esc($c['id_cuenta']) ?></td>
                        <td><code class="fw-bold"><?= esc($c['clave_cuenta']) ?></code></td>
                        <td><?= esc($c['nombre_cuenta']) ?></td>
						<td class="text-center">
							<?php if ($c['estatus'] == 1): ?>
								<span class="badge text-bg-success">Activo</span>
							<?php else: ?>
								<span class="badge text-bg-secondary">Inactivo</span>
							<?php endif; ?>
						</td>
                        <td class="text-end no-export">
                            <?php if (puede('catalogos.administrar')): ?>
								<a href="<?= site_url('catalogos/cuentas/editar/' . $c['id_cuenta']) ?>" 
								   class="btn btn-outline-warning" 
								   title="Editar">
									<i class="bi bi-pencil"></i>
								</a>
								<form action="<?= site_url('catalogos/cuentas/eliminar/' . $c['id_cuenta']) ?>" method="post" class="d-inline" onsubmit="return confirm('¿Eliminar la cuenta &quot;<?= esc($c['nombre_cuenta'], 'js') ?>&quot;?');">
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
    initDataTable('#tablaCuentas', {
        columnDefs: [
            { orderable: false, targets: 3 },
            { searchable: false, targets: 3 }
        ]
    });
});
</script>
<?= $this->endSection() ?>
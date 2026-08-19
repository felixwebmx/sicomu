<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="card mb-4">
	<div class="card-header">
		<div class="row g-2 align-items-center">
			<div class="col-12 col-md-4">
				<h3 class="card-title"><i class="bi bi-shield-lock me-2"></i>Listado de Roles</h3>
			</div>
			<div class="col-12 col-md-8">
				<div class="d-flex flex-wrap justify-content-md-end gap-2">
					<?php if (puede('roles.administrar')): ?>
						<a href="<?= site_url('roles/nuevo') ?>" class="btn btn-primary btn-sm">
							<i class="bi bi-plus-lg"></i> Nuevo Rol
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
    <div class="card-body">
        <table id="tablaRoles" class="table table-striped table-hover w-100">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Rol</th>
                    <th>Descripción</th>
                    <th>Estatus</th>
                    <th class="text-end no-export">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roles as $r): ?>
                    <tr>
                        <td><?= esc($r['id']) ?></td>
                        <td><strong><?= esc($r['nombre']) ?></strong></td>
                        <td><?= esc($r['descripcion']) ?></td>
                        <td>
                            <span class="badge text-bg-<?= $r['estatus'] === 'activo' ? 'success' : 'secondary' ?>">
                                <?= esc($r['estatus']) ?>
                            </span>
                        </td>
                        <td class="text-end no-export">
                            <?php if (puede('roles.administrar')): ?>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= site_url('roles/editar/' . $r['id']) ?>" 
                                       class="btn btn-outline-warning" 
                                       title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-outline-danger" 
                                            title="Eliminar"
                                            onclick="enviarPost('<?= site_url('roles/eliminar/' . $r['id']) ?>', '¿Eliminar el rol &quot;<?= esc($r['nombre'], 'js') ?>&quot;?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
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
    initDataTable('#tablaRoles', {
        columnDefs: [
            { orderable: false, targets: 4 }, // Desactivar ordenamiento en Acciones
            { searchable: false, targets: 4 }   // Desactivar búsqueda en Acciones
        ]
    });
});
</script>
<?= $this->endSection() ?>
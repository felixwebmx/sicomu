<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0"><i class="bi bi-signpost me-2"></i>Catálogo de Vialidades</h3>
        <?php if (puede('catalogos.administrar')): ?>
            <a href="<?= site_url('catalogos/vialidades/nuevo') ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Nueva Vialidad
            </a>
        <?php endif; ?>
    </div>

    <div class="card-body">
        <table id="tablaVialidades" class="table table-striped table-hover w-100">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Vialidad</th>
                    <th>Colonia</th>
                    <th class="text-end no-export">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vialidades as $v): ?>
                    <tr>
                        <td><?= esc($v['id_vialidad']) ?></td>
                        <td><strong><?= esc($v['nombre_vialidad']) ?></strong></td>
                        <td>
                            <span class="badge text-bg-info">
                                <i class="bi bi-geo-alt me-1"></i><?= esc($v['nombre_colonia']) ?>
                            </span>
                        </td>
                        <td class="text-end no-export">
                            <?php if (puede('catalogos.administrar')): ?>
								<a href="<?= site_url('catalogos/vialidades/editar/' . $v['id_vialidad']) ?>" 
								   class="btn btn-outline-warning" 
								   title="Editar">
									<i class="bi bi-pencil"></i>
								</a>
								<form action="<?= site_url('catalogos/vialidades/eliminar/' . $v['id_vialidad']) ?>" method="post" class="d-inline" onsubmit="return confirm('¿Eliminar la vialidad &quot;<?= esc($v['nombre_vialidad'], 'js') ?>&quot;?');">
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
    initDataTable('#tablaVialidades', {
        columnDefs: [
            { orderable: false, targets: 3 },
            { searchable: false, targets: 3 }
        ]
    });
});
</script>
<?= $this->endSection() ?>
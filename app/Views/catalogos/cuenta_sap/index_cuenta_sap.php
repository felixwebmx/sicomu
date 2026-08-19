<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0"><i class="bi bi-upc-scan me-2"></i>Catálogo de SAP</h3>
        <div class="d-flex gap-2">
            <form method="get" class="d-flex gap-2">
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Buscar código o descripción..."
                       value="<?= esc($busqueda ?? '') ?>" style="min-width: 250px;">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button>
                <?php if ($busqueda): ?>
                    <a href="<?= site_url('catalogos/sap') ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                <?php endif; ?>
            </form>
            <?php if (puede('catalogos.administrar')): ?>
                <a href="<?= site_url('catalogos/sap/nuevo') ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Nuevo
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card-body">
        <table id="tablaSap" class="table table-striped table-hover w-100">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Código SAP</th>
                    <th>Descripción</th>
                    <th class="text-end no-export">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cuentasSap as $s): ?>
                    <tr>
                        <td><?= esc($s['id']) ?></td>
                        <td><code class="fs-6"><?= esc($s['codigo_sap']) ?></code></td>
                        <td><?= esc($s['descripcion']) ?></td>
                        <td class="text-end no-export">
                            <?php if (puede('catalogos.administrar')): ?>
								<a href="<?= site_url('catalogos/sap/editar/' . $s['id']) ?>" class="btn btn-outline-warning" title="Editar">
									<i class="bi bi-pencil"></i>
								</a>
								<form action="<?= site_url('catalogos/sap/eliminar/' . $s['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('¿Eliminar el código SAP &quot;<?= esc($s['codigo_sap'], 'js') ?>&quot;?');">
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
    initDataTable('#tablaSap', {
        columnDefs: [
            { orderable: false, targets: 3 },
            { searchable: false, targets: 3 }
        ],
        order: [[1, 'asc']]
    });
});
</script>
<?= $this->endSection() ?>
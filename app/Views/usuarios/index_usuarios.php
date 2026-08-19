<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="card mb-4">
	<div class="card-header">
		<div class="row g-2 align-items-center">
			<div class="col-12 col-md-4">
				<h3 class="card-title"><i class="bi bi-people me-2"></i>Listado de Usuarios</h3>
			</div>
			<div class="col-12 col-md-8">
				<div class="d-flex flex-wrap justify-content-md-end gap-2">
					<?php if (puede('usuarios.administrar')): ?>
            <a href="<?= site_url('usuarios/nuevo') ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Nuevo Usuario
            </a>
        <?php endif; ?>
				</div>
			</div>
		</div>
	</div>
    <div class="card-body">
        <table id="tablaUsuarios" class="table table-striped table-hover w-100">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Nombre completo</th>
                    <th>Correo</th>
                    <th>Estatus</th>
                    <th>Último acceso</th>
                    <th class="text-end no-export">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= esc($u['id']) ?></td>
                        <td><code><?= esc($u['nombre_usuario']) ?></code></td>
                        <td><?= esc($u['nombre_completo']) ?></td>
                        <td><?= esc($u['correo']) ?></td>
                        <td>
                            <?php
                                $badge = match ($u['estatus']) {
                                    'activo'    => 'success',
                                    'inactivo'  => 'secondary',
                                    'bloqueado' => 'danger',
                                    default     => 'secondary',
                                };
                            ?>
                            <span class="badge text-bg-<?= $badge ?>"><?= esc($u['estatus']) ?></span>
                        </td>
                        <td data-order="<?= $u['ultimo_acceso'] ?? '0' ?>">
                            <?= $u['ultimo_acceso'] ? esc(date('d/m/Y H:i', strtotime($u['ultimo_acceso']))) : '<span class="text-muted">Nunca</span>' ?>
                        </td>
                        <td class="text-end no-export">
                            <?php if (puede('usuarios.administrar')): ?>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= site_url('usuarios/editar/' . $u['id']) ?>" 
                                       class="btn btn-outline-primary" 
                                       title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    <?php if ($u['estatus'] === 'bloqueado'): ?>
                                        <button type="button" 
                                                class="btn btn-outline-warning" 
                                                title="Desbloquear"
                                                onclick="enviarPost('<?= site_url('usuarios/desbloquear/' . $u['id']) ?>', '¿Desbloquear la cuenta de &quot;<?= esc($u['nombre_usuario'], 'js') ?>&quot;?')">
                                            <i class="bi bi-unlock"></i>
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($u['id'] !== (int) session()->get('usuario_id')): ?>
                                        <button type="button" 
                                                class="btn btn-outline-danger" 
                                                title="Eliminar"
                                                onclick="enviarPost('<?= site_url('usuarios/eliminar/' . $u['id']) ?>', '¿Eliminar al usuario &quot;<?= esc($u['nombre_usuario'], 'js') ?>&quot;?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    <?php endif; ?>
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
    initDataTable('#tablaUsuarios', {
        columnDefs: [
            { orderable: false, targets: 6 },
            { searchable: false, targets: 6 }
        ]
    });
});
</script>
<?= $this->endSection() ?>
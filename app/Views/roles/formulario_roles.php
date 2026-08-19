<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0"><?= esc($titulo) ?></h3>
        </div>

        <div class="card-body">

            <?php if (session()->getFlashdata('errores')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        <?php foreach (session()->getFlashdata('errores') as $err): ?>
                            <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('roles/guardar' . ($rol ? '/' . $rol['id'] : '')) ?>" method="post">
                <?= csrf_field() ?>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre del rol</label>
                        <input type="text" name="nombre" class="form-control"
                               value="<?= esc(old('nombre', $rol['nombre'] ?? '')) ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Estatus</label>
                        <select name="estatus" class="form-select">
                            <?php $estatusActual = old('estatus', $rol['estatus'] ?? 'activo'); ?>
                            <option value="activo" <?= $estatusActual === 'activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="inactivo" <?= $estatusActual === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="2"><?= esc(old('descripcion', $rol['descripcion'] ?? '')) ?></textarea>
                </div>

                <hr>
                <label class="form-label fw-bold">Permisos asignados</label>

                <?php foreach ($permisosPorModulo as $modulo => $permisos): ?>
                    <div class="card mb-2">
                        <div class="card-header py-2"><strong><?= esc($modulo) ?></strong></div>
                        <div class="card-body py-2">
                            <?php foreach ($permisos as $permiso): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           name="permisos[]" value="<?= $permiso['id'] ?>"
                                           id="permiso_<?= $permiso['id'] ?>"
                                           <?= in_array($permiso['id'], $permisosAsignados, true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="permiso_<?= $permiso['id'] ?>">
                                        <?= esc($permiso['nombre']) ?>
                                        <span class="text-muted small">(<?= esc($permiso['clave']) ?>)</span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <button type="submit" class="btn btn-primary mt-2">
                    <i class="bi bi-save"></i> Guardar
                </button>
                <a href="<?= site_url('roles') ?>" class="btn btn-secondary mt-2">Cancelar</a>
            </form>

        </div>
    </div>

<?= $this->endSection() ?>
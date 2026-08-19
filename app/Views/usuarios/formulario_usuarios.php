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

            <form action="<?= site_url('usuarios/guardar' . ($usuario ? '/' . $usuario['id'] : '')) ?>" method="post">
                <?= csrf_field() ?>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre de usuario</label>
                        <input type="text" name="nombre_usuario" class="form-control"
                               value="<?= esc(old('nombre_usuario', $usuario['nombre_usuario'] ?? '')) ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Correo</label>
                        <input type="email" name="correo" class="form-control"
                               value="<?= esc(old('correo', $usuario['correo'] ?? '')) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nombre completo</label>
                    <input type="text" name="nombre_completo" class="form-control"
                           value="<?= esc(old('nombre_completo', $usuario['nombre_completo'] ?? '')) ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Contraseña
                            <?php if ($usuario): ?>
                                <span class="text-muted small">(dejar en blanco para no modificarla)</span>
                            <?php endif; ?>
                        </label>
                        <input type="password" name="password" class="form-control"
                               <?= $usuario ? '' : 'required' ?>>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Estatus</label>
                        <select name="estatus" class="form-select">
                            <?php $estatusActual = old('estatus', $usuario['estatus'] ?? 'activo'); ?>
                            <option value="activo" <?= $estatusActual === 'activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="inactivo" <?= $estatusActual === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                            <option value="bloqueado" <?= $estatusActual === 'bloqueado' ? 'selected' : '' ?>>Bloqueado</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label d-block">Roles asignados</label>
                    <?php foreach ($roles as $rol): ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox"
                                   name="roles[]" value="<?= $rol['id'] ?>"
                                   id="rol_<?= $rol['id'] ?>"
                                   <?= in_array($rol['id'], $rolesAsignados, true) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="rol_<?= $rol['id'] ?>">
                                <?= esc($rol['nombre']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar
                </button>
                <a href="<?= site_url('usuarios') ?>" class="btn btn-secondary">Cancelar</a>
            </form>

        </div>
    </div>

<?= $this->endSection() ?>
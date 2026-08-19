<?= $this->extend('layouts/main') ?>
<?= $this->section('contenido') ?>

<div class="card card-info card-outline mb-4">
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
        <form action="<?= site_url('catalogos/cuentas/guardar' . ($cuenta ? '/' . $cuenta['id_cuenta'] : '')) ?>" method="post">
            <?= csrf_field() ?>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Clave</label>
                    <input type="text" name="clave_cuenta" class="form-control" maxlength="4"
                           value="<?= esc(old('clave_cuenta', $cuenta['clave_cuenta'] ?? '')) ?>" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label">Nombre de la cuenta</label>
                    <input type="text" name="nombre_cuenta" class="form-control"
                           value="<?= esc(old('nombre_cuenta', $cuenta['nombre_cuenta'] ?? '')) ?>" required>
                </div>
            </div>

            <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Guardar</button>
            <a href="<?= site_url('catalogos/cuentas') ?>" class="btn btn-danger"><i class="bi bi-x-circle me-1"></i> Cancelar</a>
        </form>

    </div>
</div>

<?= $this->endSection() ?>
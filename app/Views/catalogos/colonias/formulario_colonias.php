<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="card card-info card-outline mb-4">
    <div class="card-header">
        <h3 class="card-title mb-0">
            <i class="bi bi-geo-alt me-2"></i><?= esc($titulo) ?>
        </h3>
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

        <form action="<?= site_url('catalogos/colonias/guardar' . ($colonia ? '/' . $colonia['id_colonia'] : '')) ?>" method="post">
            <?= csrf_field() ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-tag me-1"></i>Nombre de la colonia
                    </label>
                    <input type="text" 
                           name="nombre_colonia" 
                           class="form-control" 
                           value="<?= esc(old('nombre_colonia', $colonia['nombre_colonia'] ?? '')) ?>" 
                           required
                           maxlength="50"
                           placeholder="Ej: Zona Centro, Deportiva, etc.">
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save me-1"></i> Guardar
                </button>
                <a href="<?= site_url('catalogos/colonias') ?>" class="btn btn-danger">
                    <i class="bi bi-x-circle me-1"></i> Cancelar
                </a>
            </div>
        </form>

    </div>
</div>

<?= $this->endSection() ?>
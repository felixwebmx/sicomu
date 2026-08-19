<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        border-radius: 0.375rem;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        padding-left: 0;
        line-height: 1.5;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<div class="card card-info card-outline mb-4">
    <div class="card-header">
        <h3 class="card-title mb-0">
            <i class="bi bi-signpost me-2"></i><?= esc($titulo) ?>
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

        <form action="<?= site_url('catalogos/vialidades/guardar' . ($vialidad ? '/' . $vialidad['id_vialidad'] : '')) ?>" method="post">
            <?= csrf_field() ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-geo-alt me-1"></i>Colonia
                    </label>
                    <select name="id_colonia" id="select_colonia" class="form-select" required>
                        <option value="">-- Seleccione una colonia --</option>
                        <?php foreach ($colonias as $c): ?>
                            <option value="<?= $c['id_colonia'] ?>"
                                <?= (old('id_colonia', $vialidad['id_colonia'] ?? '') == $c['id_colonia']) ? 'selected' : '' ?>>
                                <?= esc($c['nombre_colonia']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-tag me-1"></i>Nombre de la vialidad
                    </label>
                    <input type="text" 
                           name="nombre_vialidad" 
                           class="form-control" 
                           value="<?= esc(old('nombre_vialidad', $vialidad['nombre_vialidad'] ?? '')) ?>" 
                           required
                           maxlength="50"
                           placeholder="Ej: Miguel Hidalgo, Juárez, etc.">
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save me-1"></i> Guardar
                </button>
                <a href="<?= site_url('catalogos/vialidades') ?>" class="btn btn-danger">
                    <i class="bi bi-x-circle me-1"></i> Cancelar
                </a>
            </div>
        </form>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#select_colonia').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Seleccione una colonia --',
        allowClear: true,
        width: '100%'
    });
});
</script>
<?= $this->endSection() ?>
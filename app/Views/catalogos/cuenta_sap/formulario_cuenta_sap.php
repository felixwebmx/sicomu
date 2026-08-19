<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title mb-0"><i class="bi bi-upc-scan me-2"></i><?= esc($titulo) ?></h3>
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

                <form action="<?= site_url('catalogos/sap/guardar' . ($cuentaSap ? '/' . $cuentaSap['id'] : '')) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-upc me-1"></i>Código SAP *
                        </label>
                        <input type="text" 
                               name="codigo_sap" 
                               class="form-control" 
                               value="<?= esc(old('codigo_sap', $cuentaSap['codigo_sap'] ?? '')) ?>" 
                               required
                               maxlength="20"
                               placeholder="Ej: 1234567890">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-text-paragraph me-1"></i>Descripción
                        </label>
                        <input type="text" 
                               name="descripcion" 
                               class="form-control" 
                               value="<?= esc(old('descripcion', $cuentaSap['descripcion'] ?? '')) ?>" 
                               maxlength="100"
                               placeholder="Ej: Servicios de Recolección">
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save me-1"></i> Guardar
                        </button>
                        <a href="<?= site_url('catalogos/sap') ?>" class="btn btn-danger">
                            <i class="bi bi-x-circle me-1"></i> Cancelar
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
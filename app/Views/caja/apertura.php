<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card card-primary card-outline shadow">
            <div class="card-header text-center">
                <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Valores Iniciales</h5>
            </div>

            <?= form_open('caja/abrir') ?>
            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label fw-bold">Usuario</label>
                    <input type="text" class="form-control" value="<?= esc($nombreUsuario) ?>" disabled>
                </div>

                <div class="mb-3">
                    <label for="caja_id" class="form-label fw-bold">Caja</label>
                    <select name="caja_id" id="caja_id" class="form-select select2" required>
                        <option value="">-- Seleccione --</option>
                        <?php foreach ($cajas as $caja): ?>
                            <option value="<?= $caja['id'] ?>"><?= esc($caja['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">Folio Inicial</label>
                        <input type="text" class="form-control text-center" value="<?= esc($folioSiguiente) ?>" disabled>
                        <small class="text-muted">Siguiente folio disponible (informativo)</small>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label fw-bold">Fecha</label>
                        <input type="text" class="form-control text-center" value="<?= date('d/m/Y') ?>" disabled>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="monto_inicial" class="form-label fw-bold">Monto Inicial (fondo de caja)</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" name="monto_inicial" id="monto_inicial"
                               class="form-control" value="0.00">
                    </div>
                </div>

            </div>
            <div class="card-footer d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle me-1"></i> Iniciar
                </button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function () {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });
</script>
<?= $this->endSection() ?>
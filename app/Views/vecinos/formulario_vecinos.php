<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        border-radius: 0.375rem;
    }
    .calculo-preview {
        background: #f8f9fa;
        border-left: 4px solid #198754;
        padding: 1rem;
        border-radius: 0.375rem;
    }
    .calculo-preview .valor {
        font-size: 1.2rem;
        font-weight: 600;
        color: #198754;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<div class="card card-info card-outline mb-4">
    <div class="card-header">
        <h3 class="card-title mb-0">
            <i class="bi bi-person-plus me-2"></i><?= esc($titulo) ?>
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

        <form action="<?= site_url('vecinos/guardar' . ($vecino ? '/' . $vecino['id_vecino'] : '')) ?>" method="post" id="formVecino">
            <?= csrf_field() ?>

            <!-- Campo oculto para redirección -->
            <?php if ($obraPreseleccionada): ?>
                <input type="hidden" name="redirect_obra" value="<?= $obraPreseleccionada['id_obra'] ?>">
            <?php endif; ?>

            <!-- ─── Sección 1: Obra ─── -->
            <h5 class="text-primary mb-3">
                <i class="bi bi-building me-1"></i>Obra Asignada
            </h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-hammer me-1"></i>Obra
                    </label>
                    <select name="id_obra" id="select_obra" class="form-select" required <?= $obraPreseleccionada ? 'readonly' : '' ?>>
                        <option value="">-- Seleccione una obra --</option>
                        <?php foreach ($obras as $o): ?>
                            <option value="<?= $o['id_obra'] ?>"
                                data-costo-ml="<?= $o['costo_x_ml'] ?>"
                                <?= (old('id_obra', $vecino['id_obra'] ?? ($obraPreseleccionada['id_obra'] ?? '')) == $o['id_obra']) ? 'selected' : '' ?>>
                                <?= esc($o['nombre_obra']) ?> - $<?= number_format($o['costo_x_ml'], 2) ?>/ml
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($obraPreseleccionada): ?>
                        <input type="hidden" name="id_obra" value="<?= $obraPreseleccionada['id_obra'] ?>">
                    <?php endif; ?>
                </div>
            </div>

            <!-- ─── Sección 2: Datos del Vecino ─── -->
            <h5 class="text-primary mb-3 mt-4">
                <i class="bi bi-person me-1"></i>Datos del Vecino
            </h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-person-fill me-1"></i>Nombre completo
                    </label>
                    <input type="text" 
                           name="nombre_vecino" 
                           class="form-control" 
                           value="<?= esc(old('nombre_vecino', $vecino['nombre_vecino'] ?? '')) ?>" 
                           required
                           maxlength="50"
                           placeholder="Ej: Juan Pérez García">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-geo me-1"></i>Colonia
                    </label>
                    <select name="id_colonia" id="select_colonia" class="form-select" required>
                        <option value="">-- Seleccione una colonia --</option>
                        <?php foreach ($colonias as $c): ?>
                            <option value="<?= $c['id_colonia'] ?>"
                                <?= (old('id_colonia', $vecino['id_colonia'] ?? '') == $c['id_colonia']) ? 'selected' : '' ?>>
                                <?= esc($c['nombre_colonia']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-signpost me-1"></i>Vialidad
                    </label>
                    <select name="id_vialidad" id="select_vialidad" class="form-select" required>
                        <option value="">-- Seleccione una colonia primero --</option>
                        <?php foreach ($vialidades as $v): ?>
                            <option value="<?= $v['id_vialidad'] ?>"
                                <?= (old('id_vialidad', $vecino['id_vialidad'] ?? '') == $v['id_vialidad']) ? 'selected' : '' ?>>
                                <?= esc($v['nombre_vialidad']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">No. Ext.</label>
                    <input type="text" 
                           name="no_exterior" 
                           class="form-control" 
                           value="<?= esc(old('no_exterior', $vecino['no_exterior'] ?? '')) ?>" 
                           required
                           maxlength="5"
                           placeholder="123">
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Bis</label>
                    <input type="text" 
                           name="no_bis" 
                           class="form-control" 
                           value="<?= esc(old('no_bis', $vecino['no_bis'] ?? '')) ?>" 
                           maxlength="2"
                           placeholder="A">
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">No. Int.</label>
                    <input type="text" 
                           name="no_interior" 
                           class="form-control" 
                           value="<?= esc(old('no_interior', $vecino['no_interior'] ?? '')) ?>" 
                           maxlength="5"
                           placeholder="5">
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-compass me-1"></i>Acera
                    </label>
                    <select name="acera" class="form-select" required>
                        <option value="">-- --</option>
                        <option value="D" <?= (old('acera', $vecino['acera'] ?? '') == 'D') ? 'selected' : '' ?>>Derecha</option>
                        <option value="I" <?= (old('acera', $vecino['acera'] ?? '') == 'I') ? 'selected' : '' ?>>Izquierda</option>
                    </select>
                </div>
            </div>

            <!-- ─── Sección 3: Metros y Cálculos ─── -->
            <h5 class="text-primary mb-3 mt-4">
                <i class="bi bi-rulers me-1"></i>Metros Lineales y Aportación
            </h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-rulers me-1"></i>Metros lineales (ML)
                    </label>
                    <div class="input-group">
                        <input type="number" 
                               name="ml" 
                               id="input_ml"
                               class="form-control" 
                               value="<?= esc(old('ml', $vecino['ml'] ?? '')) ?>" 
                               required
                               step="0.01"
                               min="0.01"
                               placeholder="10.50">
                        <span class="input-group-text">ml</span>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-currency-dollar me-1"></i>Costo por ML (de la obra)
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" 
                               name="costo_ml" 
                               id="input_costo_ml"
                               class="form-control" 
                               value="<?= esc(old('costo_ml', $vecino['costo_ml'] ?? ($obraPreseleccionada['costo_x_ml'] ?? ''))) ?>" 
                               readonly
                               step="0.01">
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-toggle-on me-1"></i>Estatus
                    </label>
                    <select name="estatus_vecino" class="form-select" required>
                        <option value="1" <?= (old('estatus_vecino', $vecino['estatus_vecino'] ?? '1') == '1') ? 'selected' : '' ?>>Activo</option>
                        <option value="0" <?= (old('estatus_vecino', $vecino['estatus_vecino'] ?? '1') == '0') ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
            </div>

            <!-- ─── Preview de Cálculos ─── -->
            <div class="calculo-preview mt-3 mb-4">
                <div class="row">
                    <div class="col-md-6 text-center">
                        <small class="text-muted d-block">Total Aportación</small>
                        <span class="valor" id="preview_total">$0.00</span>
                    </div>
                    <div class="col-md-6 text-center">
                        <small class="text-muted d-block">Costo por Metro Lineal</small>
                        <span class="valor" id="preview_costo_ml">$0.00</span>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save me-1"></i> Guardar
                </button>
                <a href="<?= site_url(isset($obraPreseleccionada) ? 'obras/vecinos/' . $obraPreseleccionada['id_obra'] : 'vecinos') ?>" class="btn btn-danger">
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
    // Select2
    $('#select_obra, #select_colonia').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Seleccione --',
        allowClear: true,
        width: '100%'
    });

    $('#select_vialidad').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Seleccione una colonia primero --',
        allowClear: true,
        width: '100%'
    });

    // AJAX: Cargar vialidades al cambiar colonia
    $('#select_colonia').on('change', function() {
        const idColonia = $(this).val();
        const $vialidad = $('#select_vialidad');
        
        $vialidad.empty().append('<option value="">-- Cargando... --</option>').trigger('change');
        
        if (!idColonia) {
            $vialidad.empty().append('<option value="">-- Seleccione una colonia primero --</option>').trigger('change');
            return;
        }

        fetch('<?= site_url('catalogos/ajax/vialidades-por-colonia/') ?>' + idColonia)
            .then(r => r.json())
            .then(data => {
                $vialidad.empty().append('<option value="">-- Seleccione una vialidad --</option>');
                if (data.success && data.data.length > 0) {
                    data.data.forEach(v => {
                        $vialidad.append(new Option(v.nombre_vialidad, v.id_vialidad));
                    });
                }
                $vialidad.trigger('change');
            })
            .catch(() => {
                $vialidad.empty().append('<option value="">-- Error al cargar --</option>').trigger('change');
            });
    });

    // ─── Cálculos en tiempo real ───
    const $obra = $('#select_obra');
    const $ml = $('#input_ml');
    const $costoMl = $('#input_costo_ml');

    function actualizarCostoMl() {
        const selected = $obra.find(':selected');
        const costoMl = parseFloat(selected.data('costo-ml')) || 0;
        $costoMl.val(costoMl.toFixed(2));
        calcularTotal();
    }

    function calcularTotal() {
        const ml = parseFloat($ml.val()) || 0;
        const costoMl = parseFloat($costoMl.val()) || 0;
        const total = ml * costoMl;

        $('#preview_total').text('$' + total.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#preview_costo_ml').text('$' + costoMl.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }

    $obra.on('change', actualizarCostoMl);
    $ml.on('input', calcularTotal);

    // Calcular al cargar
    actualizarCostoMl();
});
</script>
<?= $this->endSection() ?>
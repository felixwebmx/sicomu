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
    .calculo-preview {
        background: #f8f9fa;
        border-left: 4px solid #0d6efd;
        padding: 1rem;
        border-radius: 0.375rem;
    }
    .calculo-preview .valor {
        font-size: 1.1rem;
        font-weight: 600;
        color: #0d6efd;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<div class="card card-info card-outline mb-4">
    <div class="card-header">
        <h3 class="card-title mb-0">
            <i class="bi bi-building me-2"></i><?= esc($titulo) ?>
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

        <form action="<?= site_url('obras/guardar' . ($obra ? '/' . $obra['id_obra'] : '')) ?>" method="post" id="formObra">
            <?= csrf_field() ?>

            <!-- ─── Sección 1: Información General ─── -->
            <h5 class="text-primary mb-3">
                <i class="bi bi-info-circle me-1"></i>Información General
            </h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-tag me-1"></i>Nombre de la obra
                    </label>
                    <input type="text" 
                           name="nombre_obra" 
                           class="form-control" 
                           value="<?= esc(old('nombre_obra', $obra['nombre_obra'] ?? '')) ?>" 
                           required
                           maxlength="100"
                           placeholder="Ej: Pavimentación Calle Hidalgo">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-calendar-event me-1"></i>Programa
                    </label>
                    <select name="id_programa" id="select_programa" class="form-select" required>
                        <option value="">-- Seleccione un programa --</option>
                        <?php foreach ($programas as $p): ?>
                            <option value="<?= $p['id_programa'] ?>"
                                <?= (old('id_programa', $obra['id_programa'] ?? '') == $p['id_programa']) ? 'selected' : '' ?>>
                                <?= esc($p['nombre_programa']) ?> (<?= esc($p['anio_programa']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- ─── Sección 2: Ubicación ─── -->
            <h5 class="text-primary mb-3 mt-4">
                <i class="bi bi-geo-alt me-1"></i>Ubicación
            </h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-geo me-1"></i>Colonia
                    </label>
                    <select name="id_colonia" id="select_colonia" class="form-select" required>
                        <option value="">-- Seleccione una colonia --</option>
                        <?php foreach ($colonias as $c): ?>
                            <option value="<?= $c['id_colonia'] ?>"
                                <?= (old('id_colonia', $obra['id_colonia'] ?? '') == $c['id_colonia']) ? 'selected' : '' ?>>
                                <?= esc($c['nombre_colonia']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-signpost me-1"></i>Vialidad
                    </label>
                    <select name="id_vialidad" id="select_vialidad" class="form-select" required>
                        <option value="">-- Seleccione una vialidad --</option>
                        <?php foreach ($vialidades as $v): ?>
                            <option value="<?= $v['id_vialidad'] ?>"
                                <?= (old('id_vialidad', $obra['id_vialidad'] ?? '') == $v['id_vialidad']) ? 'selected' : '' ?>>
                                <?= esc($v['nombre_vialidad']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- ─── Sección 3: Costos y Metros ─── -->
            <h5 class="text-primary mb-3 mt-4">
                <i class="bi bi-currency-dollar me-1"></i>Costos y Metros Lineales
            </h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-cash-stack me-1"></i>Costo total de la obra
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" 
                               name="costo_total" 
                               id="costo_total"
                               class="form-control" 
                               value="<?= esc(old('costo_total', $obra['costo_total'] ?? '')) ?>" 
                               required
                               step="0.01"
                               min="0.01"
                               placeholder="7,400,634.29">
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-rulers me-1"></i>Total de metros lineales
                    </label>
                    <div class="input-group">
                        <input type="number" 
                               name="total_ml" 
                               id="total_ml"
                               class="form-control" 
                               value="<?= esc(old('total_ml', $obra['total_ml'] ?? '')) ?>" 
                               required
                               step="0.01"
                               min="0.01"
                               placeholder="1699.80">
                        <span class="input-group-text">ml</span>
                    </div>
                </div>
            </div>

            <!-- ─── Sección 4: Vecinos ─── -->
            <h5 class="text-primary mb-3 mt-4">
                <i class="bi bi-people me-1"></i>Vecinos
            </h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-arrow-right me-1"></i>Acera derecha
                    </label>
                    <input type="number" 
                           name="derecha" 
                           id="derecha"
                           class="form-control" 
                           value="<?= esc(old('derecha', $obra['derecha'] ?? '0')) ?>" 
                           required
                           min="0"
                           step="1"
                           placeholder="872">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-arrow-left me-1"></i>Acera izquierda
                    </label>
                    <input type="number" 
                           name="izquierda" 
                           id="izquierda"
                           class="form-control" 
                           value="<?= esc(old('izquierda', $obra['izquierda'] ?? '0')) ?>" 
                           required
                           min="0"
                           step="1"
                           placeholder="828">
                </div>
            </div>

            <!-- ─── Sección 5: Porcentajes ─── -->
            <h5 class="text-primary mb-3 mt-4">
                <i class="bi bi-percent me-1"></i>Distribución de Aportación
            </h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-building me-1"></i>% Gobierno
                    </label>
                    <div class="input-group">
                        <input type="number" 
                               name="por_gobierno" 
                               id="por_gobierno"
                               class="form-control" 
                               value="<?= esc(old('por_gobierno', $obra['por_gobierno'] ?? '90')) ?>" 
                               required
                               min="0"
                               max="100"
                               step="1"
                               placeholder="90">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-people-fill me-1"></i>% Vecinos
                    </label>
                    <div class="input-group">
                        <input type="number" 
                               name="por_vecinos" 
                               id="por_vecinos"
                               class="form-control" 
                               value="<?= esc(old('por_vecinos', $obra['por_vecinos'] ?? '10')) ?>" 
                               required
                               min="0"
                               max="100"
                               step="1"
                               placeholder="10">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>

            <!-- ─── Preview de Cálculos ─── -->
            <div class="calculo-preview mt-4 mb-4">
                <h6 class="mb-3"><i class="bi bi-calculator me-1"></i>Cálculos automáticos (preview)</h6>
                <div class="row">
                    <div class="col-md-4 text-center mb-2">
                        <small class="text-muted d-block">Aportación Gobierno</small>
                        <span class="valor" id="preview_monto_gobierno">$0.00</span>
                    </div>
                    <div class="col-md-4 text-center mb-2">
                        <small class="text-muted d-block">Aportación Vecinos</small>
                        <span class="valor" id="preview_monto_vecinos">$0.00</span>
                    </div>
                    <div class="col-md-4 text-center mb-2">
                        <small class="text-muted d-block">Costo por Metro Lineal</small>
                        <span class="valor" id="preview_costo_x_ml">$0.00</span>
                    </div>
                </div>
                <div class="mt-2 text-center">
                    <small class="text-muted">Total vecinos: <strong id="preview_total_vecinos">0</strong></small>
                    <span class="mx-2">|</span>
                    <small class="text-muted">Suma de %: <strong id="preview_suma_porcentaje">0%</strong></small>
                </div>
            </div>

            <!-- ─── Estatus ─── -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-toggle-on me-1"></i>Estatus
                    </label>
                    <select name="estatus_obra" class="form-select" required>
                        <option value="1" <?= (old('estatus_obra', $obra['estatus_obra'] ?? '1') == '1') ? 'selected' : '' ?>>
                            Activa
                        </option>
                        <option value="0" <?= (old('estatus_obra', $obra['estatus_obra'] ?? '1') == '0') ? 'selected' : '' ?>>
                            Inactiva
                        </option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save me-1"></i> Guardar
                </button>
                <a href="<?= site_url('obras') ?>" class="btn btn-danger">
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
    // Select2 para selects
    $('#select_programa, #select_colonia').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Seleccione --',
        allowClear: true,
        width: '100%'
    });

    // Select2 para vialidad (inicialmente vacío o con datos)
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
    const $costoTotal = $('#costo_total');
    const $totalMl = $('#total_ml');
    const $derecha = $('#derecha');
    const $izquierda = $('#izquierda');
    const $porGobierno = $('#por_gobierno');
    const $porVecinos = $('#por_vecinos');

    function calcularPreview() {
        const costoTotal = parseFloat($costoTotal.val()) || 0;
        const totalMl = parseFloat($totalMl.val()) || 0;
        const derecha = parseInt($derecha.val()) || 0;
        const izquierda = parseInt($izquierda.val()) || 0;
        const porGob = parseInt($porGobierno.val()) || 0;
        const porVec = parseInt($porVecinos.val()) || 0;

        const montoGobierno = costoTotal * (porGob / 100);
        const montoVecinos = costoTotal * (porVec / 100);
        const costoXml = totalMl > 0 ? montoVecinos / totalMl : 0;
        const totalVecinos = derecha + izquierda;
        const sumaPorcentaje = porGob + porVec;

        $('#preview_monto_gobierno').text('$' + montoGobierno.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#preview_monto_vecinos').text('$' + montoVecinos.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#preview_costo_x_ml').text('$' + costoXml.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#preview_total_vecinos').text(totalVecinos);
        
        const $suma = $('#preview_suma_porcentaje');
        $suma.text(sumaPorcentaje + '%');
        if (sumaPorcentaje !== 100) {
            $suma.addClass('text-danger').removeClass('text-success');
        } else {
            $suma.addClass('text-success').removeClass('text-danger');
        }
    }

    // Event listeners para cálculos
    [$costoTotal, $totalMl, $derecha, $izquierda, $porGobierno, $porVecinos].forEach($el => {
        $el.on('input change', calcularPreview);
    });

    // Calcular al cargar (edición)
    calcularPreview();

    // Validación antes de enviar
    $('#formObra').on('submit', function(e) {
        const porGob = parseInt($porGobierno.val()) || 0;
        const porVec = parseInt($porVecinos.val()) || 0;
        
        if (porGob + porVec !== 100) {
            e.preventDefault();
            alert('La suma de los porcentajes de gobierno y vecinos debe ser exactamente 100%.');
            return false;
        }
    });
});
</script>
<?= $this->endSection() ?>
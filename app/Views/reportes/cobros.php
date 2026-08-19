<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<style>
    /* Ajuste de altura para Select2 con Bootstrap 5 */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
        padding-top: 0.375rem;
        padding-bottom: 0.375rem;
    }
    .select2-container--bootstrap-5 .select2-selection--single {
        padding-left: 0.75rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0"><i class="bi bi-file-earmark-excel me-2"></i>Reportes de Cobros</h3>
    </div>
    <div class="card-body">

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form method="post">
            <?= csrf_field() ?>

            <!-- ═══════════════════════════════════════════════════
                 SECCIÓN 1: REPORTES POR FECHA
                 ═══════════════════════════════════════════════════ -->
            <h5 class="text-primary mb-3 border-bottom pb-2">
                <i class="bi bi-calendar-range me-2"></i>Reportes por Rango de Fechas
            </h5>

            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control"
                           value="<?= date('Y-m-01') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control"
                           value="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <div class="row mb-5">
                <!-- Servicios -->
                <div class="col-md-4 mb-3">
                    <div class="card border-primary h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title text-primary">
                                <i class="bi bi-receipt me-2"></i>Servicios
                            </h5>
                            <p class="text-muted small">
                                Cuenta · Partida · Concepto · Contribuyente · Folio · Fecha
                            </p>
                            <button type="submit"
                                    formaction="<?= site_url('reportes/cobros/servicios/excel') ?>"
                                    class="btn btn-primary">
                                <i class="bi bi-file-earmark-excel me-2"></i>Descargar Excel
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Aportaciones -->
                <div class="col-md-4 mb-3">
                    <div class="card border-success h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title text-success">
                                <i class="bi bi-house-door me-2"></i>Aportaciones
                            </h5>
                            <p class="text-muted small">
                                Vecino · Folio · Obra · Monto · Fecha · Estatus
                            </p>
                            <button type="submit"
                                    formaction="<?= site_url('reportes/cobros/aportaciones/excel') ?>"
                                    class="btn btn-success">
                                <i class="bi bi-file-earmark-excel me-2"></i>Descargar Excel
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Arqueos -->
                <div class="col-md-4 mb-3">
                    <div class="card border-warning h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title text-warning">
                                <i class="bi bi-calculator me-2"></i>Resumen Diario por Caja
                            </h5>
                            <p class="text-muted small">
                                Fecha · Caja · Cajero · Folios · Totales · Diferencia
                            </p>
                            <button type="submit"
                                    formaction="<?= site_url('reportes/cobros/arqueos/excel') ?>"
                                    class="btn btn-warning text-dark">
                                <i class="bi bi-file-earmark-excel me-2"></i>Descargar Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════
                 SECCIÓN 2: REPORTE DE VECINOS POR OBRA
                 ═══════════════════════════════════════════════════ -->
            <h5 class="text-info mb-3 border-bottom pb-2">
                <i class="bi bi-people me-2"></i>Reporte de Vecinos por Obra
            </h5>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Seleccionar Obra</label>
                    <select name="obra_id" id="selectObra" class="form-select" data-placeholder="Buscar obra...">
                        <option value=""></option>
                        <?php foreach ($obras as $o): ?>
                            <option value="<?= esc($o['id_obra']) ?>">
                                <?= esc($o['nombre_obra']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card border-info h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title text-info">
                                <i class="bi bi-people-fill me-2"></i>Vecinos de la Obra
                            </h5>
                            <p class="text-muted small">
                                Vecino · Vialidad · Números · Colonia · ML · Costo · Total · Pagado · Resto · Acera
                            </p>
                            <button type="submit"
                                    formaction="<?= site_url('reportes/cobros/vecinos/excel') ?>"
                                    class="btn btn-info text-white">
                                <i class="bi bi-file-earmark-excel me-2"></i>Descargar Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </form>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    $('#selectObra').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Buscar obra...',
        allowClear: true,
        language: {
            noResults: function() {
                return "No se encontraron obras";
            },
            searching: function() {
                return "Buscando...";
            }
        }
    });
});
</script>
<?= $this->endSection() ?>
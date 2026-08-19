<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">

        <!-- Título de la pantalla -->
        <div class="alert alert-warning text-center mb-3">
            <h5 class="mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>
                Está a punto de cerrar la caja. Complete los datos.
            </h5>
        </div>

        <div class="card card-danger shadow">
            <div class="card-header text-center bg-danger text-white">
                <h4 class="mb-0"><i class="bi bi-lock-fill me-2"></i>Cierre de Caja / Arqueo</h4>
            </div>

            <!-- Formulario POST a caja/cerrar (procesarCierre) -->
            <form action="<?= site_url('caja/cerrar') ?>" method="post">
                <?= csrf_field() ?>

                <div class="card-body">

                    <!-- ═══ CAMPO ESTRELLA: EFECTIVO CONTADO ═══ -->
                    <div class="mb-4 p-3 border border-danger rounded bg-light">
                        <label for="efectivo_contado" class="form-label fw-bold text-danger fs-5">
                            <i class="bi bi-cash-stack me-2"></i>EFECTIVO CONTADO EN CAJA *
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-danger text-white fs-4 fw-bold">$</span>
                            <input type="text"
                                   name="efectivo_contado"
                                   id="efectivo_contado"
                                   class="form-control text-end fw-bold border-danger"
                                   style="font-size: 2rem;"
                                   placeholder="0.00"
                                   required
                                   autofocus
                                   autocomplete="off">
                        </div>
                        <div class="form-text text-danger fw-bold mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Escriba el dinero REAL que hay en caja (incluye fondo inicial). Ej: 1500.50
                        </div>
                    </div>

                    <hr>

                    <!-- Resumen de folios -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Folio Inicial</label>
                            <input type="text" class="form-control text-center" value="<?= esc($totales['folio_inicial'] ?? '—') ?>" disabled>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Folio Final</label>
                            <input type="text" class="form-control text-center" value="<?= esc($totales['folio_final'] ?? '—') ?>" disabled>
                        </div>
                    </div>

                    <!-- Totales cobrados -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Servicios ($)</label>
                            <input type="text" class="form-control text-end fw-bold text-success" 
                                   value="$<?= number_format((float) ($totales['total_servicios'] ?? 0), 2) ?>" disabled>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Aportaciones ($)</label>
                            <input type="text" class="form-control text-end fw-bold text-primary" 
                                   value="$<?= number_format((float) ($totales['total_aportaciones'] ?? 0), 2) ?>" disabled>
                        </div>
                    </div>

                    <div class="alert alert-info mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><strong>Total Sistema:</strong></span>
                            <span class="fs-5 fw-bold">$<?= number_format((float) ($totales['total_sistema'] ?? 0), 2) ?></span>
                        </div>
                    </div>

                    <div class="alert alert-light border mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Fondo de Caja Inicial:</span>
                            <strong>$<?= number_format((float) ($apertura['monto_inicial'] ?? 0), 2) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between fs-5 fw-bold text-primary mt-2 pt-2 border-top">
                            <span>EFECTIVO ESPERADO:</span>
                            <span>$<?= number_format((float) (($totales['total_sistema'] ?? 0) + ($apertura['monto_inicial'] ?? 0)), 2) ?></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="observaciones" class="form-label fw-bold">Observaciones</label>
                        <textarea name="observaciones" id="observaciones" class="form-control" rows="2" placeholder="Diferencias, billetes rotos, etc."></textarea>
                    </div>

                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="<?= site_url('caja/panel') ?>" class="btn btn-secondary btn-lg">
                        <i class="bi bi-arrow-left me-1"></i> Cancelar y Regresar
                    </a>
                    <button type="submit" class="btn btn-danger btn-lg px-5">
                        <i class="bi bi-lock-fill me-1"></i> Cerrar Caja y Generar Arqueo
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
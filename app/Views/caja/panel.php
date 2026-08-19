<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-cash-register me-2"></i>Caja abierta desde: <?= esc($apertura['hora_apertura']) ?></span>
        
        <!-- ESTE BOTÓN ES UN LINK (GET), NO UN FORMULARIO -->
        <a href="<?= site_url('caja/cerrar') ?>" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-lock-fill me-1"></i> Cerrar Caja
        </a>
    </div>
    <div class="card-body">
        <ul class="nav nav-tabs" id="cajaTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-servicios" type="button">
                    Caja (Servicios)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-aportaciones" type="button">
                    Cuentas Cobradas (Aportaciones)
                </button>
            </li>
        </ul>
        <div class="tab-content pt-3">
            
            <!-- PESTAÑA SERVICIOS -->
            <div class="tab-pane fade show active" id="tab-servicios">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="small-box text-bg-primary">
                            <div class="inner">
                                <h3>$<?= number_format((float) ($totales['total_servicios'] ?? 0), 2) ?></h3>
                                <p>Total Cobrado (Servicios)</p>
                            </div>
                            <i class="bi bi-cash-coin small-box-icon"></i>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="small-box text-bg-success">
                            <div class="inner">
                                <h3><?= esc($totales['folios_servicio'] ?? 0) ?></h3>
                                <p>Folios Emitidos</p>
                            </div>
                            <i class="bi bi-receipt small-box-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="text-center py-3">
                    <a href="<?= site_url('servicios/cobro') ?>" class="btn btn-primary btn-lg me-2">
                        <i class="bi bi-plus-circle me-1"></i> Nuevo Cobro
                    </a>
                    <a href="<?= site_url('servicios/cobro/historial') ?>" class="btn btn-outline-info btn-lg me-2">
                        <i class="bi bi-list-ul me-1"></i> Historial Cobros
                    </a>
                    <?php if (puede('cobros.crear')): ?>
                        <a href="<?= site_url('caja/arqueos') ?>" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-journal-check me-1"></i> Arqueos
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- PESTAÑA APORTACIONES -->
            <div class="tab-pane fade" id="tab-aportaciones">
				<div class="row">
					<div class="col-md-4 mb-3">
						<div class="small-box text-bg-warning">
							<div class="inner">
								<h3>$<?= number_format((float) ($totales['total_aportaciones'] ?? 0), 2) ?></h3>
								<p>Total Aportaciones</p>
							</div>
							<i class="bi bi-people small-box-icon"></i>
						</div>
					</div>
				</div>
				<div class="text-center py-3">
					<a href="<?= site_url('aportaciones/cobro') ?>" class="btn btn-warning btn-lg me-2">
						<i class="bi bi-plus-circle me-1"></i> Nueva Aportación
					</a>
					<a href="<?= site_url('aportaciones/cobro/historial') ?>" class="btn btn-outline-dark btn-lg">
						<i class="bi bi-list-ul me-1"></i> Historial Aportaciones
					</a>
				</div>
			</div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
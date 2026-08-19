<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<?php if (!$tieneCaja): ?>

    <!-- Sin caja abierta -->
    <div class="alert alert-warning d-flex align-items-center">
        <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
        <div>
            <h5 class="alert-heading">No tienes caja abierta</h5>
            <p class="mb-2">Para comenzar a cobrar debes aperturar una caja primero.</p>
            <a href="<?= site_url('caja') ?>" class="btn btn-primary">
                <i class="bi bi-cash-coin me-1"></i> Ir a Apertura de Caja
            </a>
        </div>
    </div>

<?php else: ?>

    <!-- ═══ KPIs ═══ -->
    <div class="row">
        <div class="col-lg-3 col-md-6 col-12">
            <div class="small-box text-bg-primary">
                <div class="inner">
                    <h3>$<?= number_format((float)($totales['total_sistema'] ?? 0), 2) ?></h3>
                    <p>Total Cobrado Hoy</p>
                </div>
                <i class="bi bi-cash-stack small-box-icon"></i>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="small-box text-bg-success">
                <div class="inner">
                    <h3><?= ($totales['folios_servicio'] ?? 0) + ($totales['folios_aportacion'] ?? 0) ?></h3>
                    <p>Cobros Realizados</p>
                </div>
                <i class="bi bi-receipt small-box-icon"></i>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="small-box text-bg-info">
                <div class="inner">
                    <h3><?= esc($totales['folio_final'] ?? '—') ?></h3>
                    <p>Folio Actual</p>
                </div>
                <i class="bi bi-tag small-box-icon"></i>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="small-box text-bg-warning">
                <div class="inner">
                    <h3><?= esc($cajaNombre) ?></h3>
                    <p>Caja Activa</p>
                </div>
                <i class="bi bi-shop small-box-icon"></i>
            </div>
        </div>
    </div>

    <!-- ═══ Info del turno + Acciones rápidas ═══ -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0"><i class="bi bi-info-circle me-2"></i>Información de tu Turno</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Caja:</strong> <?= esc($cajaNombre) ?></p>
                    <p class="mb-1"><strong>Apertura:</strong> <?= date('d/m/Y H:i', strtotime($apertura['hora_apertura'])) ?></p>
                    <p class="mb-0"><strong>Folio inicial:</strong> <?= esc($apertura['folio_inicial'] ?? '—') ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center justify-content-center gap-3">
                    <a href="<?= site_url('caja/panel') ?>" class="btn btn-primary btn-lg">
                        <i class="bi bi-cash-coin me-2"></i>Ir a Caja
                    </a>
                    <a href="<?= site_url('caja/cerrar') ?>" class="btn btn-danger btn-lg">
                        <i class="bi bi-lock-fill me-2"></i>Cerrar Caja
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ Últimos 5 cobros ═══ -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0"><i class="bi bi-clock-history me-2"></i>Últimos Cobros</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Folio</th>
                        <th>Tipo</th>
                        <th>Nombre</th>
                        <th class="text-end">Monto</th>
                        <th class="text-end">Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ultimosCobros)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Aún no hay cobros en este turno</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ultimosCobros as $c): ?>
                        <tr>
                            <td><?= esc($c['folio']) ?></td>
                            <td>
                                <span class="badge bg-<?= $c['tipo'] === 'Servicio' ? 'primary' : 'success' ?>">
                                    <?= esc($c['tipo']) ?>
                                </span>
                            </td>
                            <td><?= esc($c['nombre']) ?></td>
                            <td class="text-end">$<?= number_format((float)$c['monto'], 2) ?></td>
                            <td class="text-end"><?= date('H:i', strtotime($c['fecha_cobro'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php endif; ?>

<?= $this->endSection() ?>
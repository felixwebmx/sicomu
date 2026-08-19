<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<style>
    .resumen-card {
        border-left: 4px solid #0d6efd;
    }
    .resumen-pagado {
        border-left-color: #198754;
    }
    .resumen-resto {
        border-left-color: #dc3545;
    }
    /* Select2 para filtros */
    .select2-filtro .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<!-- ─── Resumen de Obra (si se está viendo una obra específica) ─── -->
<?php if (isset($obra) && $obra): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-building me-2"></i><?= esc($obra['nombre_obra']) ?>
                    <span class="badge bg-light text-dark ms-2"><?= esc($obra['nombre_programa']) ?> (<?= esc($obra['anio_programa']) ?>)</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <small class="text-muted">Colonia / Vialidad</small>
                        <p class="mb-0"><strong><?= esc($obra['nombre_colonia']) ?> / <?= esc($obra['nombre_vialidad']) ?></strong></p>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Costo Total</small>
                        <p class="mb-0"><strong>$<?= number_format($obra['costo_total'], 2) ?></strong></p>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Costo por ML</small>
                        <p class="mb-0"><strong>$<?= number_format($obra['costo_x_ml'], 2) ?></strong></p>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Aportación Vecinos</small>
                        <p class="mb-0"><strong>$<?= number_format($obra['monto_vecinos'], 2) ?></strong> (<?= esc($obra['por_vecinos']) ?>%)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ─── Estadísticas ─── -->
<?php if (isset($resumen) && $resumen): ?>
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card resumen-card">
            <div class="card-body text-center">
                <h4 class="mb-0"><?= $resumen['total_vecinos'] ?></h4>
                <small class="text-muted">Vecinos</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card resumen-card">
            <div class="card-body text-center">
                <h4 class="mb-0"><?= number_format($resumen['total_ml'], 2) ?> ml</h4>
                <small class="text-muted">Total ML</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card resumen-card resumen-pagado">
            <div class="card-body text-center">
                <h4 class="mb-0 text-success">$<?= number_format($resumen['total_pagado'], 2) ?></h4>
                <small class="text-muted">Pagado</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card resumen-card resumen-resto">
            <div class="card-body text-center">
                <h4 class="mb-0 text-danger">$<?= number_format($resumen['total_resto'], 2) ?></h4>
                <small class="text-muted">Por cobrar</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card resumen-card">
            <div class="card-body text-center">
                <h4 class="mb-0"><?= $resumen['vecinos_pagados'] ?> / <?= $resumen['vecinos_deudores'] ?></h4>
                <small class="text-muted">Pagados / Deudores</small>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>

<!-- ─── Tabla de Vecinos ─── -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
            <i class="bi bi-people me-2"></i><?= esc($titulo) ?>
        </h3>
        <div>
            <?php if (isset($idObra) && $idObra): ?>
                <a href="<?= site_url('obras') ?>" class="btn btn-outline-secondary btn-sm me-2">
                    <i class="bi bi-arrow-left"></i> Volver a Obras
                </a>
            <?php endif; ?>
            <?php if (puede('vecinos.administrar')): ?>
                <a href="<?= site_url('vecinos/nuevo' . (isset($idObra) && $idObra ? '?obra=' . $idObra : '')) ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Nuevo Vecino
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card-body">
        <!-- Filtro por obra con Select2 -->
        <?php if (!isset($obra)): ?>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">
                    <i class="bi bi-filter me-1"></i>Filtrar por obra:
                </label>
                <select id="filtroObra" class="form-select select2-filtro">
                    <option value="">-- Todas las obras --</option>
                    <?php foreach ($obras as $o): ?>
                        <option value="<?= $o['id_obra'] ?>" <?= (isset($idObra) && $idObra == $o['id_obra']) ? 'selected' : '' ?>>
                            <?= esc($o['nombre_obra']) ?> (<?= esc($o['anio_programa'] ?? 'N/A') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <?php endif; ?>

        <table id="tablaVecinos" class="table table-striped table-hover w-100">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Vecino</th>
                    <th>Dirección</th>
                    <th>Obra</th>
                    <th>Acera</th>
                    <th>ML</th>
                    <th>Costo/ML</th>
                    <th>Aportación</th>
                    <th>Pagado</th>
                    <th>Resto</th>
                    <th>Último Pago</th>
                    <th>Estatus</th>
                    <th class="text-end no-export">Acciones</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select2 para filtro de obra
    $('#filtroObra').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Todas las obras --',
        allowClear: true,
        width: '100%'
    }).on('change', function() {
        const idObra = $(this).val();
        if (idObra) {
            window.location.href = '<?= site_url('vecinos?obra=') ?>' + idObra;
        } else {
            window.location.href = '<?= site_url('vecinos') ?>';
        }
    });

    // DataTable con Server-Side Processing usando la función global initDataTable
    const tabla = initDataTable('#tablaVecinos', {
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= site_url('vecinos/ajax/listado' . (isset($idObra) && $idObra ? '?obra=' . $idObra : '')) ?>',
            type: 'GET'
        },
        order: [[0, 'asc']],
        columns: [
            { data: 'id_vecino', className: 'text-center' },
            { 
                data: 'nombre_vecino',
                render: function(data) {
                    return '<strong>' + escapeHtml(data) + '</strong>';
                }
            },
            { 
                data: null,
                render: function(data) {
                    return '<small>' + 
                        escapeHtml(data.nombre_colonia) + '<br>' +
                        escapeHtml(data.nombre_vialidad) + ' #' + 
                        escapeHtml(data.no_exterior) + 
                        (data.no_bis ? ' Bis ' + escapeHtml(data.no_bis) : '') +
                        (data.no_interior ? ' Int. ' + escapeHtml(data.no_interior) : '') +
                        '</small>';
                }
            },
            { 
                data: 'nombre_obra',
                render: function(data) {
                    return '<span class="badge text-bg-primary">' + escapeHtml(data) + '</span>';
                }
            },
            { 
                data: 'acera',
                className: 'text-center',
                render: function(data) {
                    if (data === 'D') {
                        return '<span class="badge text-bg-info" title="Derecha"><i class="bi bi-arrow-right"></i> D</span>';
                    }
                    return '<span class="badge text-bg-secondary" title="Izquierda"><i class="bi bi-arrow-left"></i> I</span>';
                }
            },
            { 
                data: 'ml', 
                className: 'text-end',
                render: function(data) {
                    return parseFloat(data).toLocaleString('es-MX', {minimumFractionDigits: 2}) + ' ml';
                }
            },
            { 
                data: 'costo_ml', 
                className: 'text-end',
                render: function(data) {
                    return '$' + parseFloat(data).toLocaleString('es-MX', {minimumFractionDigits: 2});
                }
            },
            { 
                data: 'total_aportacion', 
                className: 'text-end',
                render: function(data) {
                    return '<strong>$' + parseFloat(data).toLocaleString('es-MX', {minimumFractionDigits: 2}) + '</strong>';
                }
            },
            { 
                data: 'pagado', 
                className: 'text-end text-success',
                render: function(data) {
                    return '$' + parseFloat(data).toLocaleString('es-MX', {minimumFractionDigits: 2});
                }
            },
            { 
                data: 'resto', 
                className: 'text-end text-danger',
                render: function(data) {
                    return '$' + parseFloat(data).toLocaleString('es-MX', {minimumFractionDigits: 2});
                }
            },
            { 
                data: null,
                render: function(data) {
                    if (data.fecha_ultimo_pago) {
                        return '<small>' + formatDate(data.fecha_ultimo_pago) + '<br>$' + 
                            parseFloat(data.ultimo_pago).toLocaleString('es-MX', {minimumFractionDigits: 2}) + '</small>';
                    }
                    return '<span class="text-muted">-</span>';
                }
            },
            { 
                data: null,
                className: 'text-center',
                render: function(data) {
                    let html = '';
                    if (data.estatus_vecino == 1) {
                        html += '<span class="badge text-bg-success">Activo</span>';
                    } else {
                        html += '<span class="badge text-bg-danger">Inactivo</span>';
                    }
                    if (parseFloat(data.resto) <= 0) {
                        html += ' <span class="badge text-bg-success"><i class="bi bi-check-circle"></i></span>';
                    }
                    return html;
                }
            },
            { 
                data: null,
                className: 'text-end no-export',
                orderable: false,
                searchable: false,
                render: function(data) {
                    let html = '<div class="btn-group btn-group-sm">';
                    
                    <?php if (puede('cobros.crear')): ?>
                    html += '<a href="<?= site_url('cobros/nuevo?vecino=') ?>' + data.id_vecino + '" ' +
                            'class="btn btn-outline-success" title="Registrar Pago">' +
                            '<i class="bi bi-cash-coin"></i></a>';
                    <?php endif; ?>
                    
                    <?php if (puede('vecinos.administrar')): ?>
                    html += '<a href="<?= site_url('vecinos/editar/') ?>' + data.id_vecino + '" ' +
                            'class="btn btn-outline-primary" title="Editar">' +
                            '<i class="bi bi-pencil"></i></a>';
                    html += '<button type="button" class="btn btn-outline-danger" title="Eliminar" ' +
                            'onclick="enviarPost(\'<?= site_url('vecinos/eliminar/') ?>' + data.id_vecino + '\', \'¿Eliminar al vecino?\')">' +
                            '<i class="bi bi-trash"></i></button>';
                    <?php endif; ?>
                    
                    html += '</div>';
                    return html;
                }
            }
        ],
        createdRow: function(row, data) {
            if (parseFloat(data.resto) <= 0) {
                $(row).addClass('table-success');
            } else if (parseFloat(data.resto) >= parseFloat(data.total_aportacion)) {
                $(row).addClass('table-danger');
            }
        }
    });

    // Helpers
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        return d.toLocaleDateString('es-MX');
    }
});
</script>
<?= $this->endSection() ?>